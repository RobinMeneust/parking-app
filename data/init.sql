CREATE DATABASE IF NOT EXISTS usersdata;
USE usersdata;

CREATE TABLE IF NOT EXISTS Addresses(
	idAddress INT PRIMARY KEY,
	houseNumber VARCHAR(10),
	street VARCHAR(100),
	city VARCHAR(100),
	country VARCHAR(100),
	postalCode VARCHAR(100),
	lat DECIMAL(15,12) NOT NULL,
	lng DECIMAL(15,12) NOT NULL
);

CREATE TABLE IF NOT EXISTS Users (
	idUser INT PRIMARY KEY,
    lastName VARCHAR(100),
    firstName VARCHAR(100),
    passwd VARCHAR(100) NOT NULL,
    mail VARCHAR(100) UNIQUE NOT NULL,
    agreement BOOLEAN,
	idAddress INT,
	FOREIGN KEY (idAddress) REFERENCES Addresses(idAddress)
);

CREATE TABLE IF NOT EXISTS Parking(
	idParking INT PRIMARY KEY,
	idAddress INT,
	name VARCHAR(100),
	FOREIGN KEY (idAddress) REFERENCES Addresses(idAddress)
);

CREATE TABLE IF NOT EXISTS ParkingVisite(
	idUser INT,
	idParking INT,
	dateVisited DATE,
	expenses DECIMAL(5,2),
	PRIMARY KEY (idUser, idParking),
	FOREIGN KEY (idUser) REFERENCES Users(idUser),
	FOREIGN KEY (idParking) REFERENCES Parking(idParking)
);


-- Data used for testing purposes

INSERT INTO Addresses VALUES(0,'12 bis','rue du test', 'VilleTest', 'France', '99999', 0.0, 0.0);
INSERT INTO Addresses VALUES(1,'145','avenue test', 'VilleJS', 'France', '00000', 1.0, -1.0);

INSERT INTO Users VALUES(0, 'Meneust', 'Robin', '023gf46dfs41fgf54f_#(à)', "adresseDeTest@test.fr", TRUE, 0);

INSERT INTO Parking VALUES(0, 0, 'Parking du port');
INSERT INTO Parking VALUES(1, 0, 'Parking souterrain');
INSERT INTO Parking VALUES(2, 1, NULL);

INSERT INTO ParkingVisite VALUES(0, 0, '2023-04-10', 7.0);
INSERT INTO ParkingVisite VALUES(0, 0, '2018-09-24', 7.0);
INSERT INTO ParkingVisite VALUES(0, 0, '2018-09-25', 7.0);
INSERT INTO ParkingVisite VALUES(0, 1, '2019-09-24', 0.0);
INSERT INTO ParkingVisite VALUES(0, 2, '2023-04-01', 5.0);