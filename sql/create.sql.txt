CREATE TABLE user_table (
	login TEXT PRIMARY KEY NOT NULL,
	password TEXT NOT NULL,
	name TEXT NOT NULL,
	surname TEXT NOT NULL,
	adress TEXT NOT NULL,
	phone TEXT,
	admin BOOLEAN DEFAULT false
);

CREATE TABLE videogame (
	id INTEGER PRIMARY KEY NOT NULL,
	title TEXT NOT NULL,
	price FLOAT CHECK (price >=0),
	console TEXT NOT NULL,
	amount INTEGER CHECK (amount >= 0)
);

CREATE TABLE order_table (
	id INTEGER PRIMARY KEY NOT NULL,
	date DATE NOT NULL,
	payment TEXT NOT NULL,
	userid TEXT REFERENCES user_table (login) ON UPDATE CASCADE ON DELETE CASCADE NOT NULL
);

CREATE TABLE game_order (
	gameid INTEGER REFERENCES videogame (id) NOT NULL,
	orderid INTEGER REFERENCES order_table (id) ON DELETE CASCADE NOT NULL,
	copies INTEGER NOT NULL CHECK (copies >= 1),
	PRIMARY KEY (gameid, orderid)
);