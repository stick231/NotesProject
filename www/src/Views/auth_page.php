<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../../assets/style/styles_user.css">
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&display=swap" rel="stylesheet">
    <title>Вход</title>
</head>
<body>
    <div id="container">
    <form id="Form" method="post" action="auth.php">
        <h1>Вход в аккаунт</h1>
        <?php if (isset($_COOKIE['auth_error'])) echo "<p>$_COOKIE[auth_error]</p>";?>
        <input type="text" id="username" name="login" placeholder="Логин..">
        <br>
        <input type="password" id="password" placeholder="Пароль.." name="password">
        <br>
        <a id="link">Еще не регистрировался</a>
        <button type="submit" id="submit">Войти</button>
    </form>
    </div>
    <script src="../../assets/js/scriptAuth.js"></script>
</body>
</html>