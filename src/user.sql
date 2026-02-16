CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50),
    cognome VARCHAR(50),
    email VARCHAR(100) UNIQUE,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    totp_secret VARCHAR(128),
    ruolo int ,
    registrazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);


CREATE TABLE sessioni (
    id_sessione VARCHAR(64) PRIMARY KEY,
    id_utente INT NOT NULL,
    login DATETIME NOT NULL,
    scadenza DATETIME NOT NULL,
    logout DATETIME NULL,
    FOREIGN KEY (id_utente) REFERENCES user(id)
);

CREATE TABLE libri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titolo VARCHAR(255),
    autore VARCHAR(255),
    descrizione TEXT,
    copie_totali INT DEFAULT 0,
    copie_disponibili INT DEFAULT 0
);


CREATE TABLE prestiti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_utente INT,
    id_libro INT,
    data_prestito DATETIME,
    data_restituzione DATETIME NULL,
    restituito TINYINT DEFAULT 0
);
