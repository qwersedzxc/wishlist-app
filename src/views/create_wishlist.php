<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создать вишлист - Wishlist</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php 
    include 'header.php'; 
    require_once __DIR__ . '/../helpers/event_types.php';
    ?>
    
    <main class="container">
        <div class="form-container">
            <h1>Создать новый вишлист</h1>
            <form id="createWishlistForm" class="form" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Название</label>
                    <input type="text" id="title" name="title" required placeholder="Например: Мой день рождения 2024">
                </div>
                <div class="form-group">
                    <label for="description">Описание</label>
                    <textarea id="description" name="description" rows="4" placeholder="Расскажите о событии..."></textarea>
                </div>
                <div class="form-group">
                    <label for="cover_image">Обложка вишлиста (необязательно)</label>
                    <input type="file" id="cover_image" name="cover_image" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="event_type">Тип события</label>
                    <select id="event_type" name="event_type">
                        <?php foreach (getEventTypes() as $value => $label): ?>
                            <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="event_date">Дата события</label>
                    <input type="date" id="event_date" name="event_date">
                </div>
                <div class="form-group">
                    <label for="privacy">Приватность</label>
                    <select id="privacy" name="privacy">
                        <option value="public">Публичный (виден всем)</option>
                        <option value="friends">Доступен только друзьям</option>
                        <option value="link">Доступен по ссылке</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Создать вишлист</button>
            </form>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    <script>
        document.getElementById('createWishlistForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('action', 'create_wishlist');
            
            const response = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.success) {
                window.location.href = 'index.php?action=view_wishlist&id=' + result.id;
            } else {
                alert(result.error || 'Ошибка создания вишлиста');
            }
        });
    </script>
</body>
</html>
