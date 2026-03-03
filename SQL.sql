CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    rol ENUM('estudiante','administrador') NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE preguntas (
    id_pregunta INT AUTO_INCREMENT PRIMARY KEY,
    materia VARCHAR(100),
    enunciado TEXT,
    opcion_a TEXT,
    opcion_b TEXT,
    opcion_c TEXT,
    opcion_d TEXT,
    respuesta_correcta CHAR(1),
    dificultad ENUM('facil','medio','dificil'),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE examenes (
    id_examen INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    fecha_inicio DATETIME,
    fecha_fin DATETIME,
    tiempo_total INT NULL,
    puntaje DECIMAL(5,2),
    total_preguntas INT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

CREATE TABLE respuestas_estudiantes (
    id_respuesta INT AUTO_INCREMENT PRIMARY KEY,
    id_examen INT,
    id_pregunta INT,
    respuesta_seleccionada CHAR(1),
    es_correcta BOOLEAN,
    tiempo_respuesta INT,
    FOREIGN KEY (id_examen) REFERENCES examenes(id_examen),
    FOREIGN KEY (id_pregunta) REFERENCES preguntas(id_pregunta)
);

-- ALTER USER 'root'@'localhost' IDENTIFIED BY 'desarrollo2026';
admin@tusistema.com
Admin123