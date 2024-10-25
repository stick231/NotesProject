function fetchReadMigration() {
    const formData = new FormData();
    formData.append("migrationRead",true );

    fetch('/migration', {
        method: "POST",
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json(); 
    })
    .then(data => {
        const tableBody = document.getElementById('migrationTableBody'); 
        tableBody.innerHTML = ''; 

        data.forEach(migration => {
            const row = document.createElement('tr');

            row.innerHTML = `
                <th>${migration.id}</th>
                <th>${migration.status}</th>
                <th>${migration.time}</th>
                <th>${migration.time_update}</th>
                <th>${migration.query}</th>
                <th>
                    <button class="button-up" data-migration-id="${migration.id}">up</button>
                    <button class="button-down" data-migration-id="${migration.id}">down</button>
                </th>
                <th>
                    <button data-migration-id="${migration.id}" class="edit-migration">edit</button>
                    <button data-migration-id="${migration.id}" class="delete-migration">delete</button>
                </th>
            `;

            tableBody.appendChild(row);
        });
    })
    .catch(error => {
        console.error('There was a problem with the fetch operation:', error);
    });
}


document.addEventListener('DOMContentLoaded', () => {
    fetchReadMigration();
})

function fetchUpMigration(migrationId){
    const formData = new FormData();
    formData.append("data-migration-up", migrationId);

    fetch('/migration',{
            method : "POST",
            body: formData
        }
    )
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json(); 
    })
    .then(data => {
        alert(data.message);
        fetchReadMigration();
    })
    .catch(error => {
        console.error('There was a problem with the fetch operation:', error);
    });
}

function fetchDownMigration(migrationId){
    const formData = new FormData();
    formData.append("data-migration-down", migrationId);

    fetch('/migration',{
            method : "POST",
            body : formData
        }
    )
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        alert(data.message);
        fetchReadMigration();
    })
    .catch(error => {
        console.error('There was a problem with the fetch operation:', error);
    });
}

document.addEventListener('click', function(event) { 
    if (event.target.classList.contains('button-up')) {
        const migrationId = event.target.dataset.migrationId;
        fetchUpMigration(migrationId);
    } else if (event.target.classList.contains('button-down')) {
        migrationId = event.target.dataset.migrationId;
        fetchDownMigration(migrationId);
    } else if(event.target.classList.contains('edit-migration')){
        migrationId = event.target.dataset.migrationId;
        fetchEditFormMigration(migrationId);
        isEditing = false;
    }else if(event.target.classList.contains('delete-migration')){
        migrationId = event.target.dataset.migrationId;
        fetchDeleteMigration(migrationId)
    }
});

function fetchDeleteMigration(migrationId){
    const formData = new FormData();
    formData.append("data-migration-delete", migrationId);

    fetch('/migration',{
            method : "POST",
            body : formData
        }
    )
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        if(data.success){
            alert(data.message);
            fetchReadMigration();
        }
        else{
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('There was a problem with the fetch operation:', error);
    });
}

function fetchCreateMigration(){
    const formMigration = document.getElementById('formMigration');
    const formData = new FormData(formMigration);
    formData.append("data-migration-create", true);

    fetch('/migration',{
            method : "POST",
            body : formData
        }
    )
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        console.log(data)
        fetchReadMigration();
    })
    .catch(error => {
        console.error('There was a problem with the fetch operation:', error);
    });
}

let migrationId;

function fetchEditFormMigration(migrationIdEdit){
    const formData = new FormData();
    formData.append("data-migration-edit", migrationIdEdit);

    fetch('/migration',{
            method : "POST",
            body : formData
        }
    )
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        migrationId = migrationIdEdit;

        const migrationUpInp = document.getElementById("MigrationUp");
        const migrationDownInp = document.getElementById("MigrationDown");

        const migrationQueryArr = data[0].query.split('|');

        
        migrationUpInp.value = migrationQueryArr[0];
        migrationDownInp.value = migrationQueryArr[1];
        const buttonSubmit = document.getElementById("submitBut");

        buttonSubmit.textContent = "Редактировать";
    })
    .catch(error => {
        console.error('There was a problem with the fetch operation:', error);
    });
}

function fetchUpdateMigration($migrationId){
    const formMigration = document.getElementById('formMigration');
    const formData = new FormData(formMigration);
    formData.append("data-migration-update", $migrationId);

    fetch('/migration',{
            method : "POST",
            body : formData
        }
    )
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        console.log(data);
        fetchReadMigration();
    })
    .catch(error => {
        console.error('There was a problem with the fetch operation:', error);
    });
}

function resetForm() {
    const migrationUpInp = document.getElementById("MigrationUp");
    const migrationDownInp = document.getElementById("MigrationDown");
    const buttonSubmit = document.getElementById("submitBut");

    migrationUpInp.value = "";
    migrationDownInp.value = "";
    buttonSubmit.textContent = "Создать";
}

let isEditing = true;

function checkInp(){
    const migrationUpInp = document.getElementById("MigrationUp").value;
    const migrationDownInp = document.getElementById("MigrationDown").value;
    if(migrationUpInp.trim() === '' || migrationDownInp.trim() === ''){
        alert("Заполните все поля ввода!");
        return false;
    }
    return true;
}

document.getElementById('submitBut').addEventListener('click', (event) => {
    event.preventDefault();
    if (isEditing) {
        if (checkInp()) {
            fetchCreateMigration();
            migrationId = null;
        }
    } else {
        if (checkInp()) {
            fetchUpdateMigration(migrationId);
            isEditing = true;
            resetForm();
        }
    }
})
