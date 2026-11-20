let output = document.getElementById("output");
let albumTabel = document.getElementById("tableBody");
let uitvoer = document.getElementById("uitvoer");

let albumTitel = document.getElementById("albumTitel");
let albumArtiest = document.getElementById("albumArtiest");
let albumJaartal = document.getElementById("albumJaartal");
let albumGenre = document.getElementById("albumGenre");
let albumNummers = document.getElementById("albumNummers");

class Album{
    constructor(titel, artiest, jaartal, genre, nummers){
        this.albumTitel = titel;
        this.albumArtiest = artiest;
        this.albumJaartal = jaartal;
        this.albumGenre = genre;
        this.albumNummers = nummers;
    }

    addRowToTable(tableId){
        const nowRow = tableId.insertRow();
        for(let [key, value] of Object.entries(this)){
            if (key == "albumNummers") {value = value.join(", ")}

            let cell = nowRow.insertCell();
            cell.appendChild(document.createTextNode(value));
        }
    }

    async opslaan(){
        const response = await fetch("jsonWrite.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(this)
        });

        let antwoord = await response.json();

        if (!response.ok) {
            throw new Error(antwoord.errors.join(" "));
        }

    }
}

async function getAlbums() {
    try {
        const response = await fetch("jsonRead.php");
        if (!response.ok) {
            throw new Error(response.status);
        }
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new TypeError("geen geldige json");
        }
        let data = await response.json();

        console.log(data);

        albumTabel.innerHTML = "";
        data.forEach(albumData => {
            let album = new Album(albumData.albumTitel, albumData.albumArtiest, albumData.albumJaartal, albumData.albumGenre, albumData.albumNummers);
            album.addRowToTable(albumTabel);
        })

    } catch (error) {
        console.error(error.message);
        uitvoer.innerHTML = "Albums konden niet geladen worden";
    }
}

document.getElementById('opslaan').addEventListener('click', async () => {
    uitvoer.innerHTML = "";

    if (!albumTitel.reportValidity() || !albumArtiest.reportValidity() || !albumJaartal.reportValidity() || !albumGenre.reportValidity()) {return}

    let nummers = [];
    albumNummers.value.split("\n").forEach(regel => {
        if (regel.trim()!="") {
            nummers.push(regel.trim());
        }
    })

    let album = new Album(albumTitel.value, albumArtiest.value, Number(albumJaartal.value), albumGenre.value, nummers);

    try {
        await album.opslaan();

        albumTitel.value = "";
        albumArtiest.value = "";
        albumJaartal.value = "";
        albumGenre.value = "";
        albumNummers.value = "";

        uitvoer.innerHTML = "Album opgeslagen!";
        getAlbums();
    } catch (error) {
        console.error('Error:', error);
        uitvoer.innerHTML = error.message;
    }

})

getAlbums();
