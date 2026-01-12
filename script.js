const API_URL = 'api.php';

function incarcaLista() {
    fetch(API_URL)
        .then(res => res.json()) 
        .then(data => {
            if (data.error) {
                console.error("Eroare Backend:", data.error);
                alert("Nu m-am putut conecta la baza de date: " + data.error);
                return;
            }

            const tbody = document.getElementById('tabelStudenti');
            tbody.innerHTML = ''; 

            if (Array.isArray(data)) {
                data.forEach(student => {
                    const rand = `
                        <tr>
                            <td>${student.id}</td>
                            <td>${student.nume}</td>
                            <td>${student.an}</td>
                            <td>${student.media}</td>
                        </tr>
                    `;
                    tbody.innerHTML += rand;
                });
            } else {
                console.warn("Formatul datelor este incorect:", data);
            }
        })
        .catch(err => console.error("Eroare fetch:", err));
}

document.getElementById('studentForm').addEventListener('submit', function(e) {
    e.preventDefault(); 

    const studentNou = {
        nume: document.getElementById('nume').value,
        an: document.getElementById('an').value,
        media: document.getElementById('media').value
    };

    fetch(API_URL, {
        method: "POST",
        headers: { 
            "Content-Type": "application/json" 
        },
        body: JSON.stringify(studentNou)
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert("Student adăugat!");
            document.getElementById('studentForm').reset();
            incarcaLista();
        } else {
            alert("Eroare: " + data.message);
        }
    });
});
incarcaLista();