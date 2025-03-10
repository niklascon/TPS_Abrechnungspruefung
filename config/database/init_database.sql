-- Check whether the rent_accounting schema exists and delete it
DROP SCHEMA IF EXISTS rent_accounting;

-- new scheme for the rent_accounting
CREATE SCHEMA rent_accounting
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_general_ci;
Use rent_accounting;

-- table: real_estate
CREATE TABLE real_estate (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(45)
);

-- table: bill
CREATE TABLE bill (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200),
    sum INT,
    year INT,
    fk_real_estate INT,
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_bill_real_estate FOREIGN KEY (fk_real_estate) REFERENCES real_estate(id) ON DELETE CASCADE
);

-- table: booking_type
CREATE TABLE booking_type (
    id INT AUTO_INCREMENT PRIMARY KEY,
    short_name VARCHAR(45),
    description VARCHAR(200),
    fk_parent_id INT,
    CONSTRAINT fk_booking_type_parent FOREIGN KEY (fk_parent_id) REFERENCES booking_type(id) ON DELETE CASCADE
);

-- table: line_item
CREATE TABLE line_item (
    id INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(100),
    price FLOAT,
    fk_bill INT NOT NULL,
    fk_booking_type INT NOT NULL,
    CONSTRAINT fk_line_item_bill FOREIGN KEY (fk_bill) REFERENCES bill(id) ON DELETE CASCADE,
    CONSTRAINT fk_line_item_booking_type FOREIGN KEY (fk_booking_type) REFERENCES booking_type(id) ON DELETE CASCADE
);

-- table: user
CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),
    email VARCHAR(255),
    create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- table: user_real_estate
CREATE TABLE user_real_estate (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fk_user INT NOT NULL,
    fk_real_estate INT NOT NULL,
    CONSTRAINT fk_user_real_estate_user FOREIGN KEY (fk_user) REFERENCES user(id) ON DELETE CASCADE,
    CONSTRAINT fk_user_real_estate_real_estate FOREIGN KEY (fk_real_estate) REFERENCES real_estate(id) ON DELETE CASCADE
);

-- table: text_bracket
CREATE TABLE text_bracket (
    id INT AUTO_INCREMENT PRIMARY KEY,
    text_bracket VARCHAR(255),
    short_description VARCHAR(200)
);

-- table: analysis_result
CREATE TABLE analysis_result (
    id INT AUTO_INCREMENT PRIMARY KEY,
    price_development VARCHAR(255),
    fk_real_estate INT NOT NULL,
    fk_text_bracket INT NOT NULL,
    CONSTRAINT fk_analysis_result_real_estate FOREIGN KEY (fk_real_estate) REFERENCES real_estate(id) ON DELETE CASCADE,
    CONSTRAINT fk_analysis_result_text_bracket FOREIGN KEY (fk_text_bracket) REFERENCES text_bracket(id) ON DELETE CASCADE
);

-- table: items_of_analysis
CREATE TABLE item_of_analysis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fk_line_item INT NOT NULL,
    fk_analysis INT NOT NULL,
    CONSTRAINT fk_item_of_analysis_line_item FOREIGN KEY (fk_line_item) REFERENCES line_item(id) ON DELETE CASCADE,
    CONSTRAINT fk_item_of_analysis_analysis FOREIGN KEY (fk_analysis) REFERENCES analysis_result(id) ON DELETE CASCADE
);



-- create data for the tables

-- insert into booking_type
-- INSERT INTO booking_type (short_name, description, fk_parent_id) VALUES ('Ausgaben', NULL, NULL);

INSERT INTO booking_type (ID, short_name, description, fk_parent_id) VALUES
    /* TODO do we leave it like this with short_name and description or change it to only description*/
    (1, 'Ausgaben', NULL, NULL),
    (2, 'Einnahmen', NULL, NULL),
    (3, 'Betriebskosten umlagefähig', NULL, 1),
    (4, 'Betriebskosten nicht umlagefähig', NULL, 1),

    -- hardcoded version, explicitly for our documents
    -- umlagefähig
    (5, 'Straßenreinigung', NULL, 3),
    (6, 'Müllabfuhr', NULL, 3),
    (7, 'Hausreinigung', NULL, 3),
    (8, 'Winterdienst', NULL, 3),
    (9, 'Gartenpflege', NULL, 3),
    (10, 'Allgemeinstrom', NULL, 3),
    (11, 'Kleinreparaturen', NULL, 3),
    (12, 'Wartung Rauchmelder', NULL, 3),
    (13, 'Wasserversorgung', NULL, 3),
    (14, 'Gebäude-Versicherungen', NULL, 3),
    -- nicht umlagefähig
    (15, 'Instandhaltung Gebäude', NULL, 4),
    (16, 'Verwaltungskosten', NULL, 4),
    (17, 'Kontoführung', NULL, 4);


    -- Nicos Version
    /*(1, 'Ausgaben', NULL, NULL),
    (2, 'Einnahmen', NULL, NULL),
    (3, 'Umlagefähige Kosten', NULL, 1),
    (4, 'Nicht umlagefähige Kosten', NULL, 1),
    (5, 'Wasser', NULL, 3),
    (6,'Frischwasser',NULL, 5),
    (7,'Abwasser',NULL,5),
    (8,'Grauwasser',NULL,5),
    (9, 'Heizung', NULL, 3),
    (10, 'Strom', NULL, 3),
    (11, 'Betriebskosten', NULL, 3),
    (12,'Haushaltsnahe Dienstleistung',NULL,11),
    (13,'Straßen und Winterdienst', NULL, 11),
    (14,'Müllabfuhr',NULL,11),
    (15, 'Versicherungen', NULL, 3),
    (16, 'Feuerschutz Versicherung', NULL, 15),
    (17, 'Leitungsschutzversicherung', NULL,15);*/


