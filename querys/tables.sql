CREATE DATABASE `queryTestIntranet` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `queryTestIntranet`.`persona` (
    `id_persona`    INT( 10 ) NOT NULL AUTO_INCREMENT,
    `nombre`        VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
    `apellido_pat`  VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
    `apellido_mat`  VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
    `edad`          INT( 10 ) NULL ,
    `escolaridad`   VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
    `ocupacion`     VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
    `correo`        VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
    `distrito`      VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
    `semblanza`     VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
    `foto`          VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
    `id_metadata`   INT( 10 ) NOT NULL ,
    `id_cargo`      INT( 10 ) NOT NULL ,
    `id_eleccion`   INT( 10 ) NOT NULL ,
    `id_circu`      INT( 10 ) NOT NULL ,
    `id_estado`     INT( 10 ) NOT NULL ,
    `id_comision`   INT( 10 ) NOT NULL ,
    `id_area`       INT( 10 ) NOT NULL ,
    PRIMARY KEY ( `id_persona` )) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `queryTestIntranet`.`metadata` (
    `id_metadata`   INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `genero`        VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
    `tipo_vendedor` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `queryTestIntranet`.`cargo` (
    `id_cargo`      INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `cargo`         VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `queryTestIntranet`.`eleccion` (
    `id_eleccion`   INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `eleccion`      VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `queryTestIntranet`.`circu` (
    `id_circu`      INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `circu`         VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `queryTestIntranet`.`estado` (
    `id_estado`     INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `estado`        VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `queryTestIntranet`.`comision` (
    `id_comision`   INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `comision`      VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `queryTestIntranet`.`area` (
    `id_area`       INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `area`          VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO `queryTestIntranet`.`persona` (
    `id_persona` ,
    `nombre` , 
    `apellido_pat` , 
    `apellido_mat` , 
    `edad` ,
    `escolaridad` ,
    `ocupacion` ,
    `correo` ,
    `distrito` ,
    `semblanza` ,
    `foto` , 
    `id_metadata` ,
    `id_cargo` ,
    `id_eleccion` ,
    `id_circu` ,
    `id_estado` ,
    `id_comision` ,
    `id_area` 
)
VALUES (
    NULL , 'persona1' , 'apellido1' , 'apellido2' , 10 , 'escolaridad1' , 'ocupación1' , 'correo@correo1.com' , 'distrito1' , 'semblanza1' , '/foto1.png' , 1 , 1 , 1 , 1 , 1 , 1 , 1 
), (
    NULL , 'persona2' , 'apellido3' , 'apellido4' , 20 , 'escolaridad2' , 'ocupación2' , 'correo@correo2.com' , 'distrito2' , 'semblanza2' , '/foto2.png' , 2 , 1 , 2 , 1 , 2 , 1 , 2 
), (
    NULL , 'persona3' , 'apellido5' , 'apellido6' , 30 , 'escolaridad3' , 'ocupación3' , 'correo@correo3.com' , 'distrito3' , 'semblanza3' , '/foto3.png' , 1 , 2 , 1 , 2 , 1 , 2 , 1 
), (
    NULL , 'persona4' , 'apellido7' , 'apellido8' , 40 , 'escolaridad4' , 'ocupación4' , 'correo@correo4.com' , 'distrito4' , 'semblanza4' , '/foto4.png' , 1 , 2 , 3 , 4 , 5 , 4 , 3 
), (
    NULL , 'persona5' , 'apellido9' , 'apellido0' , 50 , 'escolaridad5' , 'ocupación5' , 'correo@correo5.com' , 'distrito5' , 'semblanza5' , '/foto5.png' , 1 , 2 , 3 , 4 , 3 , 2 , 1
);

INSERT INTO `queryTestIntranet`.`metadata` (
    `id_metadata` ,
    `genero` , 
    `tipo_vendedor` 
)
VALUES (
    NULL , 'metadata1' , 'vendedor1' 
), (
    NULL , 'metadata2' , 'vendedor2' 
), (
    NULL , 'metadata3' , 'vendedor3' 
), (
    NULL , 'metadata4' , 'vendedor4' 
), (
    NULL , 'metadata5' , 'vendedor5' 
);

INSERT INTO `queryTestIntranet`.`cargo` (
    `id_cargo` ,
    `cargo`
)
VALUES (
    NULL , 'Mesa Directiva'
), (
    NULL , 'Integrante'
);

INSERT INTO `queryTestIntranet`.`eleccion` (
    `id_eleccion` ,
    `eleccion`
)
VALUES (
    NULL , 'eleccion1'
), (
    NULL , 'eleccion2'
), (
    NULL , 'eleccion3'
), (
    NULL , 'eleccion4'
), (
    NULL , 'eleccion5'
);

INSERT INTO `queryTestIntranet`.`circu` (
    `id_circu` ,
    `circu`
)
VALUES (
    NULL , 'circunscripcion1'
), (
    NULL , 'circunscripcion2'
), (
    NULL , 'circunscripcion3'
), (
    NULL , 'circunscripcion4'
), (
    NULL , 'circunscripcion5'
);

INSERT INTO `queryTestIntranet`.`estado` (
    `id_estado` ,
    `estado`
)
VALUES 
( NULL , 'Aguascalientes' ), 
( NULL , 'Baja California Norte' ), 
( NULL , 'Baja California Sur' ), 
( NULL , 'Campeche' ), 
( NULL , 'Chiapas' ), 
( NULL , 'Chihuahua' ), 
( NULL , 'Coahuila' ), 
( NULL , 'Colima' ), 
( NULL , 'Distrito Federal' ), 
( NULL , 'Durango' ), 
( NULL , 'Guanajuato' ), 
( NULL , 'Guerrero' ), 
( NULL , 'Hidalgo' ), 
( NULL , 'Jalisco' ), 
( NULL , 'Mexico' ), 
( NULL , 'Michoacan' ), 
( NULL , 'Morelos' ), 
( NULL , 'Nayarit' ), 
( NULL , 'Nuevo Leon' ), 
( NULL , 'Oaxaca' ), 
( NULL , 'Puebla' ), 
( NULL , 'Queretaro' ), 
( NULL , 'Quintana Roo' ), 
( NULL , 'San Luis Potosi' ), 
( NULL , 'Sinaloa' ), 
( NULL , 'Sonora' ), 
( NULL , 'Tabasco' ), 
( NULL , 'Tamaulipas' ), 
( NULL , 'Tlaxcala' ), 
( NULL , 'Veracruz' ), 
( NULL , 'Yucatan' ), 
( NULL , 'Zacatecas' );

INSERT INTO `queryTestIntranet`.`comision` (
    `id_comision` ,
    `comision`
)
VALUES (
    NULL , 'Comisión 1'
), (
    NULL , 'Comisión 2'
), ( 
    NULL , 'Comisión 3'
), ( 
    NULL , 'Comisión 4' 
), ( 
    NULL , 'Comisión 5' 
);


INSERT INTO `queryTestIntranet`.`area` (
    `id_area` ,
    `area`
)
VALUES (
    NULL , 'Area_1'
), (
    NULL , 'Area_2'
), (
    NULL , 'Area_3'
), (
    NULL , 'Area_4'
), (
    NULL , 'Area_5'
);