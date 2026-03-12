<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Идеи подарков - Wishlist</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php 
    include 'header.php';
    
    $giftIdeaModel = new GiftIdea();
    $category = $_GET['category'] ?? null;
    $ideas = $giftIdeaModel->getAll($category, 50);
    $categories = $giftIdeaModel->getCategories();
    ?>
    
    <main class="container">
        <div class="page-header">
            <h1>Идеи подарков</h1>
            <p>Вдохновение для выбора идеального подарка</p>
        </div>

        <div class="categories-filter">
            <a href="?action=gift_ideas" class="category-btn <?php echo !$category ? 'active' : ''; ?>">Все</a>
            <?php foreach ($categories as $cat): ?>
                <a href="?action=gift_ideas&category=<?php echo urlencode($cat); ?>" 
                   class="category-btn <?php echo $category == $cat ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($cat); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="gift-ideas-grid">
            <?php foreach ($ideas as $idea): ?>
                <div class="gift-idea-card">
                    <?php if ($idea['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($idea['image_url']); ?>" alt="<?php echo htmlspecialchars($idea['title']); ?>">
                    <?php endif; ?>
                    <div class="gift-idea-content">
                        <h3><?php echo htmlspecialchars($idea['title']); ?></h3>
                        <p><?php echo htmlspecialchars($idea['description']); ?></p>
                        <?php if ($idea['price_range']): ?>
                            <p class="price-range"><?php echo htmlspecialchars($idea['price_range']); ?></p>
                        <?php endif; ?>
                        <?php if ($idea['category']): ?>
                            <span class="category-tag"><?php echo htmlspecialchars($idea['category']); ?></span>
                        <?php endif; ?>
                        <?php if ($idea['url']): ?>
                            <a href="<?php echo htmlspecialchars($idea['url']); ?>" target="_blank" class="btn btn-small">Подробнее</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($ideas)): ?>
                <div class="empty-state">
                    <p>Идеи подарков скоро появятся</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
