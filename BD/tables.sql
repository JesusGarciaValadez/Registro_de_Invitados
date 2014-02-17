CREATE DATABASE `registry_guest` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `registry_guest`.`persona` (
    `id_persona`    INT( 10 ) NOT NULL AUTO_INCREMENT,
    `mail`          VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
    `first_name`    VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
    `last_name`     VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
    `user_name`          VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
    `job`           VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
    `where_from`         VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
    `lada`          VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
    `phone`         VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
    `ext`           VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
    `dependency`    VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
    `id_title`      INT( 10 ) NOT NULL ,
    `id_state`      INT( 10 ) NOT NULL ,
    `city`          VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
    `is_completed`  BOOLEAN NOT NULL, 
    PRIMARY KEY ( `id_persona` )) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `registry_guest`.`title` (
    `id_title`  INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `title`     VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `registry_guest`.`state` (
    `id_state`  INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `state`     VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO `registry_guest`.`persona` (
    `mail`, 
    `first_name`, 
    `last_name`, 
    `user_name`, 
    `job`, 
    `where_from`, 
    `lada`, 
    `phone`, 
    `ext`,
    `dependency`, 
    `id_title`, 
    `id_state`, 
    `city`, 
    `is_completed`
) VALUES 
( 'correo@correo1.com' , 'apellido1' , 'apellido2' , 'persona1' , "job1" , 'escuela1' , 'lada1' , 'telefono1' , 'extension1' , 'dependencia1' , 1 , 1 , "ciudad1", FALSE ), 
( 'correo@correo2.com' , 'apellido3' , 'apellido4' , 'persona2' , "job2" , 'escuela2' , 'lada2' , 'telefono2' , 'extension2' , 'dependencia2' , 2 , 3 , "ciudad2", FALSE ), 
( 'correo@correo3.com' , 'apellido5' , 'apellido6' , 'persona3' , "job3" , 'escuela3' , 'lada3' , 'telefono3' , 'extension3' , 'dependencia3' , 1 , 5 , "ciudad3", FALSE ), 
( 'correo@correo4.com' , 'apellido7' , 'apellido8' , 'persona4' , "job4" , 'escuela4' , 'lada4' , 'telefono4' , 'extension4' , 'dependencia4' , 1 , 7 , "ciudad4", FALSE ), 
( 'correo@correo5.com' , 'apellido9' , 'apellido0' , 'persona5' , "job5" , 'escuela5' , 'lada5' , 'telefono5' , 'extension5' , 'dependencia5' , 1 , 9 , "ciudad5", FALSE );

INSERT INTO `registry_guest`.`title` ( `title` ) VALUES 
( 'Ing.' ), 
( 'Dr.' ), 
( 'Dra.' ), 
( 'Mtro.' ), 
( 'Mtra.' ), 
( 'CP.' ), 
( 'Lic.' );

INSERT INTO `registry_guest`.`state` ( `state` ) VALUES 
( 'Aguascalientes' ), 
( 'Baja California Norte' ), 
( 'Baja California Sur' ), 
( 'Campeche' ), 
( 'Chiapas' ), 
( 'Chihuahua' ), 
( 'Coahuila' ), 
( 'Colima' ), 
( 'Distrito Federal' ), 
( 'Durango' ), 
( 'Guanajuato' ), 
( 'Guerrero' ), 
( 'Hidalgo' ), 
( 'Jalisco' ), 
( 'Mexico' ), 
( 'Michoaáan' ), 
( 'Morelos' ), 
( 'Nayarit' ), 
( 'Nuevo Leon' ), 
( 'Oaxaca' ), 
( 'Puebla' ), 
( 'Querétaro' ), 
( 'Quintana Roo' ), 
( 'San Luis Potosi' ), 
( 'Sinaloa' ), 
( 'Sonora' ), 
( 'Tabasco' ), 
( 'Tamaulipas' ), 
( 'Tlaxcala' ), 
( 'Veracruz' ), 
( 'Yucatan' ), 
( 'Zacatecas' );