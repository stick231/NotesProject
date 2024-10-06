<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/style/styles_user.css">
    <title>Регистрация</title>
</head>
<body>
    <div id="container">
    <form id="Form" method="post" action="register.php">
        <h1>Регистрация</h1>    
        <?php if (isset($_COOKIE['register_error'])) echo "<p>$_COOKIE[register_error]</p>";?>
        <input type="text"  maxlength="20" id="username" name="username" placeholder="Логин..">
        <br>
        <input type="password" id="password" maxlength="30" name="password" placeholder="Пароль..">
        <br>
        <a id="link">Уже зарегистрирован</a>
        <button type="submit" id="submit">Зарегистрироваться</button>
    </form>
    </div>
    <script src="/../../assets/js/scriptRegister.js"></script>
</body>
</html>