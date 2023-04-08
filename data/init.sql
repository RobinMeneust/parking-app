CREATE DATABASE IF NOT EXISTS usersdata;
USE usersdata;

CREATE TABLE IF NOT EXISTS Address(
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
	FOREIGN KEY (idAddress) REFERENCES Address(idAddress)
);

CREATE TABLE IF NOT EXISTS Parking(
	idParking INT PRIMARY KEY,
    price DECIMAL(5,2),
	idAddress INT,
	FOREIGN KEY (idAddress) REFERENCES Address(idAddress)
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