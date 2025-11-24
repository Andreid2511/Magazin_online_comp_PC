document.getElementById("btnAdauga").addEventListener("click",function(){
    const input = document.getElementById("inputActivitate");
    const activitate = input.value.trim();
    if(activitate !== ""){
        const luni = [
            "Ianuarie", "Februarie", "Martie", "Aprilie", "Mai", "Iunie",
            "Iulie", "August", "Septembrie", "Octombrie", "Noiembrie", "Decembrie"
        ]
        const d = new Date();
        const zi = d.getDate();
        const luna = luni[d.getMonth()];
        const an = d.getFullYear();

        const lista = document.getElementById("listaActivitati")
        const elementNou = document.createElement("li");
        elementNou.textContent = `${activitate} - adăugată la: ${zi} ${luna} ${an}`;
        lista.appendChild(elementNou);
        input.value = "";
    }
})