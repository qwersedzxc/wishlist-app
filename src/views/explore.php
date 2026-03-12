<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Обзор вишлистов - Wishlist</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php 
    include 'header.php';
    require_once __DIR__ . '/../helpers/event_types.php';
    
    $wishlistModel = new Wishlist();
    $page = $_GET['page'] ?? 1;
    $limit = 12;
    $offset = ($page - 1) * $limit;
    $wishlists = $wishlistModel->getPublic($limit, $offset);
    ?>
    
    <main class="container">
        <div class="page-header">
            <h1>Обзор публичных вишлистов</h1>
            <p>Найдите идеальный подарок для ваших близких</p>
        </div>

        <div class="wishlists-grid">
            <?php foreach ($wishlists as $wishlist): ?>
                <div class="wishlist-card">
                    <?php if (!empty($wishlist['cover_image'])): ?>
                        <img src="<?php echo htmlspecialchars($wishlist['cover_image']); ?>" alt="Cover" class="wishlist-cover">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($wishlist['title']); ?></h3>
                    <p class="wishlist-author">от <?php echo htmlspecialchars($wishlist['username']); ?></p>
                    <p class="wishlist-event"><?php echo htmlspecialchars(translateEventType($wishlist['event_type'])); ?></p>
                    <?php if ($wishlist['event_date']): ?>
                        <p class="wishlist-date"><?php echo date('d.m.Y', strtotime($wishlist['event_date'])); ?></p>
                    <?php endif; ?>
                    <p class="wishlist-items"><?php echo $wishlist['items_count']; ?> подарков</p>
                    <a href="?action=view_wishlist&id=<?php echo $wishlist['id']; ?>" class="btn btn-small">Посмотреть</a>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($wishlists)): ?>
                <div class="empty-state">
                    <p>Публичных вишлистов пока нет</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (count($wishlists) >= $limit): ?>
        <div class="pagination">
            <a href="?action=explore&page=<?php echo $page + 1; ?>" class="btn btn-secondary">Загрузить еще</a>
        </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
