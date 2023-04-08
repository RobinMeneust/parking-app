CREATE DATABASE IF NOT EXISTs usersdata;
USE usersdata;

CREATE TABLE users(
    nom VARCHAR(100),
    prenom VARCHAR(100), 
    mdp VARCHAR(100), 
    mail VARCHAR(100), 
    adresse VARCHAR(100), 
    code_postal VARCHAR(15), 
    agreement VARCHAR(10)
);