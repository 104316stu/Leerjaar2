This is the httpdocs of leerjaar 2 plesk server glr

## Een project toevoegen

Een map wordt een project op de homepage als er een van deze bestanden in staat:

- `<naam>.git` met een GitHub-link. De deploy zet de bestanden van die repo in de map.
- `index.php` of `index.html`
- `project.json`

`project.json` is optioneel en bepaalt hoe het project op de homepage staat:

```json
{
    "titel": "Ontkoking",
    "beschrijving": "Eén of twee zinnen over wat het is.",
    "vak": "Beroeps",
    "tags": ["PHP", "SQLite"],
    "periode": "P2",
    "status": "bezig",
    "uitgelicht": true,
    "link": "index.php",
    "cover": "assets/screenshot.png",
    "onderdelen": [
        { "titel": "Opdracht 1", "link": "Opdracht 1/html.html" }
    ],
    "verberg": false
}
```

Alles mag weg. Zonder `titel` komt de naam van de repo of map, zonder `beschrijving` de omschrijving op GitHub.
`link` en `cover` zijn relatief aan de map van het project. Zonder `cover` maakt de deploy zelf een screenshot.
De namen en volgorde van de vakken staan in `includes/subjects.php`.
`tags`, `periode` en `status` worden niet getoond (op tags kun je wel zoeken). De cover van een
project met `uitgelicht` is het plaatje als je de homepage deelt.

## Lagen

De homepage toont alleen de vakken. Elk vak en elke map heeft een eigen pagina, bijvoorbeeld `?vak=Program2/OOP`.
Een nieuwe map verschijnt vanzelf zodra er een project in staat.
Van een project met alleen een `project.json` (geen eigen pagina of `.git`) worden de submappen, pagina's,
afbeeldingen en documenten getoond.

## Hoe updates live komen

1. Push naar `main` van deze repo: de deploy start meteen.
2. Push naar een gekoppelde repo: `deploy-check.php` ziet de nieuwe commit en start de deploy.
   Een timer roept die pagina elke 5 minuten aan (zie hieronder).
3. Elk uur draait de deploy ook vanzelf, als vangnet.
4. Zondagnacht wordt alles opnieuw geüpload en krijgen alle projecten een nieuwe cover.
   Dat kan ook met de hand: Actions → Deploy to Plesk (FTP) → Run workflow → "Alles opnieuw uploaden".

Na elke deploy opent de workflow alle projecten en hun bestanden op de live site.
Geeft iets een 404, een 500 of een PHP-fout, dan faalt de run en stuurt GitHub een mail.

### Eenmalig instellen

- GitHub-secret `DEPLOY_CHECK_KEY`: een lange willekeurige tekst (Settings → Secrets and variables → Actions).
- Op cron-job.org een job die elke 5 minuten
  `https://104316.stu.sd-lab.nl/deploy-check.php?key=<DEPLOY_CHECK_KEY>` opent.
- Optioneel, voor updates binnen een minuut: zet `.github/templates/notify-portfolio.yml`
  in de gekoppelde repo, met daar het secret `PORTFOLIO_TOKEN`. Uitleg staat in dat bestand.
