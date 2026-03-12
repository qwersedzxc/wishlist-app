<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вишлист - Wishlist</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php 
    include 'header.php';
    
    require_once __DIR__ . '/../helpers/event_types.php';
    
    $wishlistModel = new Wishlist();
    $itemModel = new WishlistItem();
    
    $wishlist_id = $_GET['id'] ?? 0;
    $share_token = $_GET['token'] ?? null;
    
    // Проверка доступа по токену
    if ($share_token) {
        $wishlist = $wishlistModel->getByShareToken($share_token);
    } else {
        $wishlist = $wishlistModel->getById($wishlist_id);
    }
    
    if (!$wishlist) {
        echo '<div class="container"><p>Вишлист не найден</p></div>';
        include 'footer.php';
        exit;
    }
    
    // Проверка доступа
    $hasAccess = false;
    $isOwner = $user && $user['id'] == $wishlist['user_id'];
    
    if ($isOwner) {
        $hasAccess = true;
    } elseif ($wishlist['privacy'] == 'public') {
        $hasAccess = true;
    } elseif ($wishlist['privacy'] == 'link' && $share_token) {
        $hasAccess = true;
    } elseif ($wishlist['privacy'] == 'friends' && $user) {
        require_once __DIR__ . '/../models/Friend.php';
        $friendModel = new Friend();
        $areFriends = $friendModel->areFriends($wishlist['user_id'], $user['id']);
        
        // Отладочная информация (временно)
        if (isset($_GET['debug'])) {
            echo "<div style='background: yellow; padding: 10px; margin: 10px;'>";
            echo "Debug info:<br>";
            echo "Wishlist owner ID: " . $wishlist['user_id'] . "<br>";
            echo "Current user ID: " . $user['id'] . "<br>";
            echo "Privacy: " . $wishlist['privacy'] . "<br>";
            echo "Are friends: " . ($areFriends ? 'YES' : 'NO') . "<br>";
            echo "</div>";
        }
        
        $hasAccess = $areFriends;
    }
    
    if (!$hasAccess) {
        // Показываем дополнительную информацию для отладки
        $debugInfo = '';
        if (isset($_GET['debug']) && $user) {
            $debugInfo = "<p>Debug: User ID: {$user['id']}, Wishlist Owner: {$wishlist['user_id']}, Privacy: {$wishlist['privacy']}</p>";
        }
        
        echo '<div class="container"><div class="page-header"><h1>Доступ запрещен</h1><p>Этот вишлист доступен только друзьям владельца</p>' . $debugInfo . '</div></div>';
        include 'footer.php';
        exit;
    }
    
    $items = $itemModel->getByWishlistId($wishlist['id']);
    ?>
    
    <main class="container">
        <div class="wishlist-header">
            <?php if (!empty($wishlist['cover_image'])): ?>
                <img src="<?php echo htmlspecialchars($wishlist['cover_image']); ?>" alt="Cover" class="wishlist-cover" style="width: 100%; max-height: 400px; object-fit: cover; margin-bottom: 1rem;">
            <?php endif; ?>
            <h1><?php echo htmlspecialchars($wishlist['title']); ?></h1>
            <p class="wishlist-author">Автор: <?php echo htmlspecialchars($wishlist['username']); ?></p>
            <p class="wishlist-description"><?php echo htmlspecialchars($wishlist['description']); ?></p>
            <div class="wishlist-meta">
                <span>Событие: <?php echo htmlspecialchars(translateEventType($wishlist['event_type'])); ?></span>
                <?php if ($wishlist['event_date']): ?>
                    <span>Дата: <?php echo date('d.m.Y', strtotime($wishlist['event_date'])); ?></span>
                <?php endif; ?>
                <span>Приватность: 
                    <?php 
                        $privacyLabels = [
                            'public' => 'Публичный',
                            'friends' => 'Только друзья',
                            'link' => 'По ссылке'
                        ];
                        echo $privacyLabels[$wishlist['privacy']] ?? 'Публичный';
                    ?>
                </span>
            </div>
            
            <?php if ($isOwner && $wishlist['privacy'] == 'link' && $wishlist['share_token']): ?>
                <div class="share-link-section">
                    <h3>Ссылка для доступа:</h3>
                    <div class="share-link-container">
                        <input type="text" readonly value="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/index.php?action=view_wishlist&token=' . $wishlist['share_token']; ?>" class="share-link-input" id="shareLink" onclick="this.select()">
                        <button class="btn btn-small" onclick="copyShareLink()">Копировать</button>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($isOwner || ($user && $wishlist['privacy'] != 'link')): ?>
                <div class="share-section">
                    <h3>Поделиться вишлистом:</h3>
                    <div class="share-buttons">
                        <button class="btn btn-small" onclick="shareViaLink()">Скопировать ссылку</button>
                        <?php if ($isOwner): ?>
                            <a href="index.php?action=share_wishlist&id=<?php echo $wishlist['id']; ?>" class="btn btn-small">Отправить друзьям</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($isOwner): ?>
        <div class="add-item-section">
            <h2>Добавить подарок</h2>
            <form id="addItemForm" class="form">
                <input type="hidden" name="wishlist_id" value="<?php echo $wishlist_id; ?>">
                <div class="form-row">
                    <div class="form-group">
                        <input type="text" name="title" placeholder="Название подарка" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="url" placeholder="Ссылка (необязательно)">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <input type="number" name="price" placeholder="Цена" step="0.01">
                    </div>
                    <div class="form-group">
                        <select name="priority">
                            <option value="low">Низкий приоритет</option>
                            <option value="medium" selected>Средний приоритет</option>
                            <option value="high">Высокий приоритет</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <textarea name="description" placeholder="Описание (необязательно)" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Добавить</button>
            </form>
        </div>
        <?php endif; ?>

        <div class="items-section">
            <h2>Подарки (<?php echo count($items); ?>)</h2>
            <div class="items-grid">
                <?php foreach ($items as $item): ?>
                    <div class="item-card <?php echo $item['is_reserved'] ? 'reserved' : ''; ?>">
                        <div class="item-priority priority-<?php echo $item['priority']; ?>">
                            <span class="priority-label"><?php echo translatePriority($item['priority']); ?></span>
                        </div>
                        <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                        <?php if ($item['description']): ?>
                            <p><?php echo htmlspecialchars($item['description']); ?></p>
                        <?php endif; ?>
                        <?php if ($item['price']): ?>
                            <p class="item-price"><?php echo number_format($item['price'], 2); ?> ₽</p>
                        <?php endif; ?>
                        <?php if ($item['url']): ?>
                            <a href="<?php echo htmlspecialchars($item['url']); ?>" target="_blank" class="item-link">Посмотреть</a>
                        <?php endif; ?>
                        
                        <?php if ($item['is_reserved']): ?>
                            <div class="item-status">
                                <?php if ($isOwner || ($user && $user['id'] == $item['reserved_by'])): ?>
                                    Зарезервировано: <?php echo htmlspecialchars($item['reserved_by_username']); ?>
                                <?php else: ?>
                                    Зарезервировано
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($user && !$isOwner): ?>
                            <?php if ($item['is_reserved'] && $user['id'] == $item['reserved_by']): ?>
                                <button class="btn btn-small" onclick="unreserveItem(<?php echo $item['id']; ?>)">Отменить резерв</button>
                            <?php elseif (!$item['is_reserved']): ?>
                                <button class="btn btn-small btn-primary" onclick="reserveItem(<?php echo $item['id']; ?>)">Зарезервировать</button>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php if ($isOwner): ?>
                            <button class="btn btn-small btn-danger" onclick="deleteItem(<?php echo $item['id']; ?>)">Удалить</button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    <script>
        <?php if ($isOwner): ?>
        document.getElementById('addItemForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('action', 'add_item');
            
            const response = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.success) {
                location.reload();
            } else {
                alert(result.error || 'Ошибка добавления подарка');
            }
        });
        <?php endif; ?>

        async function reserveItem(itemId) {
            const formData = new FormData();
            formData.append('action', 'reserve_item');
            formData.append('item_id', itemId);
            
            const response = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.success) {
                location.reload();
            } else {
                alert(result.error || 'Ошибка резервирования');
            }
        }

        async function unreserveItem(itemId) {
            const formData = new FormData();
            formData.append('action', 'unreserve_item');
            formData.append('item_id', itemId);
            
            const response = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.success) {
                location.reload();
            } else {
                alert(result.error || 'Ошибка отмены резервирования');
            }
        }

        async function deleteItem(itemId) {
            if (!confirm('Удалить этот подарок?')) return;
            
            const formData = new FormData();
            formData.append('action', 'delete_item');
            formData.append('item_id', itemId);
            
            const response = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.success) {
                location.reload();
            } else {
                alert(result.error || 'Ошибка удаления');
            }
        }
        
        function copyShareLink() {
            const input = document.querySelector('.share-link-input');
            input.select();
            document.execCommand('copy');
            alert('Ссылка скопирована!');
        }
        
        function shareViaLink() {
            const wishlistId = <?php echo $wishlist['id']; ?>;
            const url = window.location.origin + '/index.php?action=view_wishlist&id=' + wishlistId;
            
            // Копируем в буфер обмена
            const tempInput = document.createElement('input');
            tempInput.value = url;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            
            alert('Ссылка скопирована в буфер обмена!\n\n' + url);
        }
    </script>
</body>
</html>
