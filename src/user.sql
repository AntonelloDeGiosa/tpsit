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



INSERT INTO user (nome, cognome, email, username, password, totp_secret, ruolo) VALUES 
('Admin', 'admin', 'admin@example.com', 'admin', '$2y$10$px4seOzDmkt/VWy17u92/.ImrvFxPMQ3MOfKUbT6nbRCNbpF.zF8G', 'CAWDIC3VTL3YRLZ3IIGM6OQU6J5OAYZ5G72I74MJDTNLBCWP3AD5DFFKPQ5U2C6HGRZDOEZ47JXXMZFDUZNDS53V2X2PUXX6CBRVQWY',2);

