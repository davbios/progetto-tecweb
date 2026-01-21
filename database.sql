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

