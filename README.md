## automatic-json-saver

### Installation
#### First time Setup
```bash
# Clone the repository to your device.
git clone git@github.com:MeesPos/automatic-json-saver.git

# Install composer packages with
composer install

# Copy the .env.example and change the copied file name to .env
cp .env.example .env

# Generate an app key
php artisan key:generate

# ! When you are done with setting up the .env file proceed

# Migrate the tables and data to your database
php artisan migrate
```
### Setup Environment

#### Database
Add the right database credentials in the ```.env``` file
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=robin-assists
DB_USERNAME=root
DB_PASSWORD=
```

### Proces uitvoeren
Om het proces uit te voeren moet je de installatie volledig uitgevoerd hebben!

Het enige wat je moet doen is het volgende command runnen:

```bash
$ php artisan import:json
```

De code van dit command kan je vinden in:
[App\Console\Commands\ImportJson.php](https://github.com/MeesPos/automatic-json-saver/blob/main/app/Console/Commands/ImportJson.php)

### Voorwaarden
- **[Primair]**	Maak het proces	zo,	dat	elk	moment	kan	worden	afgekapt	(bijv.	door	een
  SIGTERM,	stroomuitval,	etc.),	en	op	een	robuuste,	betrouwbare	manier	precies	door
  kan	gaan	waar	het	laatst	gebleven	was	(zonder	data	te	dupliceren).

Door middel van het cachen van de index heb ik ervoor gezorgd dat het proces weer door gaat op de plek waar het de 
vorige keer gestopt was. Deze cache word toegewezen aan een file naam, op deze manier word de cache van een file
nooit gebruikt word bij een andere file. De cache word verwijderd als het proces helemaal voltooid is, hierdoor
kan het de volgende keer weer vanaf het begin ge-import worden.

- Ontwerp	je	oplossing	'op	de	groei',	rekening	houdend	met	een	hypothetische	klant
  die	telkens nieuwe	wensen zal	hebben

Doordat ik zoveel mogelijk manieren dat standaard in Laravel zit gebruikt heb, is het dus makkelijk om in de
toekomst extra dingen toe te voegen. Alles is gebouwd op de code standaarden en dus voor iedereen leesbaar. De
applicatie is niet zo vast gebouwd dat er veel dingen gesloopt worden als je bezig bent met andere features.

Ook omdat ik de import in een queue job heb gezet, is het makkelijk om het te implementeren naar andere features.
Bijvoorbeeld als de klant wilt dat ze het zelf ergens kan doen, is het niet al te lastig om dit om te schrijven.

- Gebruik	een	degelijk,	maar	niet	overdreven	database-model. Code voor Eloquent-models en relaties zijn 
hierbij	niet	belangrijk,	het	gaat	ons	meer  om	de	gegevensstructuur.

Beide models zijn niet heel uitgebreid. Ze hebben allebei een relationship naar elkaar, en ```$guarded = []``` zodat
alles gewoon opgeslagen en opgehaald kan worden. Voor de rest waren er geen andere dingen nodig, daarom is dat ook
niet toegevoegd.

- Verwerk alleen	records	waarvoor	geldt	dat	de	leeftijd	tussen	de	18	en	65	ligt
  (of	onbekend	is).

Ik heb eerst de leeftijd van de persoon berekend door middel van Laravel Carbon. Die berekent hoeveel tijd er tussen
vandaag zit en de dag dat diegene is geboren. Daarna heb ik een if statement aangemaakt die checkt of de leeftijd niet
onder de 18 zit en ook niet boven de 65 zit. Als dat niet zo is, gaat het door in het proces. Ook als de geboortedatum
onbekend is (oftewel ```null```), gaat het proces door. Deze code kan je vinden in:
[App\Jobs\ImportJson.php](https://github.com/MeesPos/automatic-json-saver/blob/main/app/Jobs/ImportJson.php).

### Bonus
- Wat	nu	als	het	bronbestand	ineens	500	keer	zo	groot	wordt?

Er is een grote kans dat je de volgende error krijgt:
```bash
Allowed memory size of 536870912 bytes exhausted (tried to allocate 251658272 bytes) (Dit zijn random bytes, ligt er
uiteraard aan hoe groot je JSON bestand is)
```
Dit los je op door de memory_limit in je ```php.ini``` te verhogen zodat de server de grootte support van het JSON
bestand.

Uiteraard zou het proces ook langer duren voordat alles is geimporteerd. Maar omdat het een command is, maakt het niet
uit voor de gebruiker van de website. De website blijft gewoon normaal draaien terwijl dit gedaan word.

- Is	het	proces	makkelijk	in	te	zetten	voor	een	XML- of	CSV-bestand	met	vergelijkbare
  content?

Ja, het enige dat aangepast moet worden is de manier van het ophalen van de data. Uiteindelijk moet er een array met
verschillende objects staan met data, dit is gewoon mogelijk met XML- en CSV-bestanden. Alleen zou het ophalen van
de data dus omgeschreven moeten worden

- Stel	dat	alleen	records	moeten	worden	verwerkt	waarvoor	geldt	dat	het	creditcardnummer drie	
opeenvolgende	zelfde	cijfers	bevat,	hoe	zou	je	dat	aanpakken?

Eerst convert ik de string naar een array, zodat ik elke karakter los heb. Daarna ga ik de values bijelkaar tellen en
kijken hoeveel keer een cijfer voor komt.
```bash
$creditcardNumber = '13523643';

$array = str_split($creditcardNumber);

$count = array_count_values($array);
```

De result van ```$count``` is dan:
```bash
array:6 [
  1 => 1
  3 => 3
  5 => 1
  2 => 1
  6 => 1
  4 => 1
]
```

Daarna check ik of een value in de array 3 is, dit zet ik dan in een if statement.
```bash
if (in_array(3, $count)) {
  ...
}
```
Nu heb ik gechecked of er een karakter 3 is, als dat niet is word de code niet gerund van het opslaan. En als het
wel zo is word de code wel gerund.
