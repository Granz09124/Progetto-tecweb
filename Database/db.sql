-- CREATE DATABASE IF NOT EXISTS palestra_db;
-- USE palestra_db;
use <nome_utente>;

DROP TABLE IF EXISTS Assegnazione_PT;
DROP TABLE IF EXISTS Messaggio_Contattaci;
DROP TABLE IF EXISTS Sottoscrizione;
DROP TABLE IF EXISTS Abbonamento;
DROP TABLE IF EXISTS Personal_Trainer;
DROP TABLE IF EXISTS Istruttore;
DROP TABLE IF EXISTS Cliente;
DROP TABLE IF EXISTS Admin;
DROP TABLE IF EXISTS Utente;

CREATE TABLE Utente (
    id_utente SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cognome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash CHAR(60) NOT NULL 
);

CREATE TABLE Admin (
    id_utente BIGINT UNSIGNED PRIMARY KEY REFERENCES Utente(id_utente) ON DELETE CASCADE
);

CREATE TABLE Cliente (
    id_utente BIGINT UNSIGNED PRIMARY KEY REFERENCES Utente(id_utente) ON DELETE CASCADE,
    codice_fiscale CHAR(16) UNIQUE,
    telefono VARCHAR(20)
);

CREATE TABLE Istruttore (
    id_utente BIGINT UNSIGNED PRIMARY KEY REFERENCES Utente(id_utente) ON DELETE CASCADE,
    specializzazione VARCHAR(100),
    qualifica VARCHAR(100),
    telefono VARCHAR(20),
    presentazione VARCHAR(255)
);

CREATE TABLE Personal_Trainer (
    id_istruttore BIGINT UNSIGNED PRIMARY KEY REFERENCES Istruttore(id_utente) ON DELETE CASCADE
);

CREATE TABLE Abbonamento (
    id_abbonamento SERIAL PRIMARY KEY,
    nome_tipo VARCHAR(50) NOT NULL,
    durata_mesi INTEGER NOT NULL
);

CREATE TABLE Sottoscrizione (
    id_utente BIGINT UNSIGNED REFERENCES Utente(id_utente) ON DELETE CASCADE,
    id_abbonamento BIGINT UNSIGNED REFERENCES Abbonamento(id_abbonamento) ON DELETE CASCADE,
    data_inizio DATE NOT NULL,
    data_fine DATE NOT NULL,
    PRIMARY KEY (id_utente, id_abbonamento, data_inizio)
);

