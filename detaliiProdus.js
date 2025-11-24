window.addEventListener("DOMContentLoaded", () => {    
    const detalii = document.getElementById("detaliiProdus");
    const btn = document.getElementById("btnDetalii");
    const dataProdus = document.getElementById("dataValabilitate");

    
    const luni = [
        "Ianuarie", "Februarie", "Martie", "Aprilie", "Mai", "Iunie",
        "Iulie", "August", "Septembrie", "Octombrie", "Noiembrie", "Decembrie"
    ]
    const d = new Date();
    const zi = d.getDate();
    const luna = luni[d.getMonth()];
    const an = d.getFullYear();

    dataProdus.textContent = `${zi} ${luna} ${an}`;

    btn.addEventListener("click", () => {
        detalii.classList.toggle("ascuns");
        
        if(detalii.classList.contains("ascuns")){
            btn.textContent = "Afișează detalii";
        } else {
            btn.textContent = "Ascunde detalii";
        }
        
    })
});