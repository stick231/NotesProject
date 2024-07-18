<?php
session_start();
require_once 'db.php';

setcookie("register", 'none', time() + 3600 * 24 * 30, "/");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'])) {
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
        setcookie("user_id", $userId, time() + 3600 * 24 * 30, "/"); 
        setcookie("register", "true", time() + 3600 * 24 * 30, "/");
        $_SESSION['user_id'] = $userId;

        $_SESSION["register_username"] = $username;
        $_SESSION['just_registered'] = true;
        header('Location: index.html');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    if ($_POST['register'] == 'false') {
    }
} else {
    if (isset($_COOKIE["user_id"]) && $_COOKIE["register"] === "true") {
        header("Location: index.html");
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
        <input type="text"  maxlength="20" id="username" name="username" placeholder="Логин..">
        <br>
        <input type="password" id="password" maxlength="30" name="password" placeholder="Пароль..">
        <br>
        <a id="link">Уже зарегистрирован</a>
        <button type="submit" id="submit">Зарегистрироваться</button>
    </form>
    </div>
    <script>
        document.getElementById("link").addEventListener("click", () => {
            fetch("login.php", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: "register=true"
            })
            .then(response => {
                if (response.ok) {
                    console.log("Перенаправление на вход");
                    setTimeout(() => {
                        window.location = "login.php";
                    }, 500);
                } else {
                    console.error("Ошибка при перенаправлении:", response.status);
                }
            })
            .catch(error => {
                console.error("Ошибка при выполнении запроса:", error);
            });
        });


        function CheckInp(){
    const inpUsername = document.getElementById("username");
    const inpPassword = document.getElementById("password");

    if(inpUsername.value == null || inpUsername.value == ""){
        alert("Введите имя пользователя");
        return false;
    }

    if(inpPassword.value == null || inpPassword.value == ""){
        alert("Введите пароль");
        return false;
    }

    if(inpUsername.value.length < 4){
        alert("Имя пользователя должно быть больше 4 символов");
        return false;
    }

    if(inpPassword.value.length < 4){
        alert("Пароль должен содержать больше 4 символов");
        return false;
    }
    return true;
}

document.getElementById("submit").addEventListener("click", (event) =>{
    if (!CheckInp()) {
        event.preventDefault();
    }
});
    </script>
</body>
</html>