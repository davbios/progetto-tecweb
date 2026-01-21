USE progetto;

DROP TABLE IF EXISTS users_fav_drinks;
DROP TABLE IF EXISTS steps;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS ingredients;
DROP TABLE IF EXISTS drinks;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS users_fav_drinks;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(200) NOT NULL,
    email VARCHAR(200) NOT NULL UNIQUE,
    password VARCHAR(200) NOT NULL,
    is_admin BOOLEAN NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(300) NOT NULL,
    poster VARCHAR(400),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE drinks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    poster VARCHAR(400) NOT NULL,
    creator_id INT NOT NULL REFERENCES users(id),
    category_id INT REFERENCES categories(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    text TEXT NOT NULL,
    rate REAL NOT NULL,
    user_id INT NOT NULL REFERENCES users (id),
    drink_id INT NOT NULL REFERENCES drinks (id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE ingredients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(400) NOT NULL,
    drink_id INT NOT NULL REFERENCES drinks(id),
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE steps (
    id INT PRIMARY KEY AUTO_INCREMENT,
    drink_id INT NOT NULL REFERENCES drinks(id),
    num INT NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE users_fav_drinks (
    user_id INT NOT NULL REFERENCES users(id),
    drink_id INT REFERENCES drinks(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (user_id, drink_id)
);

-- Dati di test
INSERT INTO categories (name, poster) VALUES ('Energetici', NULL), ('Rilassanti', NULL), ('Musicali', NULL);
INSERT INTO users (username, email, password, is_admin) VALUES ('admin', 'admin@example.com', '$2y$12$lRGpE/AHZZ3WSHyXc4KC.engFmMm24lqT3LxJlO2OVfujxWFzrZAa', 1); -- psw: admin
INSERT INTO users (username, email, password, is_admin) VALUES ('user', 'user@example.com', '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0); -- psw: user

INSERT INTO users (username, email, password, is_admin) VALUES 
('admin', 'admin@example.com', '$2y$12$lRGpE/AHZZ3WSHyXc4KC.engFmMm24lqT3LxJlO2OVfujxWFzrZAa', 1),
('user', 'user@example.com', '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('mario_rossi', 'mario.rossi@email.it', '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('luigi_verdi', 'luigi.verdi@test.com', '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('giulia_bianchi', 'giulia.b@example.org', '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('francesca_neri', 'fra.neri@provider.net', '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('alessandro_sartori', 'alex.sartori@web.com', '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0), 
('sofia_galli', 'sofia.galli@cinema.it', '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('barman_pro', 'mixologist@drinks.com', '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('laura_monti', 'laura.monti@music.it', '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('valerio_romano', 'v.romano@motogp.com', '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('dario_esposito', 'dario.esposito@cucina.it', '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('critico_gastronomico', 'recensioni@foodblog.com', '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('studente_universitario', 'studente@uni.it', '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('party_planner', 'feste@events.com', '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0);

INSERT INTO categories (name, poster) VALUES 
('drink felici', 'src/img/categorie/1.png'),
('drink classici', 'src/img/categorie/2.png'),
('drink arrabbiati', 'src/img/categorie/3.png'),
('drink playlist', 'src/img/categorie/4.png'),
('drink festa', 'src/img/categorie/5.png'),
('drink assurdi', 'src/img/categorie/6.png');

INSERT INTO drinks (name, description, poster, creator_id, category_id) VALUES
-- CAT 1: DRINK FELICI (ID 1)
('Pina Colada', 'Ananas, cocco e rum: un sorso e sei subito in vacanza col sorriso.', 'src/img/drink/Pina_Colada.png', 11, 1),
('Aperol Spritz', 'Arancione come il sole al tramonto, porta allegria immediata.', 'src/img/drink/Aperol_Spritz.png', 10, 1),
('Tequila Sunrise', 'I colori dell\'alba nel bicchiere per iniziare la serata con ottimismo.', 'src/img/drink/Tequila_Sunrise.png', 1, 1),
('Bellini', 'Prosecco e polpa di pesca fresca. Dolce, frizzante e felice.', 'src/img/drink/Bellini.png', 5, 1),

-- CAT 2: DRINK CLASSICI (ID 2)
('Martini Dry', 'Agitato, non mescolato. L\'eleganza fatta a bicchiere.', 'src/img/drink/Martini_Dry.png', 2, 2),
('Gin Tonic', 'Poche chiacchiere, tanta classe. Il drink che non delude mai.', 'src/img/drink/Gin_Tonic.png', 1, 2),
('Negroni', 'Il re dell\'aperitivo italiano: Gin, Vermouth e Campari in parti uguali.', 'src/img/drink/Negroni.png', 3, 2),
('Manhattan', 'Whisky e Vermouth rosso, un gusto deciso che attraversa i secoli.', 'src/img/drink/Manhattan.png', 4, 2),

-- CAT 3: DRINK ARRABBIATI (ID 3)
('Angelo Azzurro', 'Gin e Cointreau per una gradazione che non perdona. Fortissimo.', 'src/img/drink/Angelo_Azzurro.png', 1, 3),
('Long Island Iced Tea', 'Sembra innocuo tè freddo, ma contiene 4 tipi di alcol diversi. Attento.', 'src/img/drink/Long_Island_Iced_Tea.png', 5, 3),
('Assenzio', 'La fata verde che ha fatto impazzire i poeti. Non per deboli di cuore.', 'src/img/drink/Assenzio.png', 6, 3),

-- CAT 4: DRINK PLAYLIST (ID 4)
('Whisky Sour', 'L\'equilibrio perfetto tra dolce e aspro, come un buon pezzo Jazz.', 'src/img/drink/Whisky_Sour.png', 7, 4),
('Irish Coffee', 'Caffè corretto Whiskey e panna. Per restare svegli ad ascoltare musica.', 'src/img/drink/Irish_Coffee.png', 8, 4),
('Cuba Libre', 'Rum e Cola. Il ritmo dei Caraibi nel tuo bicchiere.', 'src/img/drink/Cuba_Libre.png', 1, 4),

-- CAT 5: DRINK FESTA (ID 5)
('Mojito Cubano', 'Menta pestata e ghiaccio: impossibile non ballare con questo in mano.', 'src/img/drink/Mojito_Cubano.png', 9, 5),
('Red Bull Vodka', 'Il carburante della discoteca. Per chi vuole vedere l\'alba.', 'src/img/drink/Red_Bull_Vodka.png', 2, 5),
('Shot Tequila e Sale', 'Non si sorseggia, si butta giù. Il via ufficiale a ogni festa.', 'src/img/drink/Shot_Tequila_e_Sale.png', 12, 5),
('Caipirinha', 'Lime e zucchero di canna direttamente dal Brasile. Festa assicurata.', 'src/img/drink/Caipirinha.png', 13, 5),

-- CAT 6: DRINK ASSURDI (ID 6)
('Cervello di Scimmia', 'Un mix di Baileys e sciroppo che crea un effetto visivo... particolare.', 'src/img/drink/Cervello_di_Scimmia.png', 14, 6),
('Drink Diego', 'La leggenda narra che nessuno ricordi gli ingredienti il giorno dopo. Un mix segreto e potente.', 'src/img/drink/Drink_Diego.png', 8, 6),
('Bloody Mary', 'Succo di pomodoro, tabasco e vodka. Praticamente una zuppa alcolica.', 'src/img/drink/Bloody_Mary.png', 15, 6);

INSERT INTO reviews (drink_id, user_id, rate, text) VALUES
-- 1. PINA COLADA
(1, 12, 5.0, 'Mi sento ai Caraibi, ma sono a Padova col fattorino della pizza. 10/10.'),
(1, 4, 3.5, 'Troppo cocco, ora parlo solo con le noci. Però buono.'),
(1, 8, 4.5, 'L\'ombrellino mi è finito in un occhio, ma ne è valsa la pena.'),

-- 2. APEROL SPRITZ
(2, 3, 5.0, 'Ne ho bevuti 5 e ora parlo veneto fluentemente. Ostrega!'),
(2, 6, 4.0, 'È arancione fluo, spero non sia radioattivo. Comunque dissetante.'),
(2, 9, 2.5, 'Troppo ghiaccio, c\'era un iceberg nel mio bicchiere che ha affondato il Titanic.'),

-- 3. TEQUILA SUNRISE
(3, 11, 5.0, 'L\'alba l\'ho vista davvero dopo averne bevuti tre di fila.'),
(3, 10, 2.0, 'Troppo dolce, mi si sono cariati i denti solo a guardarlo.'),
(3, 5, 4.5, 'Bello da vedere, ma poi ti colpisce a tradimento come un ex fidanzato.'),

-- 4. BELLINI
(4, 7, 5.0, 'Mi sento una contessa decaduta a Venezia. Molto chic.'),
(4, 13, 3.0, 'Sapeva di succo di frutta, ne ho bevuti 8 e ora non mi sento le gambe.'),
(4, 2, 4.0, 'Delicato. Forse troppo, sembrava la colazione di mia nonna.'),

-- 5. MARTINI DRY
(5, 9, 5.0, 'Ho chiesto "agitato non mescolato" e il barman mi ha guardato malissimo. Drink top.'),
(5, 12, 4.0, 'Praticamente benzina in un bicchiere elegante. Adoro.'),
(5, 4, 2.5, 'L\'oliva era la parte più nutriente della mia cena.'),

-- 6. GIN TONIC
(6, 5, 4.5, 'Il classico non muore mai. Se lo sbagli sei da arresto.'),
(6, 8, 3.0, 'Troppa acqua tonica, volevo dimenticare gli esami, non idratarmi.'),
(6, 11, 4.0, 'Ginepro incastrato tra i denti tutta la sera, ma che stile.'),

-- 7. NEGRONI
(7, 14, 5.0, 'Il mio fegato ha chiesto pietà dopo il primo sorso. Consigliatissimo.'),
(7, 3, 5.0, 'Amaro come la mia vita sentimentale. Perfetto.'),
(7, 7, 3.5, 'Forte. Forse troppo. Ho visto la Madonna che mi salutava.'),

-- 8. MANHATTAN
(8, 6, 4.5, 'Mi sento in un film in bianco e nero, ma a colori e molto confuso.'),
(8, 10, 5.0, 'La ciliegina alcolica alla fine è la trappola mortale. Delizioso.'),
(8, 2, 4.0, 'Roba da duri, infatti ho pianto dopo il secondo.'),

-- 9. ANGELO AZZURRO
(9, 12, 5.0, 'Blu come il cielo, forte come un pugno in faccia. Non ricordo il ritorno a casa.'),
(9, 13, 5.0, 'Ottimo per resettare la memoria meglio di Men in Black.'),
(9, 4, 2.0, 'Sa di detersivo per i vetri, ma fa il suo sporco dovere.'),

-- 10. LONG ISLAND ICED TEA
(10, 8, 5.0, '5 alcolici insieme? Chi l\'ha inventato è un genio del male assoluto.'),
(10, 11, 4.5, 'Economico ed efficace per spegnere il cervello in 10 minuti.'),
(10, 9, 3.5, 'Il giorno dopo avevo il mal di testa scritto in braille sulla fronte.'),

-- 11. ASSENZIO
(11, 15, 5.0, 'Ho visto i folletti verdi che ballavano la macarena sul bancone.'),
(11, 5, 4.0, 'Brucia tutto, anche i peccati. Esperienza mistica.'),
(11, 7, 5.0, 'Non è un drink, è un viaggio astrale. Attenzione al ritorno.'),

-- 12. WHISKY SOUR
(12, 10, 5.0, 'L\'albume d\'uovo mi preoccupava, invece è come bere una nuvola alcolica.'),
(12, 6, 3.0, 'Troppo aspro, mi si è chiusa la faccia come un bulldog.'),
(12, 2, 4.5, 'Mi sento un cantante jazz fallito e depresso. Atmosfera bellissima.'),

-- 13. IRISH COFFEE
(13, 12, 5.0, 'Caffeina e alcol: ora sono ubriaco ma velocissimo.'),
(13, 4, 3.5, 'La panna mi è finita sui baffi, ma non ho i baffi. Imbarazzante.'),
(13, 8, 5.0, 'Meglio del cappuccino della macchinetta dell\'uni. Ti sveglia e ti stende.'),

-- 14. CUBA LIBRE
(14, 13, 4.5, 'Rum e Coca, la colazione dei campioni (o dei disperati).'),
(14, 9, 2.0, 'Sembrava una granita sbiadita, troppo ghiaccio.'),
(14, 3, 5.0, 'Viva la rivoluzione! (Ho perso il telefono dopo il terzo).'),

-- 15. MOJITO CUBANO
(15, 5, 4.0, 'Ho mangiato più insalata in questo bicchiere che in tutta la settimana.'),
(15, 11, 2.5, 'Zucchero di canna non sciolto, sto masticando sabbia praticamente.'),
(15, 14, 4.5, 'Fresco, ma la menta tra i denti è un ottimo anticoncezionale.'),

-- 16. RED BULL VODKA
(16, 12, 5.0, 'Il cuore batte a tempo techno, le gambe non rispondono. Fantastico.'),
(16, 6, 3.0, 'Sa di sciroppo per la tosse radioattivo, ma ti tiene sveglio.'),
(16, 15, 4.5, 'Sono le 4 del mattino e ho voglia di pulire tutta la casa.'),

-- 17. SHOT TEQUILA E SALE
(17, 10, 5.0, 'Sale, limone, tequila... e pavimento. La sequenza è questa.'),
(17, 8, 4.0, 'Perché ci facciamo questo male? Ne ordino un altro.'),
(17, 2, 5.0, 'Ottimo per dimenticare il proprio nome e cognome in 3 secondi netti.'),

-- 18. CAIPIRINHA
(18, 13, 3.5, 'Pestata bene, ma il lime era duro come il cemento armato.'),
(18, 7, 5.0, 'Samba liquida! Ho ballato col lampione fuori dal locale per un\'ora.'),
(18, 4, 3.0, 'Troppo zucchero, domani ho appuntamento diretto dal dentista.'),

-- 19. CERVELLO DI SCIMMIA
(19, 3, 2.0, 'Sembrava disgustoso, invece era... no, confermo, era disgustoso.'),
(19, 5, 4.5, 'L\'ho bevuto a occhi chiusi ed è andata bene. L\'aspetto è horror.'),
(19, 11, 5.0, 'Bruttissimo da vedere, perfetto per spaventare gli amici. Geniale.'),

-- 20. DRINK DIEGO
(20, 12, 5.0, 'Chi è Diego? Perché mi sono svegliato in Molise? Aiuto.'),
(20, 8, 4.5, 'Ho chiesto gli ingredienti e il barman è scappato piangendo. Misterioso.'),
(20, 14, 5.0, 'Leggendario. Non ricordo il sapore ma ricordo la sensazione di onnipotenza.'),

-- 21. BLOODY MARY
(21, 5, 5.0, 'Il salvavita ufficiale della domenica mattina. Grazie di esistere.'),
(21, 14, 2.0, 'Sembra di bere una pizza margherita liquida e fredda. Mai più.'),
(21, 3, 4.5, 'Piccante al punto giusto, mi ha svegliato anche i parenti defunti.');



