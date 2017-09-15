INSERT INTO Alcoholic (username, password) VALUES ('Kalle', 'salasana');

INSERT INTO Drink (alcoholic_id, name, volume, alcohol_percentage, description) VALUES (1, 'Jallupaukku', 60, 40.5, 'Perinteinen jallupaukku');

INSERT INTO Ingredient (name, alcohol_percentage, description) VALUES ('Jaloviina', 40.5, 'Jaloviina ei ole kirkasta');

INSERT INTO Ingredient_Drink (ingredient_id, drink_id) VALUES (1, 1);

INSERT INTO Review (alcoholic_id, drink_id, rating, description) VALUES (1, 1, 4, 'Tää on hyvää');
