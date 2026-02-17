CREATE TABLE libri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    immagine VARCHAR(255) null,
    titolo VARCHAR(255),
    autore VARCHAR(255),
    descrizione TEXT,
    copie_totali INT DEFAULT 0,
    copie_disponibili INT DEFAULT 0
);


CREATE TABLE prestiti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_utente INT NOT NULL,
    id_libro INT NOT NULL,
    data_prestito DATETIME NOT NULL,
    data_restituzione DATETIME NOT NULL,
    restituito TINYINT DEFAULT 0,
    FOREIGN KEY (id_utente) REFERENCES user(id),
    FOREIGN KEY (id_libro) REFERENCES libri(id)
);


INSERT INTO libri (immagine, titolo, autore, descrizione, copie_totali, copie_disponibili) 
VALUES 
('1984.jpg', '1984', 'George Orwell', 'Un romanzo distopico che esplora i temi del totalitarismo, della sorveglianza di massa e della repressione delle libertà individuali.', 10, 10),

('piccolo_principe.jpg', 'Il Piccolo Principe', 'Antoine de Saint-Exupéry', 'Un racconto poetico che, sotto forma di favola per bambini, affronta temi profondi come il senso della vita e l''amore.', 15, 15),

('divina_commedia.jpg', 'La Divina Commedia', 'Dante Alighieri', 'Il capolavoro della letteratura italiana: un viaggio allegorico tra Inferno, Purgatorio e Paradiso.', 5, 5),

('il_nome_della_rosa.jpg', 'Il nome della rosa', 'Umberto Eco', 'Un giallo storico ambientato in un monastero medievale, tra misteriosi omicidi e dispute filosofiche.', 8, 8),

('lo_hobbit.jpg', 'Lo Hobbit', 'J.R.R. Tolkien', 'L''avventura di Bilbo Baggins che precede gli eventi de Il Signore degli Anelli, in un mondo fantastico ricco di draghi e tesori.', 12, 12),

('orgoglio_pregiudizio.jpg', 'Orgoglio e Pregiudizio', 'Jane Austen', 'Un classico della letteratura inglese che analizza con ironia le dinamiche sociali e amorose dell''Ottocento.', 7, 7),

('centanni_solitudine.jpg', 'Cent''anni di solitudine', 'Gabriel García Márquez', 'La saga della famiglia Buendía a Macondo, capostipite del realismo magico sudamericano.', 6, 6),

('il_vecchio_e_il_mare.jpg', 'Il vecchio e il mare', 'Ernest Hemingway', 'La lotta epica tra un vecchio pescatore e un enorme pescespada nelle acque del Golfo del Messico.', 10, 10),

('harry_potter_pietra.jpg', 'Harry Potter e la Pietra Filosofale', 'J.K. Rowling', 'Il primo capitolo della saga del mago più famoso del mondo, alle prese con il suo primo anno a Hogwarts.', 20, 20),

('don_chisciotte.jpg', 'Don Chisciotte della Mancia', 'Miguel de Cervantes', 'Le bizzarre avventure di un nobile spagnolo che, impazzito per i romanzi cavallereschi, decide di diventare un cavaliere errante.', 4, 4);