# SportSystem

SportSystem ir sporta nodarbību pārvaldības sistēma, kas apvieno grafiku, grupas, apmeklējumu, maksājumus un paziņojumus vienā platformā. Projekts veidots kā pilna steka risinājums ar `Vue 3 + Vuetify` lietotāja saskarni un `Laravel` servera daļu ar datubāzes integrāciju.

## Projekta mērķis

Sistēma paredzēta sporta skolu, treneru un audzēkņu ikdienas darba organizēšanai. Tā ļauj:

- pārvaldīt lietotājus un viņu lomas;
- veidot grupas un piesaistīt tām dalībniekus;
- definēt regulāras nedēļas nodarbības un automātiski ģenerēt konkrētus nodarbību datumus;
- atzīmēt apmeklējumu;
- apstrādāt maksājumus par mēnesi vai atsevišķām nodarbībām;
- sekot paziņojumiem par svarīgiem notikumiem sistēmā.

## Lietotāju lomas

Sistēmā ir piecas pilnvērtīgas lomas.

### Administrators

Administrators pārvalda visu sistēmu:

- izveido un rediģē lietotājus;
- izveido grupas un piesaista trenerus;
- pārvalda regulārās nodarbības un konkrētos nodarbību datumus;
- var pievienot dalībnieku tikai vienai konkrētai nodarbībai;
- redz sistēmas maksājumus un paziņojumus;
- var atmaksāt maksājumus un mainīt nodarbību statusus.

### Treneris

Treneris strādā ar savām grupām un nodarbībām:

- redz savu darba grafiku;
- redz savas grupas;
- izmanto atsevišķu apmeklējuma lapu ar grupas izvēli un kalendāru;
- atzīmē dalībniekus kā klātesošus vai neklātesošus;
- saņem paziņojumus par izmaiņām grafikā, grupās un dalībniekos.

### Vecāks

Vecāks pārvalda sev piesaistītos bērnus:

- redz bērnu grupas, grafiku un apmeklējumu;
- izvēlas bērnu, par kuru apskatīt informāciju;
- redz maksājumus, parādus, bilanci un darbību vēsturi;
- maksā par mēnesi vai par atsevišķu nodarbību;
- saņem paziņojumus par apmeklējumu, atceltām nodarbībām, maksājumiem un atmaksām.

### Bērns

Bērns redz tikai sev paredzēto informāciju:

- savas grupas;
- savu grafiku;
- savu apmeklējumu;
- paziņojumus par nodarbībām un statusa izmaiņām.

### Pieaugušais dalībnieks

Pilngadīgs dalībnieks sistēmā darbojas patstāvīgi:

- redz savas grupas, grafiku un apmeklējumu;
- pats apmaksā savas nodarbības;
- izmanto savu bilanci;
- saņem paziņojumus tāpat kā neatkarīgs sistēmas dalībnieks.

## Galvenā funkcionalitāte

### 1. Grupas

Grupas satur pamatinformāciju par treniņu procesu:

- grupas nosaukumu un numuru;
- vecuma kategoriju;
- nedēļas dienas un noklusēto laiku;
- cenu;
- piesaistīto treneri;
- dalībnieku sarakstu.

Administrators var piesaistīt grupai gan bērnus, gan pieaugušus dalībniekus. Treneris redz tikai savas grupas. Vecāks, bērns un pieaugušais redz tikai tās grupas, kurās viņi piedalās.

### 2. Nedēļas nodarbības un nodarbību datumi

Sistēma atdala nodarbību veidnes no konkrētiem datumiem:

- nedēļas nodarbību sadaļa glabā regulāro nodarbību loģiku;
- nodarbību datumu sadaļa glabā konkrētus datumus, kas tiek aprēķināti no šīm veidnēm.

Tas ļauj:

- uzturēt vienu nedēļas nodarbības ierakstu;
- automātiski ģenerēt nākamos nodarbību datumus;
- atsevišķi strādāt ar konkrētu nodarbības datumu, neizjaucot visu sēriju.

### 3. Vienreizējs dalībnieks konkrētai nodarbībai

Administrators var pievienot dalībnieku tikai vienam konkrētam nodarbības datumam. Tas noder situācijās, kad dalībnieks apmeklē tikai vienu papildu nodarbību.

Šāds dalībnieks:

- parādās nodarbības dalībnieku sarakstā;
- ir redzams trenera apmeklējuma sarakstā;
- redz šo nodarbību savā grafikā;
- saņem rēķinu par šo vienu nodarbību;
- automātiski nekļūst par pilnvērtīgu grupas dalībnieku.

### 4. Grafiks

Grafika sadaļā lietotāji redz nākamās un iepriekšējās nodarbības. Sistēma ņem vērā:

- nodarbības datumu;
- sākuma un beigu laiku;
- grupu;
- statusu (`plānota`, `pabeigta`, `atcelta`);
- dalībnieku sastāvu konkrētajā datumā.

Ja nodarbība tiek atcelta, tā paliek sistēmā kā atcelta, nevis tiek dzēsta.

### 5. Apmeklējums

Apmeklējuma loģika balstās uz reāliem nodarbību datumiem, nevis abstraktām veidnēm.

Trenerim ir pieejama atsevišķa lapa:

- grupas izvēle;
- kalendārs;
- konkrētās dienas nodarbību izvēle;
- dalībnieku saraksts;
- masveida darbība visu atzīmēšanai kā klātesošus;
- manuāla statusu maiņa.