CREATE TABLE Messaggio_Contattaci (
    id_messaggio SERIAL PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    messaggio TEXT NOT NULL,
    data_invio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Assegnazione_PT (
    id_pt BIGINT UNSIGNED REFERENCES Istruttore(id_utente) ON DELETE CASCADE,
    id_cliente BIGINT UNSIGNED REFERENCES Cliente(id_utente) ON DELETE CASCADE,
    dimensione_file BIGINT UNSIGNED DEFAULT NULL,
    data_caricamento TIMESTAMP DEFAULT NULL,
    CONSTRAINT caricamento_scheda CHECK (
        (dimensione_file IS NULL AND data_caricamento IS NULL)
        OR
        (dimensione_file IS NOT NULL AND data_caricamento IS NOT NULL)
    ),
    PRIMARY KEY (id_pt, id_cliente)
);

-- ============================================
-- POPOLAZIONE DATABASE
-- ============================================


INSERT INTO Utente (nome, cognome, email, password_hash) VALUES
-- Admin e User base
('admin', 'admin', 'admin', '$2y$10$MOgMUGhexKnJFh3dgUI7C.KIHKr2ISlgtI2pgrCMssPcyrY9L3o4K'),

-- Clienti
('user', 'user', 'user', '$2y$10$5IHAhSlYq8ToQCSjwT0NkuB53Z5YiXL9pkrtjjMWMJxgX25OBP8jy'),
('Mario', 'Rossi', 'mario.rossi@email.it', '$2y$10$T8.sGrRVSpkYvEGR4LyZyu/CoMJweu2n7Nvv/5ZB/EURwH699X1qy'),
('Laura', 'Bianchi', 'laura.bianchi@email.it', '$2y$10$T8.sGrRVSpkYvEGR4LyZyu/CoMJweu2n7Nvv/5ZB/EURwH699X1qy'),
('Giuseppe', 'Verdi', 'giuseppe.verdi@email.it', '$2y$10$T8.sGrRVSpkYvEGR4LyZyu/CoMJweu2n7Nvv/5ZB/EURwH699X1qy'),
('Francesca', 'Neri', 'francesca.neri@email.it', '$2y$10$T8.sGrRVSpkYvEGR4LyZyu/CoMJweu2n7Nvv/5ZB/EURwH699X1qy'),
('Alessandro', 'Ferrari', 'alessandro.ferrari@email.it', '$2y$10$T8.sGrRVSpkYvEGR4LyZyu/CoMJweu2n7Nvv/5ZB/EURwH699X1qy'),
('Giulia', 'Romano', 'giulia.romano@email.it', '$2y$10$T8.sGrRVSpkYvEGR4LyZyu/CoMJweu2n7Nvv/5ZB/EURwH699X1qy'),

-- Istruttori e Personal Trainer
('Marco', 'Colombo', 'marco.colombo@tempio.apollo.it', '$2y$10$T8.sGrRVSpkYvEGR4LyZyu/CoMJweu2n7Nvv/5ZB/EURwH699X1qy'),
('Sofia', 'Ricci', 'sofia.ricci@tempio.apollo.it', '$2y$10$T8.sGrRVSpkYvEGR4LyZyu/CoMJweu2n7Nvv/5ZB/EURwH699X1qy'),
('Luca', 'Marino', 'luca.marino@tempio.apollo.it', '$2y$10$T8.sGrRVSpkYvEGR4LyZyu/CoMJweu2n7Nvv/5ZB/EURwH699X1qy'),
('Elena', 'Greco', 'elena.greco@tempio.apollo.it', '$2y$10$T8.sGrRVSpkYvEGR4LyZyu/CoMJweu2n7Nvv/5ZB/EURwH699X1qy'),
('Davide', 'Bruno', 'davide.bruno@tempio.apollo.it', '$2y$10$T8.sGrRVSpkYvEGR4LyZyu/CoMJweu2n7Nvv/5ZB/EURwH699X1qy'),
('pt', 'pt', 'pt', '$2y$10$iVSg/2pEdSCzXf.tUPQ0deY2yxjwAp./RQrgfN7QS/aQvWy7RiKfK');


INSERT INTO Admin (id_utente) VALUES 
(1);

-- Inserimento Clienti
INSERT INTO Cliente (id_utente, codice_fiscale, telefono) VALUES
(2, 'USRUSR00A01H501Z', '3331234567'),
(3, 'RSSMRA85M10F205X', '3409876543'),
(4, 'BNCLRA90H52L219Y', '3472345678'),
(5, 'VRDGPP75D15A944K', '3383456789'),
(6, 'NREFNC88T41F839M', '3494567890'),
(7, 'FRRLSN92B03D612P', '3355678901'),
(8, 'RMNGLI95C44H501Q', '3426789012');

-- Inserimento Istruttori
INSERT INTO Istruttore (id_utente, specializzazione, qualifica, presentazione) VALUES
(9, 'Bodybuilding e Sala Pesi', 'Personal Trainer Certificato', 'Basta allenarsi a caso. Ti guido passo dopo passo verso risultati concreti e duraturi. Massa, forza o ricomposizione corporea: il tuo percorso inizia ora.'),
(10, 'Yoga e Meditazione', 'Istruttrice Yoga', 'Il tuo successo è il mio obiettivo. Ti accompagno in un percorso di cambiamento fisico e mentale, passo dopo passo. Niente schede copia-incolla, solo supporto costante.'),
(11, 'Functional Training e Crossfit', 'Preparatore Atletico', "Preparazione atletica specifica per calciatori e atleti d'élite. Sviluppo potenza, velocità e prevenzione infortuni per portarti al livello superiore. Allenati da professionista"),
(12, 'Pilates', 'Istruttrice Pilates Certificata', "Riscopri il tuo corpo con il Pilates: <script>alert()</script>. Un percorso di benessere per una schiena sana e una mente rilassata"),
(13, 'Arrampicata e Fit Box', 'Coach Certificato', "Allenamenti di Fit Box ad alta energia: il mix perfetto tra tecnica, musica e sudore. Brucia calorie divertendoti e tira fuori la grinta. Ti aspetto al sacco!"),
(14, 'Personal trainer di esempio', 'Personal trainer di esempio', 'Personal trainer di esempio');

-- Inserimento Personal Trainer
INSERT INTO Personal_Trainer (id_istruttore) VALUES
(9),  -- Marco Colombo
(10), -- Sofia Ricci
(13), -- Davide Bruno
(14); -- Personal trainer di esempio


-- Inserimento Abbonamenti
INSERT INTO Abbonamento (nome_tipo, durata_mesi) VALUES
-- Abbonamenti Sala Pesi
('Sala Pesi Mensile', 1),
('Sala Pesi Trimestrale', 3),
('Sala Pesi Annuale', 12),

-- Abbonamenti Arrampicata
('Arrampicata Mensile', 1),
('Arrampicata Trimestrale', 3),
('Arrampicata Annuale', 12),

-- Abbonamenti Yoga
('Yoga Mensile', 1),
('Yoga Trimestrale', 3),
('Yoga Annuale', 12),

-- Abbonamenti Pilates
('Pilates Mensile', 1),
('Pilates Trimestrale', 3),
('Pilates Annuale', 12),

-- Abbonamenti Crossfit
('Crossfit Mensile', 1),
('Crossfit Trimestrale', 3),
('Crossfit Annuale', 12),

-- Abbonamenti Fit Box
('Fit Box Mensile', 1),
('Fit Box Trimestrale', 3),
('Fit Box Annuale', 12),

-- Abbonamenti Zumba
('Zumba Mensile', 1),
('Zumba Trimestrale', 3),
('Zumba Annuale', 12),

-- Bundle
('Bundle Relax & Benessere', 1),
('Bundle All Inclusive Premium', 1),
('Bundle Fitness Completo', 1);

-- Inserimento Sottoscrizioni
INSERT INTO Sottoscrizione (id_utente, id_abbonamento, data_inizio, data_fine) VALUES
-- Sottoscrizioni User
(2, 1, '2025-01-01', '2025-01-31'),

-- Sottoscrizioni Clienti
(3, 4, '2024-07-01', '2025-06-30'),   -- Mario - Sala Pesi Annuale
(4, 23, '2024-10-01', '2025-09-30'),  -- Laura - Bundle All Inclusive
(5, 11, '2024-11-01', '2025-04-30'),  -- Giuseppe - Yoga Semestrale
(6, 21, '2025-01-01', '2025-01-31'),  -- Francesca - Fit Box Mensile
(7, 17, '2025-01-15', '2025-02-14'),  -- Alessandro - Crossfit Mensile
(8, 2, '2024-07-01', '2024-09-30'),   -- Giulia - Sala Pesi Trimestrale (scaduto)

-- Sottoscrizioni Istruttori
(9, 4, '2024-01-01', '2025-12-31'),   -- Marco - Sala Pesi Annuale
(10, 12, '2024-01-01', '2025-12-31'), -- Sofia - Yoga Annuale
(11, 20, '2024-01-01', '2025-12-31'), -- Luca - Crossfit Annuale
(12, 16, '2024-01-01', '2025-12-31'), -- Elena - Pilates Annuale
(13, 8, '2024-01-01', '2025-12-31');  -- Davide - Arrampicata Annuale

-- Assegna Mario Rossi (id 3) al PT Marco Colombo (id 9)
INSERT INTO Assegnazione_PT (id_pt, id_cliente, dimensione_file, data_caricamento) VALUES (9, 3, 195843, '2026-02-03 12:47:20');
