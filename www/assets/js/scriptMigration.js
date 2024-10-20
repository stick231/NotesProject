function fetchUp(id){
    const formData = new FormData();
    formData.append("data-migration", id);

    fetch('migration.php',{
            method : "POST",
            body: formData
        }
    )
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        // return response.json(); сделать json ответ
    })
    .then(data => {
        console.log(data)
    })
    .catch(error => {
        console.error('There was a problem with the fetch operation:', error);
    });
}

function fetchDown(id){
    fetch('/migration',{
            method : "POST",
            body : JSON.stringify({'data-migration': id })  
        }
    )
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        // return response.json();сделать json ответ
    })
    .then(data => {
        console.log(data)
    })
    .catch(error => {
        console.error('There was a problem with the fetch operation:', error);
    });
}

document.addEventListener('click', function(event) { 
    if (event.target.classList.contains('button-up')) {
        const migrationId = event.target.dataset.migrationId;
        fetchUp(migrationId);
    } else if (event.target.classList.contains('button-down')) {
        migrationId = event.target.dataset.migrationId;
        fetchDown(migrationId);
    }
});