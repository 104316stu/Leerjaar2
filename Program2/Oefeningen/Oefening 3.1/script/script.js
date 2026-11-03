let output = document.getElementById("output");
let studentTabel = document.getElementById("tableBody");

let studentNaam = document.getElementById("studentNaam");
let studentNummer = document.getElementById("studentNummer");

class Student{
    constructor(naam, nummer, telefoon, klas){
        this.studentNaam = naam;
        this.studentNummer = nummer;
        this.studentTelefoon = telefoon;
        this.studentKlas = klas;
    }

    addRowToTable(tableId){
        const nowRow = tableId.insertRow();
        for(let [key, value] of Object.entries(this)){
            let cell = nowRow.insertCell();
            cell.appendChild(document.createTextNode(value));
        }
    }

    
}

async function getStudents() {
    try {
        const response = await fetch("jsonRead.php");
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new TypeError("Dit is geen geldige JSON!");
        }
        let data = await response.json(); // read the body as JSON
 
        console.log(data);
        // hier jouw code om langs de gegevens te loopen
        data.forEach(studentData => {
            let student = new Student(studentData.studentNaam, studentData.studentNummer, studentData.studentTelefoon, studentData.studentKlas);
            console.log(student, studentData);
            student.addRowToTable(studentTabel);
        })
    
    } catch (error) {
        console.error(error.message);
    }
}

document.getElementById('opslaan').addEventListener('click', () => {
    console.log('hallo');

    let student = new Student(studentNaam.value, studentNummer.value);
    student.addRowToTable(studentTabel);

     fetch("jsonWrite.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(student)
    })

})


// Haal initieel al de studenten op die in de database staan
getStudents();

