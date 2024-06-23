window.addEventListener('DOMContentLoaded', () => {
    checkUser()
    readNote();
    collapseInput()
});

function checkUser() {
    fetch("checkuser.php")
    .then(response => response.json())
    .then(data => {
        console.log(data);
        if (data.register) {
            console.log("User is registered");

            const el = document.getElementById("header");
            const imgEl = el.querySelector("img"); 

            const LoginEl = document.createElement("p");
            LoginEl.textContent = data.login;

            el.insertBefore(LoginEl, imgEl.nextSibling);

            if (!data.authentication) {
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
    return true;
}

function createNote() {
    const Form = document.getElementById("noteForm");
    const formData = new FormData(Form);

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
            readNote();
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
            readNote();
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
        document.getElementById('TitleInp').style.display = 'block';
        
        const titleInp = document.getElementById("TitleInp");
        const contentInp = document.getElementById("NoteInp");

        titleInp.value = data.title;
        contentInp.value = data.content;

        const buttonSubmit = document.getElementById("submitBut");
        buttonSubmit.style.display = "block"
        buttonSubmit.textContent = "Сохранить";

        const header = document.getElementById('header')
        header.scrollIntoView({ behavior: 'smooth' });
        
        noteId = idNote;
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
            readNote();
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
    let url = "read.php"

    if (searchData){
        url += `?search=${encodeURIComponent(searchData)}`
    }
    
    fetch(url, {
        method: "GET"
    })
    .then(response => response.json())
    .then(data => {
        const noteList = document.getElementById("noteList");
        noteList.innerHTML = ""; 

        data.forEach(note => {
            let noteDiv = document.createElement('div');
            noteDiv.classList.add('note');

            let dateCreation = document.createElement('p');
            dateCreation.classList.add('dateElement');
            dateCreation.textContent = `дата создания: ${note.timeCreate}`;

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
            noteDiv.appendChild(dateCreation);
            noteDiv.appendChild(noteListdel);
            noteDiv.appendChild(changeButtonNotes);

            noteList.appendChild(noteDiv); 
        });
    })
    .catch(error => {
        console.log("Произошла ошибка: " + error.message);
        alert("Произошла ошибка: " + error.message);
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
}

function collapseInput() {
    document.getElementById('TitleInp').value = '';
    document.getElementById('NoteInp').value = '';
    document.getElementById("submitBut").style.display = "none";
    document.getElementById('TitleInp').style.display = 'none';
}

const searchInp = document.getElementById("search")

searchInp.addEventListener("input", () =>{
    readNote(searchInp.value);
})