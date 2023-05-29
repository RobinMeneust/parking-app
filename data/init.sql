CREATE DATABASE IF NOT EXISTS usersdata;
USE usersdata;

CREATE TABLE IF NOT EXISTS Addresses(
	idAddress INT NOT NULL AUTO_INCREMENT,
	houseNumber VARCHAR(10),
	street VARCHAR(100),
	city VARCHAR(100),
	country VARCHAR(100),
	postalCode INT,
	lat DECIMAL(15,12) NOT NULL,
	lng DECIMAL(15,12) NOT NULL,
	PRIMARY KEY (idAddress)
);

CREATE TABLE IF NOT EXISTS Users(
	idUser INT NOT NULL AUTO_INCREMENT,
	email VARCHAR(100) NOT NULL UNIQUE,
	lastName VARCHAR(100),
	firstName VARCHAR(100),
	passwd VARCHAR(100) NOT NULL,
	PRIMARY KEY (idUser)
);

CREATE TABLE IF NOT EXISTS Parking(
	idParking INT NOT NULL AUTO_INCREMENT,
	idAddress INT,
	name VARCHAR(100),
	PRIMARY KEY (idParking),
	FOREIGN KEY (idAddress) REFERENCES Addresses(idAddress) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS ParkingVisite(
	idVisite INT NOT NULL AUTO_INCREMENT,
	idUser INT,
	idParking INT,
	dateVisited DATE,
	expenses DECIMAL(5,2),
	PRIMARY KEY (idVisite),
	FOREIGN KEY (idUser) REFERENCES Users(idUser) ON DELETE CASCADE,
	FOREIGN KEY (idParking) REFERENCES Parking(idParking) ON DELETE CASCADE
);


-- Data used for testing purposes

INSERT INTO Addresses VALUES(NULL,'12 bis','rue du test', 'VilleTest', 'France', 99999, 0.0, 0.0);
INSERT INTO Addresses VALUES(NULL,'145','avenue test', 'VilleJS', 'France', 00000, 1.0, -1.0);

INSERT INTO Users VALUES(NULL,"a@a.fr", 'Meneust', 'Robin', '$2y$10$vHaaNO6oH26q62ZWjv/5NedtNvq9ydh1mctHvq/P27UzZlsTW3Y46');

INSERT INTO Parking VALUES(NULL, 1, 'Parking du port');
INSERT INTO Parking VALUES(NULL, 1, 'Parking souterrain');
INSERT INTO Parking VALUES(NULL, 2, NULL);

INSERT INTO ParkingVisite VALUES(NULL,1, 1, '2018-09-24', 7.0);
INSERT INTO ParkingVisite VALUES(NULL,1, 1, '2018-09-25', 7.0);
INSERT INTO ParkingVisite VALUES(NULL,1, 2, '2019-09-24', 0.0);
INSERT INTO ParkingVisite VALUES(NULL,1, 1, '2022-09-24', 7.0);
INSERT INTO ParkingVisite VALUES(NULL,1, 1, '2022-11-25', 7.0);
INSERT INTO ParkingVisite VALUES(NULL,1, 2, '2022-12-24', 0.0);
INSERT INTO ParkingVisite VALUES(NULL,1, 2, '2023-01-15', 8.0);
INSERT INTO ParkingVisite VALUES(NULL,1, 3, '2023-02-01', 5.0);
INSERT INTO ParkingVisite VALUES(NULL,1, 2, '2023-03-15', 8.0);
INSERT INTO ParkingVisite VALUES(NULL,1, 3, '2023-04-01', 5.0);
INSERT INTO ParkingVisite VALUES(NULL,1, 1, '2023-04-08', 1.5);
INSERT INTO ParkingVisite VALUES(NULL,1, 1, '2023-04-10', 7.0);
INSERT INTO ParkingVisite VALUES(NULL,1, 1, '2023-04-10', 1.5);
INSERT INTO ParkingVisite VALUES(NULL,1, 1, '2023-04-12', 7.0);
