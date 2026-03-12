<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск пользователей - Wishlist</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php 
    include 'header.php';
    
    if (!$user) {
        header('Location: index.php?action=login');
        exit;
    }
    
    require_once __DIR__ . '/../models/Friend.php';
    
    $userModel = new User();
    $friendModel = new Friend();
    $searchResults = [];
    
    if (isset($_GET['q']) && !empty($_GET['q'])) {
        $searchResults = $userModel->search($_GET['q']);
    }
    ?>
    
    <main class="container">
        <div class="page-header">
            <h1>Поиск пользователей</h1>
        </div>

        <div class="search-section">
            <form method="GET" action="" class="search-form">
                <input type="hidden" name="action" value="search_users">
                <div class="form-group">
                    <input type="text" name="q" placeholder="Введите имя пользователя..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" required>
                    <button type="submit" class="btn btn-primary">Найти</button>
                </div>
            </form>
        </div>

        <?php if (!empty($searchResults)): ?>
        <div class="search-results">
            <h2>Результаты поиска</h2>
            <div class="friends-grid">
                <?php foreach ($searchResults as $foundUser): ?>
                    <?php if ($foundUser['id'] == $user['id']) continue; ?>
                    <?php 
                        $friendshipStatus = $friendModel->getFriendshipStatus($user['id'], $foundUser['id']);
                    ?>
                    <div class="friend-card">
                        <h3><?php echo htmlspecialchars($foundUser['username']); ?></h3>
                        <?php if ($foundUser['full_name']): ?>
                            <p><?php echo htmlspecialchars($foundUser['full_name']); ?></p>
                        <?php endif; ?>
                        <div class="friend-actions">
                            <?php if (!$friendshipStatus): ?>
                                <button class="btn btn-primary btn-small" onclick="sendRequest(<?php echo $foundUser['id']; ?>)">Добавить в друзья</button>
                            <?php elseif ($friendshipStatus['status'] == 'pending'): ?>
                                <?php if ($friendshipStatus['sender_id'] == $user['id']): ?>
                                    <span class="friend-status">Запрос отправлен</span>
                                <?php else: ?>
                                    <button class="btn btn-primary btn-small" onclick="acceptRequest(<?php echo $friendshipStatus['id']; ?>)">Принять запрос</button>
                                <?php endif; ?>
                            <?php elseif ($friendshipStatus['status'] == 'accepted'): ?>
                                <span class="friend-status">Уже в друзьях</span>
                                <a href="?action=friends" class="btn btn-small">К списку друзей</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php elseif (isset($_GET['q'])): ?>
            <div class="empty-state">
                <p>Пользователи не найдены</p>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
    <script>
        async function sendRequest(userId) {
            const formData = new FormData();
            formData.append('action', 'send_friend_request');
            formData.append('user_id', userId);
            
            const response = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.success) {
                location.reload();
            } else {
                alert(result.error || 'Ошибка отправки запроса');
            }
        }

        async function acceptRequest(requestId) {
            const formData = new FormData();
            formData.append('action', 'accept_friend_request');
            formData.append('request_id', requestId);
            
            const response = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.success) {
                location.reload();
            } else {
                alert(result.error || 'Ошибка');
            }
        }
    </script>
</body>
</html>
