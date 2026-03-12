<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - Wishlist</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container">
        <div class="form-container">
            <h1>Вход</h1>
            <form id="loginForm" class="form">
                <div class="form-group">
                    <label for="username">Имя пользователя или Email</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Войти</button>
                <p class="form-footer">Нет аккаунта? <a href="?action=register">Зарегистрироваться</a></p>
            </form>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    <script src="js/app.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            if (!username || !password) {
                alert('Заполните все поля');
                return;
            }
            
            const formData = new FormData(e.target);
            formData.append('action', 'login');
            
            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    window.location.href = 'index.php';
                } else {
                    alert(result.error || 'Ошибка входа');
                }
            } catch (error) {
                alert('Ошибка соединения с сервером');
            }
        });
    </script>
</body>
</html>
