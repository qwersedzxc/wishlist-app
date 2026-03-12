<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вишлисты друзей - Wishlist</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php 
    include 'header.php';
    require_once __DIR__ . '/../helpers/event_types.php';
    
    if (!$user) {
        header('Location: index.php?action=login');
        exit;
    }
    
    $wishlistModel = new Wishlist();
    $wishlists = $wishlistModel->getFriendsWishlists($user['id'], 50);
    ?>
    
    <main class="container">
        <div class="page-header">
            <h1>Вишлисты друзей</h1>
            <p>Найдите идеальный подарок для ваших друзей</p>
        </div>

        <?php if (empty($wishlists)): ?>
            <div class="empty-state">
                <p>У ваших друзей пока нет доступных вишлистов</p>
                <a href="?action=search_users" class="btn btn-primary">Найти друзей</a>
            </div>
        <?php else: ?>
            <div class="wishlists-grid">
                <?php foreach ($wishlists as $wishlist): ?>
                    <div class="wishlist-card">
                        <?php if (!empty($wishlist['cover_image'])): ?>
                            <img src="<?php echo htmlspecialchars($wishlist['cover_image']); ?>" alt="Cover" class="wishlist-cover">
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($wishlist['title']); ?></h3>
                        <p class="wishlist-author">
                            <a href="?action=user_profile&id=<?php echo $wishlist['user_id']; ?>">
                                <?php echo htmlspecialchars($wishlist['username']); ?>
                            </a>
                        </p>
                        <p class="wishlist-event"><?php echo htmlspecialchars(translateEventType($wishlist['event_type'])); ?></p>
                        <?php if ($wishlist['event_date']): ?>
                            <p class="wishlist-date"><?php echo date('d.m.Y', strtotime($wishlist['event_date'])); ?></p>
                        <?php endif; ?>
                        <p class="wishlist-items"><?php echo $wishlist['items_count']; ?> подарков</p>
                        <a href="?action=view_wishlist&id=<?php echo $wishlist['id']; ?>" class="btn btn-small">Посмотреть</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
