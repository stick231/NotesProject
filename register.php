<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        $error = "Такой пользователь уже есть";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashedPassword]);

        $userId = $mysqli->insert_id;
        setcookie("user_id", $userId, time() + 3600 * 60 * 60 * 60 * 60, "/"); 
        $_SESSION['user_id'] = $userId;

        $_SESSION["register_username"] = $username;
        $_SESSION['just_registered'] = true;
        header('Location: index.html');
        exit();
    }
}
if($_SERVER['REQUEST_METHOD'] == "GET"){

}
else{
    if(isset($_COOKIE["user_id"])) {
        header("location: index.html");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles_user.css">
    <title>Регистрация</title>
</head>
<body>
    <div id="container">
    <form id="Form" method="post">
        <h1>Регистрация</h1>    
        <?php if (isset($error)) echo "<p>$error</p>";?>
        <input type="text"  maxlength="20" id="username" name="username" placeholder="Логин.." required>
        <br>
        <input type="password" id="password" maxlength="30" name="password" placeholder="Пароль.." required>
        <br>
        <a id="link">Уже зарегистрирован</a>
        <button type="submit">Зарегистрироваться</button>
    </form>
    </div>
    <script>
        document.getElementById("link").addEventListener("click", ()=>{
            fetch("login.php?register=true",{
                method: "GET"
            })
            .then(data =>{
                console.log(data)
                window.location = "login.php"
            })
        })
    </script>
</body>
</html>