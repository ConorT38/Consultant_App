CREATE DATABASE admin;

USE consulter

CREATE TABLE admin(
info text,
email varchar(200),
password varchar(200),
signed_in tinyint
);

CREATE TABLE users(
info text,
email varchar(200),
password varchar(200),
signed_in tinyint,
medical_info text
);