
document.getElementById('noteForm').addEventListener('submit', function(event) {
    event.preventDefault();

    let title = document.getElementById('TitleInp').value;
    let content = document.getElementById('NoteInp').value;

    if(title.trim() === "" || content.trim() === ""){
        alert("Введите пожалуйста заголовок и заметку");
        return;
    }

    if(count >= 7){
        alert("Заметки закончились");
        return;
    }

    addNoteToList(title, content);
});

let count = 1;

function addNoteToList(title, content) {
    if (count > 6){
        alert("Заметки закончились");
    } 
    else {
        
        
       let noteList = document.getElementById('noteList');

        let noteDiv = document.createElement('div');
        noteDiv.classList.add('note');

        let noteListdel = document.createElement('span');
        noteListdel.classList.add("noteListdel");
        noteListdel.textContent = '❌';

        let titleNumElement = document.createElement('h1');
        titleNumElement.classList.add("titleH1");
        titleNumElement.textContent = `Заметка №${count++};`

        let titleElement = document.createElement('h3');
        titleElement.textContent = title;

        let contentElement = document.createElement('p');
        contentElement.classList.add('paragraphNote');
        contentElement.textContent = content;

        noteDiv.appendChild(noteListdel);
        noteDiv.appendChild(titleNumElement);
        noteDiv.appendChild(titleElement);
        noteDiv.appendChild(contentElement);

        noteList.appendChild(noteDiv);

        noteListdel.addEventListener("click", function(){
            count--
            noteDiv.remove();
            let notes = JSON.parse(localStorage.getItem('notes')) || [];

            let indexToRemove = notes.findIndex(note => note.title === title && note.content === content);
            if(indexToRemove > -1){
                notes.splice(indexToRemove, 1);
                localStorage.setItem('notes', JSON.stringify(notes));
            }
        });

        let note = { title: title, content: content };
        let notes = JSON.parse(localStorage.getItem('notes')) || [];
        console.log(notes)
        let isDuplicate = notes.some(note => note.title === title && note.content === content);
        if(!isDuplicate){
            notes.push(note);
            localStorage.setItem('notes', JSON.stringify(notes));
        }
    }
}

window.addEventListener('DOMContentLoaded', () => {
    let notes = JSON.parse(localStorage.getItem('notes')) || [];
    notes.forEach(note => {
        addNoteToList(note.title, note.content);
    });
});