document.getElementById('noteForm').addEventListener('submit', (event)=>{
    event.preventDefault();

    let title = document.getElementById('TitleInp').value;
    let content = document.getElementById('NoteInp').value;
    let timeStr = new Date().toLocaleTimeString();
    let time = `Время создания блока: ${timeStr}`

    if(title.trim() === "" || content.trim() === ""){
        alert("Введите заголовок и заметку корректно");
        return;
    }

    addNoteToList(title, content, time);
});

let count = 1;

function addNoteToList(title, content, time) {
    if (count > 6){
        alert("Заметки закончились");
    }
    else {
        let noteList = document.getElementById('noteList');

        let noteDiv = document.createElement('div');
        noteDiv.classList.add('note');

        let dateCreation = document.createElement('p');
        dateCreation.classList.add('dateElement');
        dateCreation.textContent = time

        let noteListdel = document.createElement('span');
        noteListdel.classList.add("noteListdel");
        noteListdel.textContent = '🗑️';

        let changeButtonNotes = document.createElement('span');
        changeButtonNotes.classList.add("changeButton");
        changeButtonNotes.textContent = "✏️";

        let titleNumElement = document.createElement('h1');
        titleNumElement.classList.add("titleH1");
        titleNumElement.textContent = `Заметка №${count++};`

        let titleElement = document.createElement('h3');
        titleElement.classList.add('h3Note');
        titleElement.textContent = title;

        let contentElement = document.createElement('p');
        contentElement.classList.add('paragraphNote');
        contentElement.textContent = content;

        noteDiv.appendChild(noteListdel);
        noteDiv.appendChild(changeButtonNotes)
        noteDiv.appendChild(titleNumElement);
        noteDiv.appendChild(titleElement);
        noteDiv.appendChild(contentElement);
        noteDiv.appendChild(dateCreation);

        noteList.appendChild(noteDiv);

        noteListdel.addEventListener("click", function(){
            count--;
            noteDiv.remove();
            let notes = JSON.parse(localStorage.getItem('notes')) || [];

            let indexToRemove = notes.findIndex(note => note.title === title && note.content === content);
            if(indexToRemove > -1){
                notes.splice(indexToRemove, 1);
                localStorage.setItem('notes', JSON.stringify(notes));
            }
        });

        changeButtonNotes.addEventListener('click', () =>{
            document.getElementById('TitleInp').value = title;//""
            document.getElementById('NoteInp').value = content;

            let textarea = document.getElementsByTagName("textarea")[0];
            let input = document.getElementById('TitleInp');

            textarea.setAttribute('placeholder','Введите изменения');
            input.setAttribute('placeholder','Введите изменения');

            document.getElementById('submitBut').innerHTML = "Изменить";


            document.getElementById('submitBut').addEventListener('click', () => {
                let editTime = new Date().toLocaleTimeString();
                time = `Время редактирования: ${editTime}`;
                if(input.value === "" || textarea.value === ""){
                    return;
                }
                else {
                    document.getElementById("submitBut").type = "button";
                    let notes = JSON.parse(localStorage.getItem('notes')) || [];
                    notes = notes.map(note => {
                        if (note.title === title && note.content === content) {
                            return { title: input.value, content: textarea.value, time: time}
                        } else {
                            return note;
                        }
                    });
                    localStorage.setItem('notes', JSON.stringify(notes));
                    location.reload();
                }
            });
        });
        let note = { title: title, content: content, time: time };
        let notes = JSON.parse(localStorage.getItem('notes')) || [];
        let isDuplicate = notes.some(note => note.title === title && note.content === content);
        if(!isDuplicate){
            notes.push(note);
            localStorage.setItem('notes', JSON.stringify(notes));
        }
    }
}

window.addEventListener('DOMContentLoaded', () => {
    let dateElement = document.getElementsByClassName('.dateElement')
    dateElement.innerHtml = ""
    let notes = JSON.parse(localStorage.getItem('notes')) || [];
    notes.forEach(note => {
        addNoteToList(note.title, note.content, note.time);
    });
});



