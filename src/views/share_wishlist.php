<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поделиться вишлистом</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php
    if (!isset($_SESSION['user_id'])) {
        echo '<div class="container"><p>Требуется авторизация</p></div>';
        exit;
    }
    
    require_once __DIR__ . '/../models/Wishlist.php';
    
    $wishlist_id = $_GET['id'] ?? 0;
    $wishlistModel = new Wishlist();
    $wishlist = $wishlistModel->getById($wishlist_id);
    
    if (!$wishlist || $wishlist['user_id'] != $_SESSION['user_id']) {
        echo '<div class="container"><p>Вишлист не найден</p></div>';
        exit;
    }
    ?>
    
    <div class="container" style="padding: 2rem;">
        <div class="form-container" style="max-width: 600px;">
            <h1>Поделиться вишлистом с друзьями</h1>
            <p style="margin-bottom: 1.5rem; color: #666;">Вишлист: <strong><?php echo htmlspecialchars($wishlist['title']); ?></strong></p>
            
            <form id="shareForm">
                <div class="form-group">
                    <label>Выберите друзей:</label>
                    <div id="friendsList" class="friends-list" style="max-height: 300px; overflow-y: auto; border: 2px solid #000000; padding: 1rem; background: #ffffff;">
                        <p>Загрузка...</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="shareMessage">Сообщение (необязательно):</label>
                    <textarea id="shareMessage" rows="4" placeholder="Добавьте сообщение к вишлисту..." style="width: 100%; padding: 0.75rem; border: 2px solid #000000; font-family: inherit; resize: vertical;"></textarea>
                </div>
                
                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <a href="index.php?action=view_wishlist&id=<?php echo $wishlist_id; ?>" class="btn btn-secondary">Отмена</a>
                    <button type="submit" class="btn btn-primary">Отправить</button>
                </div>
            </form>
            
            <div id="resultMessage" style="margin-top: 1rem; padding: 1rem; border: 2px solid #000000; display: none;"></div>
        </div>
    </div>

    <script>
        const wishlistId = <?php echo $wishlist_id; ?>;
        
        // Загружаем список друзей при загрузке страницы
        window.addEventListener('DOMContentLoaded', loadFriendsList);
        
        async function loadFriendsList() {
            const container = document.getElementById('friendsList');
            container.innerHTML = '<p>Загрузка...</p>';
            
            try {
                const response = await fetch('api.php?action=get_friends');
                const result = await response.json();
                
                if (result.success && result.friends) {
                    container.innerHTML = '';
                    
                    if (result.friends.length === 0) {
                        container.innerHTML = '<p style="text-align: center; color: #666666; padding: 2rem;">У вас пока нет друзей</p>';
                        return;
                    }
                    
                    result.friends.forEach(friend => {
                        const div = document.createElement('div');
                        div.style.cssText = 'padding: 0.75rem; border-bottom: 1px solid #cccccc; transition: background 0.2s;';
                        div.onmouseover = function() { this.style.background = '#f5f5f5'; };
                        div.onmouseout = function() { this.style.background = ''; };
                        
                        div.innerHTML = `
                            <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; margin: 0;">
                                <input type="checkbox" name="friend_ids[]" value="${friend.id}" style="width: 20px; height: 20px; margin: 0; cursor: pointer;">
                                <div>
                                    <span style="font-weight: 500;">${friend.username}</span>
                                    ${friend.full_name ? `<small style="display: block; color: #666666; font-size: 0.85rem;">${friend.full_name}</small>` : ''}
                                </div>
                            </label>
                        `;
                        container.appendChild(div);
                    });
                } else {
                    container.innerHTML = '<p style="text-align: center; color: #666666; padding: 2rem;">Ошибка загрузки друзей</p>';
                }
            } catch (error) {
                console.error('Error loading friends:', error);
                container.innerHTML = '<p style="text-align: center; color: #666666; padding: 2rem;">Ошибка загрузки друзей</p>';
            }
        }
        
        document.getElementById('shareForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const checkboxes = document.querySelectorAll('input[name="friend_ids[]"]:checked');
            const friendIds = Array.from(checkboxes).map(cb => cb.value);
            
            if (friendIds.length === 0) {
                showMessage('Выберите хотя бы одного друга', 'error');
                return;
            }
            
            const message = document.getElementById('shareMessage').value;
            
            const formData = new FormData();
            formData.append('action', 'share_wishlist');
            formData.append('wishlist_id', wishlistId);
            formData.append('friend_ids', JSON.stringify(friendIds));
            formData.append('message', message);
            
            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    showMessage('Вишлист успешно отправлен друзьям!', 'success');
                    setTimeout(() => {
                        window.location.href = 'index.php?action=view_wishlist&id=' + wishlistId;
                    }, 2000);
                } else {
                    showMessage(result.error || 'Ошибка отправки', 'error');
                }
            } catch (error) {
                showMessage('Ошибка соединения с сервером', 'error');
            }
        });
        
        function showMessage(text, type) {
            const messageDiv = document.getElementById('resultMessage');
            messageDiv.textContent = text;
            messageDiv.style.display = 'block';
            messageDiv.style.background = type === 'success' ? '#f5f5f5' : '#ffffff';
            messageDiv.style.color = type === 'success' ? '#000000' : '#cc0000';
        }
    </script>
</body>
</html>