Pieejamie apmeklējuma statusi:

- klātesošs
- neklātesošs

Papildus:

- ja nodarbība ir pabeigta un apmeklējums nav ievadīts, sistēma automātiski iestata statusu "klātesošs";
- ja treneris jau manuāli ielicis statusu, tas netiek pārrakstīts;
- atceltai nodarbībai apmeklējumu atzīmēt nevar.

### 6. Maksājumi

Maksājumu sistēma atbalsta divus pamatveidus.

#### Maksājums par vienu nodarbību

Par vienu nodarbību maksājums tiek izmantots, ja:

- dalībnieks apmeklē tikai konkrētu nodarbības datumu;
- vai grupas nodarbība tiek apmaksāta atsevišķi.

Cena tiek ņemta:

- vispirms no `session.price`;
- ja tā nav norādīta, tad no `group.price`.

#### Mēneša maksājums

Ja dalībnieks ir pilnvērtīgi piesaistīts grupai, sistēma ļauj apmaksāt mēnesi kopumā.

Cena tiek ņemta no:

- `group.price`

Mēneša maksājums:

- sedz konkrēto grupu un konkrēto mēnesi;
- slēpj atbilstošos atsevišķos vienas nodarbības parādus šajā mēnesī;
- netiek piedāvāts, ja daļa šī mēneša nodarbību jau ir apmaksāta atsevišķi.

#### Maksājuma redzamības loģika

Vienas nodarbības maksājums kļūst redzams:

- 7 dienas pirms maksājuma termiņa

Mēneša maksājums kļūst redzams:

- 7 dienas pirms mēneša sākuma

Ja termiņš ir pagājis, maksājums kļūst kavēts.

### 7. Bilance, atmaksas un kredīti

Sistēmā ir konta bilance, kuru izmanto:

- vecāki;
- pilngadīgie dalībnieki.

Ja apmaksāta nodarbība tiek atcelta:

- par vienu nodarbību samaksātā summa tiek ieskaitīta bilancē;
- ja bija apmaksāts mēnesis, bilancē tiek atgriezta tikai šīs vienas nodarbības daļa.

Ja atcelšana bija kļūda un nodarbība tiek atjaunota:

- iepriekš piešķirtais kredīts tiek automātiski atcelts;
- ja kredīts jau ir iztērēts, atjaunošana tiek bloķēta, lai nesabojātu finanšu loģiku.

### 8. Paziņojumi

Sistēmā ir pilna paziņojumu plūsma vairākām lomām.

Paziņojumi aptver:

- nodarbības atcelšanu un atjaunošanu;
- apmeklējuma statusa maiņu;
- maksājuma gaidīšanu un kavējumu;
- veiksmīgus maksājumus un atmaksas;
- kredīta pievienošanu un atcelšanu;
- dalībnieka pievienošanu vai izņemšanu no vienas nodarbības;
- izmaiņas grupā vai grafikā.

Papildus:

- administrators redz visas sistēmas paziņojumus;
- ir iespējams atzīmēt vienu paziņojumu vai visus paziņojumus kā izlasītus;
- sākumlapā tiek rādīts paziņojumu kopsavilkums.

## Interfeisa īpašības

Projektā ir ieviests:

- responsīvs dizains;
- tumšā tēma;
- vienots dizaina stils lietotāja un administratora sadaļām;
- atsevišķi akcenti administratora un trenera lomām;
- valodas pārslēgšana lietotāja saskarnē (`LV / EN`);
- kartīšu izkārtojums, dialogi, izkrītošās izvēlnes un adaptācija mobilajām ierīcēm.

## Tehnoloģiju steks

### Lietotāja saskarne

- `Vue 3`
- `Vue Router`
- `Pinia`
- `Vuetify`
- `Vite`
- `Axios`

### Servera daļa

- `Laravel 12`
- `Laravel Sanctum`
- `PHP 8.2+`

### Datubāze

- `SQLite` (noklusētajā vietējā konfigurācijā)

## Projekta struktūra

```text
frontend/   Vue + Vuetify lietotāja saskarne
backend/    Laravel API, biznesa loģika, modeļi, migrācijas un testi
uzd/        pasniedzēja uzdevumi un materiāli
```

## Instalēšana un palaišana

### 1. Servera daļa

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

### 2. Lietotāja saskarne

```bash
cd frontend
npm install
npm run dev -- --host 0.0.0.0
```

Lietotāja saskarne pēc noklusējuma darbojas Vite serverī, bet servera daļa nodrošina Laravel API.

## Datubāzes atjaunošana un testēšanas dati

Ja nepieciešams pilnībā pārbūvēt datubāzi ar sākotnējiem datiem:

```bash
cd backend
php artisan migrate:fresh --seed
```

## Testēšana

Servera daļas testus var palaist ar:

```bash
cd backend
php artisan test
```

Lietotāja saskarnes produkcijas būves pārbaude:

```bash
cd frontend
npm run build
```

## Projekta kopsavilkums

SportSystem ir pilna steka vadības sistēma sporta nodarbībām, kurā vienuviet apvienota:

- lietotāju un lomu pārvaldība;
- grupu un nodarbību grafiks;
- apmeklējuma uzskaite;
- maksājumi un bilances loģika;
- paziņojumu sistēma;
- atbalsts gan bērniem un vecākiem, gan treneriem, administratoram un neatkarīgiem pieaugušajiem dalībniekiem.
