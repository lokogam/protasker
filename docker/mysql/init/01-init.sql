-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS protasker;

-- Usar la base de datos
USE protasker;

-- Configuraciones adicionales
SET GLOBAL sql_mode = '';
SET SESSION sql_mode = '';

-- Configurar charset
ALTER DATABASE protasker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;