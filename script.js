window.addEventListener('DOMContentLoaded', () => {
    readNote();
});

let noteId;
let isEditing = true;

document.getElementById('noteForm').addEventListener('submit', (event) => {
    event.preventDefault();
    if (isEditing) {
        if (checkInp()) {
            createNote();
            noteId = null;
            console.log(isEditing);
        }
    } else {
        if (checkInp()) {
            updateNote(noteId);
            isEditing = true;
            resetForm();
            console.log(isEditing);
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
        console.log(data);
        if (data.success) {
            console.log(data.message);
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
        console.log(noteId);
        deleteNote(noteId);
    } else if (event.target.classList.contains('changeButton')) {
        const noteId = event.target.dataset.noteId;
        editNoteForm(noteId);
        isEditing = false;
        console.log(isEditing);
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
        console.log(data);
        const titleInp = document.getElementById("TitleInp");
        const contentInp = document.getElementById("NoteInp");

        titleInp.value = data.title;
        contentInp.value = data.content;

        const buttonSubmit = document.getElementById("submitBut");
        buttonSubmit.textContent = "Сохранить";
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

function readNote() {
    fetch("read.php", {
        method: "GET"
    })
    .then(response => response.json())
    .then(data => {
        const noteList = document.getElementById("noteList");
        noteList.innerHTML = "";

        console.log(data);
        data.forEach(note => {
            let noteList = document.getElementById('noteList');

            let noteDiv = document.createElement('div');
            noteDiv.classList.add('note');

            let dateCreation = document.createElement('p');
            dateCreation.classList.add('dateElement');
            dateCreation.textContent = note.timeCreate;

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

            noteDiv.appendChild(noteListdel);
            noteDiv.appendChild(changeButtonNotes);
            noteDiv.appendChild(titleElement);
            noteDiv.appendChild(contentElement);
            noteDiv.appendChild(dateCreation);

            noteList.appendChild(noteDiv);
        });
    })
    .catch(error => {
        console.log("Произошла ошибка: " + error.message);
        alert("Произошла ошибка: " + error.message);
    });
}
