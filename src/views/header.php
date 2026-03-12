<header class="header">
    <div class="container">
        <nav class="nav">
            <a href="index.php" class="logo">Wishlist</a>
            <ul class="nav-menu">
                <li><a href="?action=explore">Обзор</a></li>
                <li><a href="?action=gift_ideas">Идеи подарков</a></li>
                <?php if ($user): ?>
                    <?php
                    // Получаем количество непрочитанных уведомлений
                    require_once __DIR__ . '/../models/Notification.php';
                    $notificationModel = new Notification();
                    $unreadCount = $notificationModel->getUnreadCount($user['id']);
                    ?>
                    <li><a href="?action=my_wishlists">Мои вишлисты</a></li>
                    <li><a href="?action=friends_wishlists">Вишлисты друзей</a></li>
                    <li>
                        <a href="?action=friends">Друзья
                            <?php if ($unreadCount > 0): ?>
                                <span class="notification-badge"><?php echo $unreadCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li><a href="?action=search_users">Поиск</a></li>
                    <li><a href="?action=create_wishlist">Создать</a></li>
                    <li class="user-menu">
                        <span><?php echo htmlspecialchars($user['username']); ?></span>
                        <a href="?action=logout" class="btn btn-small">Выйти</a>
                    </li>
                <?php else: ?>
                    <li><a href="?action=login" class="btn btn-small">Войти</a></li>
                    <li><a href="?action=register" class="btn btn-small btn-primary">Регистрация</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
