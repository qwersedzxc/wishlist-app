<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль пользователя - Wishlist</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php 
    include 'header.php';
    require_once __DIR__ . '/../helpers/event_types.php';
    
    $profile_user_id = $_GET['id'] ?? 0;
    
    if (!$profile_user_id) {
        header('Location: index.php');
        exit;
    }
    
    require_once __DIR__ . '/../models/Friend.php';
    
    $userModel = new User();
    $wishlistModel = new Wishlist();
    $friendModel = new Friend();
    
    $profileUser = $userModel->getById($profile_user_id);
    
    if (!$profileUser) {
        echo '<div class="container"><p>Пользователь не найден</p></div>';
        include 'footer.php';
        exit;
    }
    
    $isOwnProfile = $user && $user['id'] == $profile_user_id;
    $friendshipStatus = null;
    $areFriends = false;
    
    if ($user && !$isOwnProfile) {
        $friendshipStatus = $friendModel->getFriendshipStatus($user['id'], $profile_user_id);
        $areFriends = $friendModel->areFriends($user['id'], $profile_user_id);
    }
    
    // Получаем вишлисты с учетом прав доступа
    $wishlists = $wishlistModel->getAccessibleByUserId($profile_user_id, $user['id'] ?? null);
    ?>
    
    <main class="container">
        <div class="profile-header">
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($profileUser['username']); ?></h1>
                <?php if ($profileUser['full_name']): ?>
                    <p class="profile-fullname"><?php echo htmlspecialchars($profileUser['full_name']); ?></p>
                <?php endif; ?>
                <p class="profile-member-since">На сайте с <?php echo date('d.m.Y', strtotime($profileUser['created_at'])); ?></p>
            </div>
            
            <?php if ($user && !$isOwnProfile): ?>
                <div class="profile-actions">
                    <?php if (!$friendshipStatus): ?>
                        <button class="btn btn-primary" onclick="sendRequest(<?php echo $profile_user_id; ?>)">Добавить в друзья</button>
                    <?php elseif ($friendshipStatus['status'] == 'pending'): ?>
                        <?php if ($friendshipStatus['sender_id'] == $user['id']): ?>
                            <span class="friend-status">Запрос отправлен</span>
                        <?php else: ?>
                            <button class="btn btn-primary" onclick="acceptRequest(<?php echo $friendshipStatus['id']; ?>)">Принять запрос</button>
                            <button class="btn btn-secondary" onclick="rejectRequest(<?php echo $friendshipStatus['id']; ?>)">Отклонить</button>
                        <?php endif; ?>
                    <?php elseif ($friendshipStatus['status'] == 'accepted'): ?>
                        <span class="friend-status">В друзьях</span>
                        <button class="btn btn-danger btn-small" onclick="removeFriend(<?php echo $profile_user_id; ?>)">Удалить из друзей</button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <section class="profile-wishlists">
            <h2>Вишлисты (<?php echo count($wishlists); ?>)</h2>
            
            <?php if (empty($wishlists)): ?>
                <div class="empty-state">
                    <?php if ($isOwnProfile): ?>
                        <p>У вас пока нет вишлистов</p>
                        <a href="?action=create_wishlist" class="btn btn-primary">Создать первый вишлист</a>
                    <?php elseif ($areFriends): ?>
                        <p>У этого пользователя пока нет доступных вишлистов</p>
                    <?php else: ?>
                        <p>У этого пользователя нет публичных вишлистов</p>
                        <p>Добавьте пользователя в друзья, чтобы видеть больше вишлистов</p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="wishlists-grid">
                    <?php foreach ($wishlists as $wishlist): ?>
                        <div class="wishlist-card">
                            <?php if (!empty($wishlist['cover_image'])): ?>
                                <img src="<?php echo htmlspecialchars($wishlist['cover_image']); ?>" alt="Cover" class="wishlist-cover">
                            <?php endif; ?>
                            <h3><?php echo htmlspecialchars($wishlist['title']); ?></h3>
                            <p class="wishlist-event"><?php echo htmlspecialchars(translateEventType($wishlist['event_type'])); ?></p>
                            <?php if ($wishlist['event_date']): ?>
                                <p class="wishlist-date"><?php echo date('d.m.Y', strtotime($wishlist['event_date'])); ?></p>
                            <?php endif; ?>
                            <p class="wishlist-privacy">
                                <?php 
                                    $privacyLabels = [
                                        'public' => 'Публичный',
                                        'friends' => 'Только друзья',
                                        'link' => 'По ссылке'
                                    ];
                                    echo $privacyLabels[$wishlist['privacy']] ?? 'Публичный';
                                ?>
                            </p>
                            <a href="?action=view_wishlist&id=<?php echo $wishlist['id']; ?>" class="btn btn-small">Открыть</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
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
    </script>
</body>
</html>
