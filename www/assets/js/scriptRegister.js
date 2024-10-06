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