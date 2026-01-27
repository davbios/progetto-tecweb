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
    bio TEXT NOT NULL,
    picture VARCHAR(400),
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
    creator_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    category_id INT REFERENCES categories(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    text TEXT NOT NULL,
    rate REAL NOT NULL,
    user_id INT NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    drink_id INT NOT NULL REFERENCES drinks (id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE ingredients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(400) NOT NULL,
    drink_id INT NOT NULL REFERENCES drinks(id) ON DELETE CASCADE,
    quantity VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE steps (
    id INT PRIMARY KEY AUTO_INCREMENT,
    drink_id INT NOT NULL REFERENCES drinks(id) ON DELETE CASCADE,
    num INT NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE users_fav_drinks (
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    drink_id INT REFERENCES drinks(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (user_id, drink_id)
);

INSERT INTO users (username, email, bio, picture, password, is_admin) VALUES 
('admin', 'admin@example.com', '', NULL, '$2y$12$lRGpE/AHZZ3WSHyXc4KC.engFmMm24lqT3LxJlO2OVfujxWFzrZAa', 1),
('user', 'user@example.com', '', NULL, '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('mario_rossi', 'mario.rossi@email.it', '', NULL, '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('luigi_verdi', 'luigi.verdi@test.com', '', NULL, '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('giulia_bianchi', 'giulia.b@example.org', '', NULL, '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('francesca_neri', 'fra.neri@provider.net', '', NULL, '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('alessandro_sartori', 'alex.sartori@web.com', '', NULL, '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0), 
('sofia_galli', 'sofia.galli@cinema.it', '', NULL, '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('barman_pro', 'mixologist@drinks.com', '', NULL, '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('laura_monti', 'laura.monti@music.it', '', NULL, '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('valerio_romano', 'v.romano@motogp.com', '', NULL, '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('dario_esposito', 'dario.esposito@cucina.it', '', NULL, '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('critico_gastronomico', 'recensioni@foodblog.com', '', NULL, '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('studente_universitario', 'studente@uni.it', '', NULL, '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0),
('party_planner', 'feste@events.com', '', NULL, '$2y$12$0T0GjlFAzcJLy0xBOYQZG.cG1Xj4Ru6UyDSFTOTegAoYkw3vD.J6u', 0);

INSERT INTO categories (name, poster) VALUES 
('Felici', 'img/categorie/1.png'),
('Classici', 'img/categorie/2.png'),
('Arrabbiati', 'img/categorie/3.png'),
('Playlist', 'img/categorie/4.png'),
('Festa', 'img/categorie/5.png'),
('Assurdi', 'img/categorie/6.png');

INSERT INTO drinks (name, description, poster, creator_id, category_id) VALUES
-- CAT 1: DRINK FELICI (ID 1)
('Pina Colada', 'Ananas, cocco e rum: un sorso e sei subito in vacanza col sorriso.', 'img/drink/Pina_Colada.png', 11, 1),
('Aperol Spritz', 'Arancione come il sole al tramonto, porta allegria immediata.', 'img/drink/Aperol_Spritz.png', 10, 1),
('Tequila Sunrise', 'I colori dell\'alba nel bicchiere per iniziare la serata con ottimismo.', 'img/drink/Tequila_Sunrise.png', 1, 1),
('Bellini', 'Prosecco e polpa di pesca fresca. Dolce, frizzante e felice.', 'img/drink/Bellini.png', 5, 1),

-- CAT 2: DRINK CLASSICI (ID 2)
('Martini Dry', 'Agitato, non mescolato. L\'eleganza fatta a bicchiere.', 'img/drink/Martini_Dry.png', 2, 2),
('Gin Tonic', 'Poche chiacchiere, tanta classe. Il drink che non delude mai.', 'img/drink/Gin_Tonic.png', 1, 2),
('Negroni', 'Il re dell\'aperitivo italiano: Gin, Vermouth e Campari in parti uguali.', 'img/drink/Negroni.png', 3, 2),
('Manhattan', 'Whisky e Vermouth rosso, un gusto deciso che attraversa i secoli.', 'img/drink/Manhattan.png', 4, 2),

-- CAT 3: DRINK ARRABBIATI (ID 3)
('Angelo Azzurro', 'Gin e Cointreau per una gradazione che non perdona. Fortissimo.', 'img/drink/Angelo_Azzurro.png', 1, 3),
('Long Island Iced Tea', 'Sembra innocuo tè freddo, ma contiene 4 tipi di alcol diversi. Attento.', 'img/drink/Long_Island_Iced_Tea.png', 5, 3),
('Assenzio', 'La fata verde che ha fatto impazzire i poeti. Non per deboli di cuore.', 'img/drink/Assenzio.png', 6, 3),

-- CAT 4: DRINK PLAYLIST (ID 4)
('Whisky Sour', 'L\'equilibrio perfetto tra dolce e aspro, come un buon pezzo Jazz.', 'img/drink/Whisky_Sour.png', 7, 4),
('Irish Coffee', 'Caffè corretto Whiskey e panna. Per restare svegli ad ascoltare musica.', 'img/drink/Irish_Coffee.png', 8, 4),
('Cuba Libre', 'Rum e Cola. Il ritmo dei Caraibi nel tuo bicchiere.', 'img/drink/Cuba_Libre.png', 1, 4),

-- CAT 5: DRINK FESTA (ID 5)
('Mojito Cubano', 'Menta pestata e ghiaccio: impossibile non ballare con questo in mano.', 'img/drink/Mojito_Cubano.png', 9, 5),
('Red Bull Vodka', 'Il carburante della discoteca. Per chi vuole vedere l\'alba.', 'img/drink/Red_Bull_Vodka.png', 2, 5),
('Shot Tequila e Sale', 'Non si sorseggia, si butta giù. Il via ufficiale a ogni festa.', 'img/drink/Shot_Tequila_e_Sale.png', 12, 5),
('Caipirinha', 'Lime e zucchero di canna direttamente dal Brasile. Festa assicurata.', 'img/drink/Caipirinha.png', 13, 5),

-- CAT 6: DRINK ASSURDI (ID 6)
('Cervello di Scimmia', 'Un mix di Baileys e sciroppo che crea un effetto visivo... particolare.', 'img/drink/Cervello_di_Scimmia.png', 14, 6),
('Drink Diego', 'La leggenda narra che nessuno ricordi gli ingredienti il giorno dopo. Un mix segreto e potente.', 'img/drink/Drink_Diego.png', 8, 6),
('Bloody Mary', 'Succo di pomodoro, tabasco e vodka. Praticamente una zuppa alcolica.', 'img/drink/Bloody_Mary.png', 15, 6);

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

INSERT INTO ingredients (name, drink_id, quantity) VALUES 
-- 1. PINA COLADA
('Rum Bianco', 1, '30 ml'),
('Succo di Ananas', 1, '90 ml'),
('Latte di Cocco', 1, '30 ml'),
('Fetta di Ananas', 1, '1 fetta'),

-- 2. APEROL SPRITZ
('Aperol', 2, '60 ml'),
('Prosecco', 2, '90 ml'),
('Spruzzata di Soda', 2, '20 ml'),
('Fetta di Arancia', 2, '1 fetta'),

-- 3. TEQUILA SUNRISE
('Tequila', 3, '45 ml'),
('Succo d\'Arancia', 3, '90 ml'),
('Sciroppo di Granatina', 3, '15 ml'),
('Ciliegina', 3, '1 ciliegina'),

-- 4. BELLINI
('Prosecco', 4, '100 ml'),
('Polpa di Pesca', 4, '50 ml'),

-- 5. MARTINI DRY
('Gin', 5, '60 ml'),
('Vermouth Dry', 5, '10 ml'),
('Oliva Verde', 5, '1 oliva'),

-- 6. GIN TONIC
('Gin', 6, '40 ml'),
('Acqua Tonica', 6, '120 ml'),
('Fetta di Limone', 6, '1 fetta'),

-- 7. NEGRONI
('Gin', 7, '30 ml'),
('Vermouth Rosso', 7, '30 ml'),
('Bitter Campari', 7, '30 ml'),
('Scorza d\'Arancia', 7, '1 scorza'),

-- 8. MANHATTAN
('Rye Whisky', 8, '50 ml'),
('Vermouth Rosso', 8, '20 ml'),
('Gocce di Angostura', 8, '2 gocce'),
('Ciliegia al Maraschino', 8, '1 ciliegia'),

-- 9. ANGELO AZZURRO
('Gin', 9, '30 ml'),
('Cointreau', 9, '30 ml'),
('Blue Curacao', 9, '10 ml'),

-- 10. LONG ISLAND ICED TEA
('Vodka', 10, '15 ml'),
('Rum Bianco', 10, '15 ml'),
('Gin', 10, '15 ml'),
('Tequila', 10, '15 ml'),
('Cointreau', 10, '15 ml'),
('Cola', 10, '30 ml'),

-- 11. ASSENZIO
('Assenzio', 11, '40 ml'),
('Zolletta di Zucchero', 11, '1 zolletta'),
('Acqua Ghiacciata', 11, '40 ml'),

-- 12. WHISKY SOUR
('Bourbon Whisky', 12, '45 ml'),
('Succo di Limone', 12, '30 ml'),
('Sciroppo di Zucchero', 12, '15 ml'),
('Albume d\'uovo', 12, '1 albume'),

-- 13. IRISH COFFEE
('Whisky Irlandese', 13, '40 ml'),
('Caffè Espresso Bollente', 13, '80 ml'),
('Zucchero', 13, '1 cucchiaino'),
('Panna Fresca', 13, '30 ml'),

-- 14. CUBA LIBRE
('Rum Chiaro', 14, '50 ml'),
('Coca Cola', 14, '120 ml'),
('Succo di Lime', 14, '10 ml'),

-- 15. MOJITO CUBANO
('Rum Bianco', 15, '50 ml'),
('Zucchero di Canna', 15, '2 cucchiaini'),
('Foglie di Menta', 15, '8 foglie'),
('Spicchi di Lime', 15, '2 spicchi'),
('Acqua Frizzante', 15, '50 ml'),

-- 16. RED BULL VODKA
('Vodka', 16, '30 ml'),
('Red Bull', 16, '120 ml'),
('Ghiaccio', 16, '3 cubetti'),

-- 17. SHOT TEQUILA E SALE
('Tequila', 17, '30 ml'),
('Sale', 17, '1 pizzico'),
('Spicchio di Limone', 17, '1 spicchio'),

-- 18. CAIPIRINHA
('Cachaca', 18, '50 ml'),
('Lime a pezzi', 18, '3 pezzi'),
('Zucchero di Canna', 18, '2 cucchiaini'),

-- 19. CERVELLO DI SCIMMIA
('Vodka alla Pesca', 19, '20 ml'),
('Baileys', 19, '10 ml'),
('Gocce di Granatina', 19, '5 gocce'),

-- 20. DRINK DIEGO (Assurdo)
('Assenzio', 20, '30 ml'),
('Peperoncino Piccante', 20, '1 intero'),
('Lacrime di Studente', 20, '10 ml'),
('Ingrediente Segreto X', 20, '20 ml'),

-- 21. BLOODY MARY
('Vodka', 21, '45 ml'),
('Succo di Pomodoro', 21, '90 ml'),
('Tabasco', 21, '2 gocce'),
('Gambo di Sedano', 21, '1 gambo');

INSERT INTO steps (drink_id, num, description) VALUES
-- 1. PINA COLADA
(1, 1, 'Inserisci il ghiaccio, il rum, il succo d\'ananas e il latte di cocco nel frullatore.'),
(1, 2, 'Frulla ad alta velocità finché il composto non diventa cremoso (circa 15 secondi).'),
(1, 3, 'Versa in un bicchiere alto e decora con la fetta d\'ananas.'),

-- 2. APEROL SPRITZ
(2, 1, 'Riempi un calice da vino con abbondante ghiaccio.'),
(2, 2, 'Versa prima il Prosecco, poi aggiungi l\'Aperol (per evitare che si depositi sul fondo).'),
(2, 3, 'Completa con una spruzzata di soda e guarnisci con la fetta d\'arancia.'),

-- 3. TEQUILA SUNRISE
(3, 1, 'Metti i cubetti di ghiaccio in un bicchiere alto.'),
(3, 2, 'Versa la tequila e il succo d\'arancia, poi mescola delicatamente.'),
(3, 3, 'Versa lo sciroppo di granatina molto lentamente: scenderà sul fondo creando l\'effetto "alba". Non mescolare!'),

-- 4. BELLINI
(4, 1, 'Versa la polpa di pesca fredda direttamente nel flûte.'),
(4, 2, 'Aggiungi il Prosecco lentamente per non far perdere il gas.'),
(4, 3, 'Mescola delicatamente dal basso verso l\'alto.'),

-- 5. MARTINI DRY
(5, 1, 'Raffredda una coppa Martini con del ghiaccio (poi buttalo).'),
(5, 2, 'Riempi un mixing glass con ghiaccio, versa Gin e Vermouth e mescola (o agita se preferisci alla James Bond).'),
(5, 3, 'Filtra il tutto nella coppa fredda e aggiungi l\'oliva.'),

-- 6. GIN TONIC
(6, 1, 'Riempi il bicchiere di ghiaccio fino all\'orlo.'),
(6, 2, 'Versa il Gin e passa la fetta di limone sul bordo del bicchiere prima di inserirla.'),
(6, 3, 'Aggiungi l\'acqua tonica e mescola appena appena.'),

-- 7. NEGRONI
(7, 1, 'Metti il ghiaccio in un bicchiere basso.'),
(7, 2, 'Versa Gin, Vermouth Rosso e Bitter Campari in parti uguali.'),
(7, 3, 'Mescola bene e strizza la scorza d\'arancia sopra il drink per sprigionare gli oli essenziali.'),

-- 8. MANHATTAN
(8, 1, 'Metti del ghiaccio nel mixing glass.'),
(8, 2, 'Versa il Whisky, il Vermouth e l\'Angostura. Mescola delicatamente per 30 secondi.'),
(8, 3, 'Filtra nella coppa e aggiungi la ciliegina al maraschino.'),

-- 9. ANGELO AZZURRO
(9, 1, 'Riempi lo shaker di ghiaccio.'),
(9, 2, 'Versa Gin, Cointreau e Blue Curacao.'),
(9, 3, 'Agita energicamente e filtra nella coppa cocktail.'),

-- 10. LONG ISLAND ICED TEA
(10, 1, 'Metti il ghiaccio in un bicchiere alto e versa tutti gli alcolici (Vodka, Rum, Gin, Tequila, Cointreau).'),
(10, 2, 'Aggiungi il succo di limone e la Cola.'),
(10, 3, 'Mescola delicatamente per amalgamare.'),

-- 11. ASSENZIO
(11, 1, 'Versa l\'assenzio nel bicchiere.'),
(11, 2, 'Appoggia l\'apposito cucchiaino forato sopra il bicchiere con la zolletta di zucchero.'),
(11, 3, 'Versa l\'acqua ghiacciata goccia a goccia sullo zucchero finché non si scioglie e intorbidisce l\'assenzio.'),

-- 12. WHISKY SOUR
(12, 1, 'Versa Whisky, limone, zucchero e albume nello shaker SENZA ghiaccio.'),
(12, 2, 'Agita fortissimo (Dry Shake) per montare l\'albume a neve.'),
(12, 3, 'Aggiungi il ghiaccio, agita di nuovo per raffreddare e filtra nel bicchiere.'),

-- 13. IRISH COFFEE
(13, 1, 'Scalda il bicchiere con acqua bollente, poi svuotalo.'),
(13, 2, 'Sciogli lo zucchero nel caffè caldo e aggiungi il Whisky.'),
(13, 3, 'Versa la panna leggermente montata facendola scorrere sul dorso di un cucchiaino (deve galleggiare, non mescolarsi!).'),

-- 14. CUBA LIBRE
(14, 1, 'Spremi il succo di mezzo lime dentro un bicchiere alto e lascia cadere la scorza dentro.'),
(14, 2, 'Riempi di ghiaccio e versa il Rum.'),
(14, 3, 'Aggiungi la Coca Cola e mescola.'),

-- 15. MOJITO CUBANO
(15, 1, 'Metti zucchero, foglie di menta e succo di lime nel bicchiere. Pesta delicatamente (non distruggere la menta!).'),
(15, 2, 'Riempi il bicchiere di ghiaccio tritato e aggiungi il Rum.'),
(15, 3, 'Colma con acqua frizzante e mescola dal basso verso l\'alto.'),

-- 16. RED BULL VODKA
(16, 1, 'Metti il ghiaccio nel bicchiere.'),
(16, 2, 'Versa la Vodka.'),
(16, 3, 'Aggiungi la Red Bull fino a riempire e servi.'),

-- 17. SHOT TEQUILA E SALE
(17, 1, 'Versa la tequila nel bicchierino (chupito).'),
(17, 2, 'Metti il sale sul dorso della mano tra pollice e indice.'),
(17, 3, 'Il rito: lecca il sale, bevi la tequila d\'un fiato, mordi il limone.'),

-- 18. CAIPIRINHA
(18, 1, 'Taglia il lime a cubetti e mettilo nel bicchiere con lo zucchero.'),
(18, 2, 'Pesta energicamente per estrarre il succo.'),
(18, 3, 'Aggiungi ghiaccio tritato fino all\'orlo e versa la Cachaca. Mescola.'),

-- 19. CERVELLO DI SCIMMIA
(19, 1, 'Versa la Vodka alla pesca nel bicchierino.'),
(19, 2, 'Versa il Baileys molto lentamente (meglio se usando un cucchiaino): a contatto con la vodka si addenserà creando filamenti.'),
(19, 3, 'Aggiungi poche gocce di granatina per l\'effetto "sangue". Bevi tutto d\'un fiato.'),

-- 20. DRINK DIEGO (Assurdo)
(20, 1, 'Versa l\'assenzio nel bicchiere e accendilo con un accendino (flambé) per pochi secondi. Spegni la fiamma.'),
(20, 2, 'Aggiungi il peperoncino e l\'ingrediente segreto mescolando in senso antiorario.'),
(20, 3, 'Bevi pensando intensamente a un esame passato. L\'effetto è immediato.'),

-- 21. BLOODY MARY
(21, 1, 'Metti il ghiaccio nello shaker con vodka, succo di pomodoro e limone.'),
(21, 2, 'Aggiungi tabasco e salsa Worcester a piacere. Agita delicatamente.'),
(21, 3, 'Filtra nel bicchiere alto e decora con il gambo di sedano.');

INSERT INTO users_fav_drinks (user_id, drink_id) VALUES
(1, 7),
(1, 13),
(1, 2),
(2, 1),
(2, 6),
(2, 15),
(3, 9),
(3, 11),
(4, 10),
(4, 16),
(4, 17),
(5, 4),
(5, 5),
(8, 20),
(8, 12),
(10, 3),
(12, 21), 
(14, 19), 
(15, 14), 
(6, 6),   
(9, 15);  
