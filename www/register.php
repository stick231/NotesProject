<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Controllers\AuthController;
use Entities\Database;
use Entities\User;
use Repository\UserRepository;

$authAction = new AuthController();

if(isset($_GET['register']) && $_GET['register'] === false){
    setcookie("register", 'false', time() + 3600 * 24 * 30, "/");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'])) {
    $database = new Database();

    $username = $_POST["username"];
    $password = $_POST["password"];

    $user = (new User())
        ->setUsername($username)
        ->setPassword($password);

    $userRepository = new UserRepository($database);

    if ($userRepository->register($user)) {
        $authAction->redirectToHomePage();
    } else {
        $response = "Такой пользователь уже есть!";
        $_SESSION['register_error'] = $response;
        header("Location: /register"); 
        exit; 
    }
}

if (isset($_COOKIE["user_id"]) && isset($_COOKIE["register"]) && $_COOKIE["register"] === true) {
    $authAction->redirectToHomePage();
}
elseif(isset($_SESSION["login"])){
    $authAction->redirectToHomePage();
}

if (isset($_SESSION['register_error'])) {
    $response = $_SESSION['register_error'];
    unset($_SESSION['register_error']); 
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
    <form id="Form" method="post" action="/register">
        <h1>Регистрация</h1>    
        <?php if (isset($response)) echo "<p>$response</p>";?>
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
            fetch("/auth?register=true", {
                method: "GET",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
            })
            .then(response => {
                if (response.ok) {
                    console.log("Перенаправление на вход");
                    setTimeout(() => {
                        window.location = "/auth";
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