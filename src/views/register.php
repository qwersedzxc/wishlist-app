<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Wishlist</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container">
        <div class="form-container">
            <h1>Регистрация</h1>
            <form id="registerForm" class="form">
                <div class="form-group">
                    <label for="username">Имя пользователя</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="full_name">Полное имя</label>
                    <input type="text" id="full_name" name="full_name">
                </div>
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" required minlength="6">
                </div>
                <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                <p class="form-footer">Уже есть аккаунт? <a href="?action=login">Войти</a></p>
            </form>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    <script>
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            // Валидация на клиенте
            if (username.length < 3) {
                alert('Имя пользователя должно быть не менее 3 символов');
                return;
            }
            
            if (password.length < 6) {
                alert('Пароль должен быть не менее 6 символов');
                return;
            }
            
            const formData = new FormData(e.target);
            formData.append('action', 'register');
            
            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    alert('Регистрация успешна! Теперь войдите в систему.');
                    window.location.href = 'index.php?action=login';
                } else {
                    alert(result.error || 'Ошибка регистрации');
                }
            } catch (error) {
                alert('Ошибка соединения с сервером');
            }
        });
    </script>
</body>
</html>
