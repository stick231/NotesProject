<?php
session_start();
require_once 'db.php';

if (isset($_GET['register']) && $_GET['register'] === 'true') {
    $_SESSION['register'] = true;
}


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
            $userId = $row['id'];
            setcookie("user_id", $userId, time() + 3600 * 60 * 60 * 60 * 60, "/");
            
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

if(isset($_SESSION["login"]) || isset($_SESSION['just_registered'])){
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
        <?php if (isset($warning)) echo "<p class=''>$warning</p>";?>
        <input type="text" id="username" name="login" placeholder="Логин.." required>
        <br>
        <input type="password" id="password" placeholder="Пароль.." name="password" required>
        <br>
        <a id="link">Еще не регистрировался</a>
        <button type="submit">Войти</button>
    </form>
    </div>
    <script>
        document.getElementById("link").addEventListener("click", ()=>{
            fetch("register.php",{
                method: "GET"
            })
            .then(data =>{
                console.log(data)
                window.location = "register.php";
            })
        })
    </script>
</body>
</html>