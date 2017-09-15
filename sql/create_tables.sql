CREATE TABLE Alcoholic(
	id SERIAL PRIMARY KEY,
	username varchar(30) NOT NULL,
	password varchar(50) NOT NULL
);

CREATE TABLE Ingredient(
	id SERIAL PRIMARY KEY,
	name varchar(50) NOT NULL,
	alcohol_percentage INTEGER,
	description varchar(500)
);

CREATE TABLE Drink(
	id SERIAL PRIMARY KEY,
	user_id INTEGER REFERENCES Alcoholic(id),
	name varchar(50) NOT NULL,
	volume INTEGER,
	alcohol_percentage INTEGER,
	description varchar(500)
);

CREATE TABLE Ingredient_Drink(
	ingredient_id INTEGER REFERENCES Ingredient(id),
	drink_id INTEGER REFERENCES Drink(id),
	PRIMARY KEY(ingredient_id, drink_id)
);
	

CREATE TABLE Review(
	id SERIAL PRIMARY KEY,
	user_id INTEGER REFERENCES Alcoholic(id),
	drink_id INTEGER REFERENCES Drink(id),
	description varchar(1500)
);
