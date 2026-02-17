# BiblioTech – Sistema di gestione biblioteca scolastica

## Descrizione

BiblioTech è un’applicazione web sviluppata in **PHP** per la gestione dei prestiti di una biblioteca scolastica.
Il sistema digitalizza il registro cartaceo, permettendo la gestione di:

* catalogo libri
* utenti
* prestiti e restituzioni
* disponibilità copie
* autenticazione sicura con **2FA (TOTP)**

Il progetto è containerizzato tramite **Docker** per garantire portabilità e facilità di avvio.

---

## Funzionalità principali

### Autenticazione e sicurezza

* Registrazione utenti
* Login con email e password
* Password salvate come hash
* Autenticazione a due fattori (TOTP)
* Gestione sessioni con scadenza
* Controllo accessi basato sul ruolo

### Studente

* Visualizzazione catalogo libri
* Prestito libri (solo se copie disponibili)
* Visualizzazione storico prestiti

### Bibliotecario / Admin

* Visualizzazione prestiti attivi
* Registrazione restituzioni
* Gestione copie disponibili
* Visualizzazione utenti
* Gestione bibliotecari (Admin)

---

## Tecnologie utilizzate

* PHP + Apache
* MySQL
* HTML / CSS
* Docker
* phpMyAdmin
* TOTP (Two-Factor Authentication)
* 2FAuth (servizio separato)

---

## Architettura del sistema

Servizi Docker:

| Servizio   | Descrizione                                     |
| ---------- | ----------------------------------------------- |
| web        | Applicazione PHP con Apache                     |
| db         | Database MySQL                                  |
| phpmyadmin | Interfaccia web per gestione database           |
| 2fauth     | Gestione codici di autenticazione a due fattori |

Tutti i servizi comunicano tramite rete Docker dedicata.

---

## Requisiti

* Docker
* Docker Compose

Verifica installazione:

```bash
docker --version
docker-compose --version
```

---

## Avvio rapido (per il docente)

### 1. Clonare il repository

```bash
git clone https://github.com/AntonelloDeGiosa/tpsit.git
cd tpsit
```

---

### 2. Avviare i container

```bash
docker-compose up -d --build
```

Attendere circa 20–30 secondi.

---

### 3. Accesso ai servizi

Applicazione web:

```
http://localhost:8080
```

phpMyAdmin:

```
http://localhost:8081
```

2FAuth:

```
http://localhost:8082
```

---

## Configurazione Database

Se il database non viene caricato automaticamente:

1. Aprire phpMyAdmin
2. Accedere con le credenziali definite nel `docker-compose.yml`
3. Importare i file:

```
src/biblioteca.sql
src/user.sql
```

---

## Utente di test 

Dopo l’import del database è possibile utilizzare per accedere:

```
Email: admin@admin.it
Username:admin
Password: admin
Chiave (da inserire in 2FAuth (http://localhost:8082)): CAWDIC3VTL3YRLZ3IIGM6OQU6J5OAYZ5G72I74MJDTNLBCWP3AD5DFFKPQ5U2C6HGRZDOEZ47JXXMZFDUZNDS53V2X2PUXX6CBRVQWY
Con queste credenziali si accede alla parte dedicata all'admin.
Qui si potrà aggiornare:
1. aggiornare il numero di copie 
2.Visuallizare i prestiti attivi 
3. Gestire Bibliotecari
```

(In alternativa è possibile registrare un nuovo utente che sarà studente)

---

## Autenticazione a due fattori (2FA)

Durante la registrazione:

1. Il sistema genera una chiave segreta
2. La chiave deve essere inserita in **2FAuth** (http://localhost:8082)
3. Accedere a 2FAuth e generare il codice TOTP
4. Inserire il codice durante il login

Il codice cambia ogni 30 secondi ed è basato su:

* chiave segreta utente
* orario corrente

---

## Modello dati

Entità principali:

### Utente

* id
* nome
* cognome
* email
* username
* password (hash)
* ruolo
* totp_secret
* data registrazione

### Libro

* id
* titolo
* autore
* descrizione
* copie_totali
* copie_disponibili

### Prestito

* id
* utente
* libro
* data_prestito
* data_restituzione
* stato (attivo/restituito)

### Sessione

* id_sessione
* id_utente
* login
* logout
* scadenza

---

## Logica dei prestiti

* Un libro può essere preso in prestito solo se:

```
copie_disponibili > 0
```

* Alla creazione del prestito:

  * copie_disponibili --
* Alla restituzione:

  * copie_disponibili ++
  * il prestito viene chiuso

---

## Struttura del progetto

```
tpsit/
│
├── docker-compose.yml
├── Dockerfile
├── README.md
│
├── src/
│   ├── index.php
│   ├── login.php
│   ├── register.php
│   ├── dashboard.php
│   ├── libri.php
│   ├── copie.php
│   ├── prestiti_attivi.php
│   ├── prestiti_utente.php
│   ├── amministrazione.php
│   ├── gestione_bibliotecari.php
│   ├── logout.php
│   ├── biblioteca.sql
│   └── user.sql
```

---


## Obiettivo del progetto

Progetto sviluppato per il corso **TPSIT** con lo scopo di realizzare un sistema completo comprendente:

* Analisi dei requisiti
* Progettazione (E-R e UML)
* Implementazione
* Sicurezza (hash + 2FA)
* Containerizzazione Docker
* Distribuzione portabile

L’intero sistema può essere avviato con un solo comando Docker.

---

## Autore

Antonello De Giosa
Progetto scolastico – TPSIT