-- test data

-- Insert into user
INSERT INTO user (username, email) VALUES
    ('MaxMustermann', 'max.mustermann@example.com'),
    ('ErikaMustermann', 'erika.mustermann@example.com'),
    ('HansMeier', 'hans.meier@example.com');

-- Insert into real_estate
INSERT INTO real_estate (name) VALUES
    ('Haus am See'),
    ('Wohnung 2'),
    ('Haus 1');

-- Insert into user_real_estate
INSERT INTO user_real_estate (fk_user, fk_real_estate) VALUES
    (1, 1),
    (2, 2),
    (3, 3),
    (1, 3);

-- Insert into bill
INSERT INTO bill (name, sum, year, fk_real_estate) VALUES
    ('Mietabrechnung1', 902, 2023, 1), -- real_estate 1
    ('Mietabrechnung2', 1130, 2022, 1), -- real_estate 1
    ('Mietabrechnung3', 400, 2022, 2), -- real_estate 2
    ('Mietabrechnung4', 900, 2021, 2); -- real_estate 2

-- Insert into line_item
INSERT INTO line_item (description, price, fk_bill, fk_booking_type) VALUES
    ('Frischwasser Kosten', 300, 1, 6),
    ('Abwasser Kosten', 200, 1, 7),
    ('Straßen und Winterdienst', 123, 1, 13),
    ('Müllabfuhr', 259, 1, 14),
    ('Frischwasser Kosten', 230, 2, 6),
    ('Abwasser Kosten', 500, 2, 7),
    ('Straßen und Winterdienst', 150, 2, 13),
    ('Müllabfuhr', 250, 2, 14),
    ('Strom', 400, 3, 10),
    ('Umlagefaehig Summe', 1032, 1, 3),
    ('Betriebskosten Zwischensumme', 382, 1, 11),
    ('Ausgaben Gesamtsumme', 1052, 1, 1),
    ('Umlagefaehig Summe', 1180, 2, 3),
    ('Betriebskosten Zwischensumme', 400, 2, 11),
    ('Ausgaben Gesamtsumme', 1180, 2, 1),
    ('Umlagefaehig Summe', 400, 3, 3),
    ('Ausgaben Gesamtsumme', 400, 3, 1),
    ('Wasser Kosten', 500, 1, 5),
    ('Wasser Kosten', 730, 2, 5),
    ('Kleinreparatur', 20, 1, 4),
    ('Strom', 900, 4, 10),
    ('Umlagefaehig Summe', 900, 4, 3),
    ('Ausgaben Gesamtsumme', 900, 4, 1),
    ('Gebäudeversicherung', 150, 1, 15),
    ('Gebäudeversicherung', 50, 2, 15);

-- Insert into text_bracket
INSERT INTO text_bracket (text_bracket, short_description) VALUES
    ('Starke Abweichung', 'Achtung, das scheint sehr verdächtig'),
    ('Ungewöhnlich', 'Achtung, das scheint nach einem ungewöhnlich hohen Stromverbrauch'),
    ('Anwalt', 'Besorg dir einen Anwalt!');

-- Insert into analysis_result
INSERT INTO analysis_result (price_development, fk_real_estate, fk_text_bracket) VALUES
    ('Erhöhter Abwasserverbrauch', 1, 1),
    ('Erhöhter Stromverbrauch', 2, 1),
    ('sehr hoher Stromverbrauch', 2, 3),
    ('Erhöhte Versicherungskosten', 1, 1);

-- Insert into item_of_analysis
INSERT INTO item_of_analysis (fk_line_item, fk_analysis) VALUES
    (6, 1),
    (2, 1),
    (21, 2),
    (21, 2),
    (21,3),
    (24,4),
    (25,4);