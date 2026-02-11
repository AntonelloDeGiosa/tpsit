CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50),
    cognome VARCHAR(50),
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    totp_secret VARCHAR(128)
);