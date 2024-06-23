<?php
session_start();
require_once 'db.php';

if (isset($_POST["login"]) && isset($_POST["password"])) {
    $login = $_POST["login"];
    $password = $_POST["password"];


    $query = "SELECT * FROM users WHERE username = ?";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $login);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
            $_SESSION["login"] = $login;
            header("location: index.html");
            exit;
        } else {
            $warning = "Неверный логин или пароль";
        }
    } else {
        $warning = "Пользователь не найден";
    }
} else {
    $warning = "Ошибка авторизации";
}

if(isset($_SESSION["login"])){
    header("location: index.html");
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles_user.css">
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&display=swap" rel="stylesheet">
    <title>Вход</title>
</head>
<body>
    <div id="container">
    <form id="Form" method="post">
        <h1>Вход в аккаунт</h1>
        <?php if (isset($warning)) echo "<p class=''>$warning</p>"; ?>
        <input type="text" id="username" name="login" placeholder="Логин.." required>
        <br>
        <input type="password" id="password" placeholder="Пароль.." name="password" required>
        <br>
        <button type="submit">Войти</button>
    </form>
    </div>
</body>
</html>