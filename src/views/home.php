<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist - Главная</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php 
    include 'header.php'; 
    require_once __DIR__ . '/../helpers/event_types.php';
    ?>
    
    <main class="container">
        <section class="hero">
            <h1>Создавайте и делитесь вишлистами</h1>
            <p>Платформа для управления списками желаемых подарков к любым праздникам</p>
            <?php if (!$user): ?>
                <div class="hero-actions">
                    <a href="?action=register" class="btn btn-primary">Регистрация</a>
                    <a href="?action=login" class="btn btn-secondary">Войти</a>
                </div>
            <?php else: ?>
                <div class="hero-actions">
                    <a href="?action=create_wishlist" class="btn btn-primary">Создать вишлист</a>
                    <a href="?action=my_wishlists" class="btn btn-secondary">Мои вишлисты</a>
                </div>
            <?php endif; ?>
        </section>

        <section class="features">
            <h2>Возможности</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <h3>Создание вишлистов</h3>
                    <p>Создавайте списки к дням рождения, свадьбам, Новому году и другим событиям</p>
                </div>
                <div class="feature-card">
                    <h3>Делитесь</h3>
                    <p>Делитесь своими вишлистами с друзьями и семьей</p>
                </div>
                <div class="feature-card">
                    <h3>Резервирование</h3>
                    <p>Резервируйте подарки, чтобы избежать дублирования</p>
                </div>
                <div class="feature-card">
                    <h3>Идеи подарков</h3>
                    <p>Получайте вдохновение из каталога идей подарков</p>
                </div>
            </div>
        </section>

        <section class="recent-wishlists">
            <h2>Недавние публичные вишлисты</h2>
            <?php
            $wishlistModel = new Wishlist();
            $wishlists = $wishlistModel->getPublic(6);
            ?>
            <div class="wishlists-grid">
                <?php foreach ($wishlists as $wishlist): ?>
                    <div class="wishlist-card">
                        <?php if (!empty($wishlist['cover_image'])): ?>
                            <img src="<?php echo htmlspecialchars($wishlist['cover_image']); ?>" alt="Cover" class="wishlist-cover">
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($wishlist['title']); ?></h3>
                        <p class="wishlist-author">от <?php echo htmlspecialchars($wishlist['username']); ?></p>
                        <p class="wishlist-event"><?php echo htmlspecialchars(translateEventType($wishlist['event_type'])); ?></p>
                        <p class="wishlist-items"><?php echo $wishlist['items_count']; ?> подарков</p>
                        <a href="?action=view_wishlist&id=<?php echo $wishlist['id']; ?>" class="btn btn-small">Посмотреть</a>
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="?action=explore" class="btn btn-secondary">Смотреть все</a>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
