<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои вишлисты - Wishlist</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php 
    include 'header.php';
    require_once __DIR__ . '/../helpers/event_types.php';
    
    $wishlistModel = new Wishlist();
    $wishlists = $wishlistModel->getByUserId($user['id']);
    ?>
    
    <main class="container">
        <div class="page-header">
            <h1>Мои вишлисты</h1>
            <a href="?action=create_wishlist" class="btn btn-primary">Создать новый</a>
        </div>

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
                    <p class="wishlist-visibility"><?php echo $wishlist['is_public'] ? 'Публичный' : 'Приватный'; ?></p>
                    <a href="?action=view_wishlist&id=<?php echo $wishlist['id']; ?>" class="btn btn-small">Открыть</a>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($wishlists)): ?>
                <div class="empty-state">
                    <p>У вас пока нет вишлистов</p>
                    <a href="?action=create_wishlist" class="btn btn-primary">Создать первый вишлист</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
