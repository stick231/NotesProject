document.addEventListener('DOMContentLoaded', () => {
    if (!localStorage.getItem('activeIcon')) {
        localStorage.setItem('activeIcon', 'notes');
    }

    const activeIcon = localStorage.getItem('activeIcon');
    const notesSection = document.getElementById('notesSection');
    const remindersSection = document.getElementById('remindersSection');
    const reminderTimeInput = document.getElementById('reminderTime');
    const date_inp_container = document.querySelector("#data-input-container")

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
    const date_inp_container = document.getElementById("date-input-container");

    // Получаем элемент reminderTimeInput каждый раз, когда вызывается функция
    let reminderTimeInput = document.getElementById('reminderTime');

    if (this.getAttribute('data-icon') === "notes") {
        // Показать секцию заметок
        if (notesSection) {
            notesSection.style.display = 'grid';
        } else {
            console.error('Секция заметок не найдена.');
        }

        if (remindersSection) {
            remindersSection.style.display = 'none';
        } else {
            console.error('Секция напоминаний не найдена.');
        }

        document.getElementById("search").value = "";

        // Удаляем элемент reminderTimeInput, если он существует
        if (reminderTimeInput && reminderTimeInput.parentNode) {
            date_inp_container.removeChild(reminderTimeInput);
        }

        readNote(); // Вызов функции для чтения заметок

    } else if (this.getAttribute('data-icon') === "reminders") {
        // Показать секцию напоминаний
        if (notesSection) {
            notesSection.style.display = 'none';
        } else {
            console.error('Секция заметок не найдена.');
        }

        if (remindersSection) {
            remindersSection.style.display = 'grid';
        } else {
            console.error('Секция напоминаний не найдена.');
        }

        document.getElementById("search").value = "";

        // Если reminderTimeInput не существует или не имеет родителя, создаем новый элемент
        if (!reminderTimeInput || !reminderTimeInput.parentNode) {
            const newReminderTime = document.createElement('input');
            newReminderTime.id = 'reminderTime';
            newReminderTime.type = 'datetime-local';
            newReminderTime.name = 'reminder_time';
            newReminderTime.placeholder = 'Время напоминания';

            if (date_inp_container) {
                date_inp_container.appendChild(newReminderTime); // Добавляем новый элемент в контейнер
            } else {
                console.error('Контейнер для ввода времени не найден.');
            }
        }

        readReminders(); // Вызов функции для чтения напоминаний
    }
}

// Пример добавления обработчика событий для иконок
document.querySelectorAll('.nav-icons div').forEach(icon => {
    icon.addEventListener('click', handleIconClick);
});
function readReminders(searchData = "") {
    let sql = "index.php?read=reminder";

    if(searchData){
        sql += `&search=${encodeURIComponent(searchData)}`
    }

    fetch(sql, {
        method: "GET"
    })
    .then(data => {
    })
    .catch(error => {
        console.log("Произошла ошибка: " + error.message);
    });
}
function readNote(searchData = "") {
    let url = "index.php?read=note";

    if (searchData) {
        url += `&search=${encodeURIComponent(searchData)}`;
    }

    fetch(url, {
        method: "GET"
    })
    .then(data => {
        
    })
    .catch(error => {
        console.log("Произошла ошибка: " + error.message);
    });
}


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

let noteId;
let isEditing = true;

function createNote() {
    const form = document.getElementById("noteForm");
    const formData = new FormData(form);

    fetch('index.php', {
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






function expandInput() {
    document.getElementById('TitleInp').style.display = 'block';
    document.getElementById("submitBut").style.display = "block";
    
    if(document.getElementById("reminderTime")){
        document.getElementById("reminderTime").style.display = "block"
    }
}

function collapseInput() 
{
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