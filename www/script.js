document.addEventListener('DOMContentLoaded', () => {
    if (!localStorage.getItem('activeIcon')) {
        localStorage.setItem('activeIcon', 'notes');
    }

    const activeIcon = localStorage.getItem('activeIcon');
    const notesSection = document.getElementById('notesSection');
    const remindersSection = document.getElementById('remindersSection');
    const reminderTimeInput = document.getElementById('reminderTime');
    const date_inp_container = document.getElementById("date-input-container")

    if (activeIcon === "notes") {
        readNote();
        notesSection.style.display = 'grid';
        remindersSection.style.display = 'none';
        if (reminderTimeInput && reminderTimeInput.parentNode) {
            date_inp_container.removeChild(reminderTimeInput);
        }
    } else if (activeIcon === "reminders") {
        readReminders();
        notesSection.style.display = 'none';
        remindersSection.style.display = 'grid';
        if (!reminderTimeInput || !reminderTimeInput.parentNode) {
            const newReminderTime = document.createElement('input');
            newReminderTime.id = 'reminderTime';
            newReminderTime.type = 'datetime-local';
            newReminderTime.name = 'reminder_time';
            newReminderTime.placeholder = 'Время напоминания';
            date_inp_container.appendChild(newReminderTime)
        }
    }

    console.log(activeIcon);
    checkUser();
    collapseInput();

    const navIcons = document.querySelectorAll('.nav-icons div');
    navIcons.forEach(icon => {
        icon.addEventListener('click', handleIconClick);
        if (icon.getAttribute('data-icon') === activeIcon) {
            icon.classList.add('active');
        }
    });
});

function handleIconClick() {
    const navIcons = document.querySelectorAll('.nav-icons div');
    navIcons.forEach(navIcon => navIcon.classList.remove('active'));
    this.classList.add('active');
    localStorage.setItem('activeIcon', this.getAttribute('data-icon'));

    const notesSection = document.getElementById('notesSection');
    const remindersSection = document.getElementById('remindersSection');
    const noteForm = document.getElementById('noteForm');
    const reminderTimeInput = document.getElementById('reminderTime');
    const date_inp_container = document.getElementById("date-input-container")

    if (this.getAttribute('data-icon') === "notes") {
        notesSection.style.display = 'grid';
        remindersSection.style.display = 'none';
        document.getElementById("search").value = ""
        if (reminderTimeInput && reminderTimeInput.parentNode) {
            date_inp_container.removeChild(reminderTimeInput);
        }
        readNote();
    } else if (this.getAttribute('data-icon') === "reminders") {
        notesSection.style.display = 'none';
        remindersSection.style.display = 'grid';
        if (!reminderTimeInput || !reminderTimeInput.parentNode) {
            const newReminderTime = document.createElement('input');
            newReminderTime.id = 'reminderTime';
            newReminderTime.type = 'datetime-local';
            newReminderTime.name = 'reminder_time';
            newReminderTime.placeholder = 'Время напоминания';
            date_inp_container.appendChild(newReminderTime)
        }
        document.getElementById("search").value = ""
        readReminders();
    }
}

