CREATE DATABASE IF NOT EXISTS apilaravel;
USE apilaravel;

CREATE TABLE users(
id 		int(255) auto_increment not null,
email varchar(255),
role	varchar(20),
name	varchar(255),
surname	varchar(255),
password varchar(255),
created_at datetime DEFAULT NULL,
updated_at datetime DEFAULT NULL,
remember_token varchar(255),
CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE cars(
id 		int(255) auto_increment not null,
user_id int(255) not null,
title	varchar(255),
description text,
price	varchar(30),
status  varchar(30),
created_at datetime DEFAULT NULL,
updated_at datetime DEFAULT NULL,
CONSTRAINT pk_cars PRIMARY KEY(id),
CONSTRAINT fk_cars_users FOREIGN KEY(user_id) REFERENCES users(id)
)ENGINE=InnoDb;

/* ORDENES DE SERVICIO */

CREATE TABLE equipos(
 	id int(255) auto_increment noy null,
 	descripcion varchar(1024),
 	marca       varchar(255),
 	modelo      varchar(255),
 	fecha_adq	datetime,
 	serie 		varchar(255),
 	inventario 	varchar(255),
 	estatus	    char(1),
 	ubicacion 	varchar(255),
 	ubicacion_lt varchar(255),
 	ubicacion_ln varchar(255),
 	created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    constraint pk_equipos primary key(id)
)ENGINE=InnoDb;

              
 