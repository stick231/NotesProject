<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/../../assets/style/styles.css">    
    <link rel="icon" href="assets/png/icons8-notes-48.png" type="image/x-icon">
    <title>Заметки</title>
</head>
<body>
    <header id="header">
        <img src="assets/png/user-circle.png" alt="Пользователь: " id="user-img"><br>
        <div id="btn-back" title="Выйти из аккаунта"><img id="logout" src="assets/png/logout.png"></div>
        <h1>Заметки</h1>
        <input type="text" id="search" placeholder="Поиск">
    </header>
    <div class="sidebar">
        <div class="nav-icons">
            <a href="/notes"><div home-page='1' data-icon="notes" id='div-icon-notes'><span><img id="icon-note" src="assets/png/icons8-notes-48.png" alt=""><p class="text">Заметки</p></span></div></a>
            <a href="/reminders"><div data-icon="reminders" id='div-icon-reminders'><span><img src="assets/png/icons8-reminder-241.png" alt=""><p class="text">Напоминания</p></span></div></a>
        </div>
    </div>
    <div id="container">
        <main>
            <form id="noteForm">
                <input id="TitleInp" type="text" name="title" maxlength="60" placeholder="Введите заголовок..." title="Это ваш заголовок" required>
                <textarea id="NoteInp" name="content" cols="30" rows="3" placeholder="Заметка..." title="Это ваша заметка" required></textarea>
                <div id="data-input-container"></div>
                <button type="submit" id="submitBut">Создать</button>
            </form>
        </main>
    </div>
    <section id="notesSection">
        <div id="noteList"></div>
    </section>
    <section id="remindersSection">
        <div id="reminderList"></div>
        <div id="expiredReminderList"></div>
    </section>
    <script src="../../assets/js/script.js"></script>
</body>
</html>