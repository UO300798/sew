CREATE DATABASE UO300798_DB COLLATE utf8_spanish_ci;

CREATE TABLE `generos` (
  `id_genero` INT AUTO_INCREMENT PRIMARY KEY,
  `descripcion` VARCHAR(50) NOT NULL
);

INSERT INTO `generos` (`descripcion`) VALUES ('Hombre'), ('Mujer'), ('Otro/No binario');


CREATE TABLE `dispositivos` (
  `id_dispositivo` INT AUTO_INCREMENT PRIMARY KEY,
  `descripcion` VARCHAR(50) NOT NULL
);

INSERT INTO `dispositivos` (`descripcion`) VALUES ('Ordenador'), ('Tableta'), ('Teléfono');


CREATE TABLE `usuarios` (
  `id_usuario` INT AUTO_INCREMENT PRIMARY KEY, 
  `profesion` VARCHAR(100) NOT NULL,
  `edad` INT NOT NULL,
  `id_genero` INT NOT NULL,                    
  `pericia_informatica` TINYINT NOT NULL,
  FOREIGN KEY (`id_genero`) REFERENCES `generos`(`id_genero`)
);


CREATE TABLE `resultados` (
  `id_resultado` INT AUTO_INCREMENT PRIMARY KEY,
  `id_usuario` INT NOT NULL,                   
  `id_dispositivo` INT NOT NULL,               
  `tiempo` INT NOT NULL,
  `completado` BOOLEAN NOT NULL,
  `respuesta1` TEXT,
  `respuesta2` TEXT,
  `respuesta3` TEXT,
  `respuesta4` TEXT,
  `respuesta5` TEXT,
  `respuesta6` TEXT,
  `respuesta7` TEXT,
  `respuesta8` TEXT,
  `respuesta9` TEXT,
  `respuesta10` TEXT,
  `comentarios` TEXT,
  `propuestas_mejora` TEXT,
  `valoracion` TINYINT NOT NULL CHECK (`valoracion` BETWEEN 0 AND 10),
  FOREIGN KEY (`id_usuario`) REFERENCES `usuarios`(`id_usuario`) ON DELETE CASCADE,
  FOREIGN KEY (`id_dispositivo`) REFERENCES `dispositivos`(`id_dispositivo`)
);


CREATE TABLE `observaciones` (
  `id_observacion` INT AUTO_INCREMENT PRIMARY KEY,
  `id_usuario` INT NOT NULL,
  `comentarios` TEXT NOT NULL,
  FOREIGN KEY (`id_usuario`) REFERENCES `usuarios`(`id_usuario`) ON DELETE CASCADE
);