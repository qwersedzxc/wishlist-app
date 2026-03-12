<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Друзья - Wishlist</title>
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
    require_once __DIR__ . '/../models/Notification.php';
    
    $friendModel = new Friend();
    $notificationModel = new Notification();
    
    $friends = $friendModel->getFriends($user['id']);
    $incomingRequests = $friendModel->getIncomingRequests($user['id']);
    $outgoingRequests = $friendModel->getOutgoingRequests($user['id']);
    
    // Получаем уведомления о вишлистах
    $wishlistNotifications = $notificationModel->getByUserId($user['id'], 10);
    ?>
    
    <main class="container">
        <div class="page-header">
            <h1>Друзья</h1>
        </div>

        <?php if (!empty($wishlistNotifications)): ?>
        <section class="friends-section">
            <h2>Полученные вишлисты (<?php echo count($wishlistNotifications); ?>)</h2>
            <div class="notifications-grid" style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                <?php foreach ($wishlistNotifications as $notification): ?>
                    <div class="notification-card" style="background: <?php echo $notification['is_read'] ? '#ffffff' : '#f5f5f5'; ?>; padding: 1.5rem; border: 2px solid #000000; position: relative;">
                        <?php if (!$notification['is_read']): ?>
                            <div style="position: absolute; top: 10px; right: 10px; background: #000000; color: #ffffff; padding: 0.25rem 0.5rem; font-size: 0.75rem;">Новое</div>
                        <?php endif; ?>
                        
                        <h3 style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($notification['title']); ?></h3>
                        <p style="color: #666666; margin-bottom: 1rem;"><?php echo nl2br(htmlspecialchars($notification['message'])); ?></p>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                            <small style="color: #999999;">
                                <?php echo date('d.m.Y H:i', strtotime($notification['created_at'])); ?>
                                <?php if ($notification['from_username']): ?>
                                    от <?php echo htmlspecialchars($notification['from_username']); ?>
                                <?php endif; ?>
                            </small>
                            
                            <div style="display: flex; gap: 0.5rem;">
                                <?php if ($notification['link']): ?>
                                    <a href="<?php echo htmlspecialchars($notification['link']); ?>" class="btn btn-small btn-primary">Посмотреть</a>
                                <?php endif; ?>
                                
                                <?php if (!$notification['is_read']): ?>
                                    <button class="btn btn-small btn-secondary" onclick="markAsRead(<?php echo $notification['id']; ?>)">Отметить прочитанным</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php 
            $unreadCount = 0;
            foreach ($wishlistNotifications as $notification) {
                if (!$notification['is_read']) $unreadCount++;
            }
            ?>
            
            <?php if ($unreadCount > 0): ?>
                <div style="margin-top: 1rem; text-align: center;">
                    <button class="btn btn-secondary" onclick="markAllAsRead()">Отметить все как прочитанные (<?php echo $unreadCount; ?>)</button>
                </div>
            <?php endif; ?>
        </section>
        <?php endif; ?>

        <?php if (!empty($incomingRequests)): ?>
        <section class="friends-section">
            <h2>Входящие запросы (<?php echo count($incomingRequests); ?>)</h2>
            <div class="friends-grid">
                <?php foreach ($incomingRequests as $request): ?>
                    <div class="friend-card">
                        <h3><?php echo htmlspecialchars($request['username']); ?></h3>
                        <?php if ($request['full_name']): ?>
                            <p><?php echo htmlspecialchars($request['full_name']); ?></p>
                        <?php endif; ?>
                        <div class="friend-actions">
                            <button class="btn btn-primary btn-small" onclick="acceptRequest(<?php echo $request['id']; ?>)">Принять</button>
                            <button class="btn btn-secondary btn-small" onclick="rejectRequest(<?php echo $request['id']; ?>)">Отклонить</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <?php if (!empty($outgoingRequests)): ?>
        <section class="friends-section">
            <h2>Исходящие запросы (<?php echo count($outgoingRequests); ?>)</h2>
            <div class="friends-grid">
                <?php foreach ($outgoingRequests as $request): ?>
                    <div class="friend-card">
                        <h3><?php echo htmlspecialchars($request['username']); ?></h3>
                        <?php if ($request['full_name']): ?>
                            <p><?php echo htmlspecialchars($request['full_name']); ?></p>
                        <?php endif; ?>
                        <p class="friend-status">Ожидает ответа</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <section class="friends-section">
            <h2>Мои друзья (<?php echo count($friends); ?>)</h2>
            <?php if (empty($friends)): ?>
                <div class="empty-state">
                    <p>У вас пока нет друзей</p>
                    <a href="?action=search_users" class="btn btn-primary">Найти друзей</a>
                </div>
            <?php else: ?>
                <div class="friends-grid">
                    <?php foreach ($friends as $friend): ?>
                        <div class="friend-card">
                            <h3><?php echo htmlspecialchars($friend['username']); ?></h3>
                            <?php if ($friend['full_name']): ?>
                                <p><?php echo htmlspecialchars($friend['full_name']); ?></p>
                            <?php endif; ?>
                            <div class="friend-actions">
                                <a href="?action=user_profile&id=<?php echo $friend['id']; ?>" class="btn btn-small">Профиль</a>
                                <button class="btn btn-danger btn-small" onclick="removeFriend(<?php echo $friend['id']; ?>)">Удалить</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <?php include 'footer.php'; ?>
    <script>
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

        async function rejectRequest(requestId) {
            const formData = new FormData();
            formData.append('action', 'reject_friend_request');
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

        async function removeFriend(friendId) {
            if (!confirm('Удалить из друзей?')) return;
            
            const formData = new FormData();
            formData.append('action', 'remove_friend');
            formData.append('friend_id', friendId);
            
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

        async function markAsRead(notificationId) {
            const formData = new FormData();
            formData.append('action', 'mark_notification_read');
            formData.append('notification_id', notificationId);
            
            const response = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.success) {
                location.reload();
            } else {
                alert(result.error || 'Ошибка отметки уведомления');
            }
        }

        async function markAllAsRead() {
            const formData = new FormData();
            formData.append('action', 'mark_all_notifications_read');
            
            const response = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.success) {
                location.reload();
            } else {
                alert(result.error || 'Ошибка отметки уведомлений');
            }
        }
    </script>
</body>
</html>
