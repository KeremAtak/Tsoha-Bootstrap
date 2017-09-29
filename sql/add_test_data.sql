INSERT INTO Alcoholic (username, password) VALUES ('Kalle', 'salasana');
INSERT INTO Alcoholic (username, password) VALUES ('Pate', 'salasana');
INSERT INTO Alcoholic (username, password) VALUES ('Esko', 'salasana');

INSERT INTO Drink (alcoholic_id, name, volume, alcohol_percentage, rating, description) VALUES (1, 'Jallupaukku', 60, 40.5, 1, 'Perinteinen jallupaukku');

INSERT INTO Ingredient (name, alcohol_percentage, description) VALUES ('Jaloviina', 40.5, 'Jaloviina ei ole kirkasta');

INSERT INTO Ingredient (name, alcohol_percentage, description) VALUES ('Minttuviina', 40, 'Ei saanu laittaa jallupaukkuun!');

INSERT INTO Ingredient (name, alcohol_percentage, description) VALUES ('Maito', 0, 'Tätä tulee lehmästä.');

INSERT INTO Ingredient_Drink (ingredient_id, drink_id) VALUES (1, 1);
INSERT INTO Ingredient_Drink (ingredient_id, drink_id) VALUES (2, 1);

INSERT INTO Review (alcoholic_id, drink_id, reviewer, rating, description) VALUES (1, 1, 'Pate', 4, 'Tää on hyvää');
