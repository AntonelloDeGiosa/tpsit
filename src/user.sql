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