function scheduleReminder(note) {
    const reminderTime = new Date(note.reminder_time).getTime();
    const currentTime = new Date().getTime();
    const delay = reminderTime - currentTime;

    if (delay > 0) {
        setTimeout(() => {
            sendReminder(note);
        }, delay);
        fetch("expired.php", {
            method: "POST",
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `id=${note.id}&expired=false`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                console.log(data)
            } 
            else {
            }
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
    } else {
        console.log("Отправляем запрос на expired.php с id:", note.id);
        fetch("expired.php", {
            method: "POST",
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `id=${note.id}&expired=true`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
            } 
            else {
            }
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
    }
}

function sendReminder(note) {
    if (!("Notification" in window)) {
        alert("This browser does not support desktop notification");
    } else if (Notification.permission === "granted") {
        createNotification(note);
    } else if (Notification.permission !== "denied") {
        Notification.requestPermission().then(function (permission) {
            if (permission === "granted") {
                createNotification(note);
            } else {
                alert(`Напоминание: ${note.title}\n${note.content}`);
            }
        });
    } else {
        alert(`Напоминание: ${note.title}\n${note.content}`);
    }

    setTimeout(() => {
        readReminders();
    }, 1000);
}

function createNotification(note) {
    const options = {
        body: note.content,
        icon: 'png/icons8-reminder-241.png', 
        sound: 'sound/message.mp3',
        vibrate: [200, 100, 200], 
        badge: 'png/icons8-notes-48.png', 
        tag: 'reminder',
        renotify: true,
        requireInteraction: true 
    };

    const notification = new Notification(note.title, options);

    if (options.sound) {
        const audio = new Audio(options.sound);
        audio.play().catch(error => console.error('Error playing sound:', error));
    }
}

function readReminders(searchData = "") {
    let sql = "read_reminders.php";

    if(searchData){
        sql += `?search=${encodeURIComponent(searchData)}`
    }

    fetch(sql, {
        method: "GET"
    })
    .then(response => response.json())
    .then(data => {
        const reminderList = document.getElementById("reminderList");
        const expiredReminderList = document.getElementById("expiredReminderList");
        reminderList.innerHTML = "";
        expiredReminderList.innerHTML = ""

        if (data.length === 0) {
            const newTextNoNotes = document.createElement("h3");
            if(searchData){
                newTextNoNotes.textContent = "Таких напоминаний нету. Но вы можете их сделать";
            }
            else{
                newTextNoNotes.textContent = "Напоминаний нету. Но вы можете их сделать";
            }
            newTextNoNotes.classList.add("textNoNotes");
            reminderList.appendChild(newTextNoNotes);
            return;
        }

        data.forEach(note => {
            let noteDiv = document.createElement('div');
            noteDiv.classList.add('note');

            let dateReminders = document.createElement('p');
            dateReminders.classList.add('dateElement');
            dateReminders.textContent = `Напоминание на: ${note.reminder_time}`;

            let dateNote = document.createElement('p');
            dateNote.classList.add('dateElement');
            dateNote.textContent = note.last_update ? `Дата редактирования: ${note.last_update}` : `дата создания: ${note.time}`;

            let noteListdel = document.createElement('span');
            noteListdel.classList.add("noteListdel");
            noteListdel.textContent = '🗑️';
            noteListdel.setAttribute("data-note-id", note.id);

            let changeButtonNotes = document.createElement('span');
            changeButtonNotes.classList.add("changeButton");
            changeButtonNotes.textContent = "✏️";
            changeButtonNotes.setAttribute("data-note-id", note.id);

            let titleElement = document.createElement('h3');
            titleElement.classList.add('h3Note');
            titleElement.textContent = note.title;

            let contentElement = document.createElement('p');
            contentElement.classList.add('paragraphNote');
            contentElement.textContent = note.content;

            noteDiv.appendChild(titleElement);
            noteDiv.appendChild(contentElement);
            noteDiv.appendChild(dateNote);
            noteDiv.appendChild(dateReminders);
            noteDiv.appendChild(noteListdel);
            noteDiv.appendChild(changeButtonNotes);
            
            if(note.expired == 0){
                reminderList.appendChild(noteDiv);
            }
            else{
                document.getElementById("expiredReminderList").appendChild(noteDiv);

                dateReminders.textContent = `Напоминание на: ${note.reminder_time} (Просрочено)`;
            }

            scheduleReminder(note)
        });
    })
    .catch(error => {
        console.log("Произошла ошибка: " + error.message);
    });
}

function checkUser() {
    fetch("checkuser.php")
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log(data);
        if (data.register) {
            console.log("User is registered");

            const el = document.getElementById("header");
            if (el) {
                const imgEl = el.querySelector("img");
                if (imgEl) {
                    const LoginEl = document.createElement("p");
                    LoginEl.textContent = data.register_username ? data.register_username : data.login;
                    el.insertBefore(LoginEl, imgEl.nextSibling);
                } else {
                    console.error("No img element found in header");
                }
            } else {
                console.error("Header element not found");
            }

            if (!data.authentication && !data.just_registered) {
                window.location = "login.php";
            }
        } else {
            console.log("User is not registered");
            window.location = "register.php";
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

document.getElementById("user-img").addEventListener("click", (event) => {
    const btnBack = document.getElementById("btn-back");
    btnBack.classList.add("visible");
});

document.addEventListener("click", (event) => {
    const userImg = document.getElementById("user-img");
    const btnBack = document.getElementById("btn-back");

    if (!userImg.contains(event.target) && !btnBack.contains(event.target)) {
        btnBack.classList.remove("visible");
    }
});

document.getElementById("btn-back").addEventListener("click", () => {
    fetch("back_user.php", {
        method: "POST"
    })
    .then(() => {
        window.location.reload();
    });
});

let noteId;
let isEditing = true;

document.getElementById('submitBut').addEventListener('click', (event) => {
    event.preventDefault();
    if (isEditing) {
        if (checkInp()) {
            createNote();
            noteId = null;
        }
    } else {
        if (checkInp()) {
            updateNote(noteId);
            isEditing = true;
            resetForm();
        }
    }
});

function checkInp() {
    let title = document.getElementById('TitleInp').value;
    let content = document.getElementById('NoteInp').value;

    if (title.trim() === "" || content.trim() === "") {
        alert("Заголовок и содержимое не могут быть пустыми");
        return false;
    }
    if(document.getElementById("reminderTime")){
        let reminder_time = document.getElementById("reminderTime").value
        if(reminder_time.trim() === ""){
            alert("Введите время напоминания");
            return false
        }
    }
    return true;
}

function createNote() {
    const form = document.getElementById("noteForm");
    const formData = new FormData(form);

    fetch('create.php', {
        method: "POST",
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Ошибка сети: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert(data.message);
            if(localStorage.getItem('activeIcon') === "notes"){
                readNote();
            }
            else{
                readReminders();
            }
            resetForm();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.log("Произошла ошибка: " + error.message);
        alert("Произошла ошибка: " + error.message);
    });
}

document.addEventListener('click', function(event) {
    if (event.target.classList.contains("noteListdel")) {
        const noteId = event.target.dataset.noteId;
        deleteNote(noteId);
    } else if (event.target.classList.contains('changeButton')) {
        const noteId = event.target.dataset.noteId;
        editNoteForm(noteId);
        isEditing = false;
    }
});

function deleteNote(idNote) {
    fetch("delete.php", {
        method: "POST",
        body: `id=${idNote}`,
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if(localStorage.getItem('activeIcon') === "notes"){
                readNote();
            }
            else{
                readReminders()
            }
            alert(data.message);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.log("Произошла ошибка: " + error.message);
    });
}

function editNoteForm(idNote) {
    fetch(`read.php?id=${idNote}`, {
        method: "GET",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.length > 0) { 
            expandInput();

            const titleInp = document.getElementById("TitleInp");
            const contentInp = document.getElementById("NoteInp");

            titleInp.value = data[0].title; 
            contentInp.value = data[0].content; 

            const buttonSubmit = document.getElementById("submitBut");
            buttonSubmit.textContent = "Сохранить";

            const header = document.getElementById('header');
            header.scrollIntoView({ behavior: 'smooth' });

            noteId = idNote;
        } else {
            console.error("No note found with the given ID.");
        }
    })
    .catch(error => {
        console.error('Error fetching note:', error);
    });
}

function updateNote(idNote) {
    const Form = document.getElementById("noteForm");
    const formData = new FormData(Form);
    formData.append('id', idNote);

    fetch(`update.php`, {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            if(localStorage.getItem('activeIcon') === "notes"){
                readNote();
            }
            else{
                readReminders()
            }
            resetForm();
        } else {
            alert(data.message);
        }
    });
}

function resetForm() {
    const titleInp = document.getElementById("TitleInp");
    const contentInp = document.getElementById("NoteInp");
    const buttonSubmit = document.getElementById("submitBut");

    titleInp.value = "";
    contentInp.value = "";
    buttonSubmit.textContent = "Создать";
}

function readNote(searchData = "") {
    let url = "read.php";

    if (searchData) {
        url += `?search=${encodeURIComponent(searchData)}`;
    }

    fetch(url, {
        method: "GET"
    })
    .then(response => response.json())
    .then(data => {
        const noteList = document.getElementById("noteList");
        noteList.innerHTML = "";

        if (data.length === 0) {
            const newTextNoNotes = document.createElement("h3");
            newTextNoNotes.textContent = url !== "read.php" ? "Таких заметок нету. Но вы можете их сделать" : "Заметок нету. Но вы можете их сделать";
            newTextNoNotes.classList.add("textNoNotes");
            noteList.appendChild(newTextNoNotes);
            return;
        }

        data.forEach(note => {
            let noteDiv = document.createElement('div');
            noteDiv.classList.add('note');

            let dateNote = document.createElement('p');
            dateNote.classList.add('dateElement');
            dateNote.textContent = note.last_update ? `Дата редактирования: ${note.last_update}` : `дата создания: ${note.time}`;

            let noteListdel = document.createElement('span');
            noteListdel.classList.add("noteListdel");
            noteListdel.textContent = '🗑️';
            noteListdel.setAttribute("data-note-id", note.id);

            let changeButtonNotes = document.createElement('span');
            changeButtonNotes.classList.add("changeButton");
            changeButtonNotes.textContent = "✏️";
            changeButtonNotes.setAttribute("data-note-id", note.id);

            let titleElement = document.createElement('h3');
            titleElement.classList.add('h3Note');
            titleElement.textContent = note.title;

            let contentElement = document.createElement('p');
            contentElement.classList.add('paragraphNote');
            contentElement.textContent = note.content;

            noteDiv.appendChild(titleElement);
            noteDiv.appendChild(contentElement);
            noteDiv.appendChild(dateNote);
            noteDiv.appendChild(noteListdel);
            noteDiv.appendChild(changeButtonNotes);

            noteList.appendChild(noteDiv);
        });
    })
    .catch(error => {
        console.log("Произошла ошибка: " + error.message);
    });
}

document.getElementById('NoteInp').addEventListener('focus', function() {
    expandInput();
});

document.getElementById('submitBut').addEventListener('click', function(event) {
    event.preventDefault();
    collapseInput();
});

document.addEventListener('click', function(event) {
    const inputContainer = document.querySelector('#noteForm');
    if (!inputContainer.contains(event.target)) {
        collapseInput();
    }
});

function expandInput() {
    document.getElementById('TitleInp').style.display = 'block';
    document.getElementById("submitBut").style.display = "block";
    
    if(document.getElementById("reminderTime")){
        document.getElementById("reminderTime").style.display = "block"
    }
}

function collapseInput() {
    document.getElementById('TitleInp').value = '';
    document.getElementById('NoteInp').value = '';
    document.getElementById("submitBut").style.display = "none";
    document.getElementById('TitleInp').style.display = 'none';
    if(document.getElementById("reminderTime")){
        document.getElementById("reminderTime").style.display = "none"
    }
}

const searchInp = document.getElementById("search");

searchInp.addEventListener("input", () => {
    if(localStorage.getItem('activeIcon') === "notes"){
        readNote(searchInp.value);
    }
    else{
        readReminders(searchInp.value)
    }
});