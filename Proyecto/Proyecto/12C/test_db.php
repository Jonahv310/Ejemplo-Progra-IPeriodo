<?php
try {
    echo "Intentando conectar a MySQL en 127.0.0.1...\n";
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306", "root", "");
    echo "Conexion exitosa!\n";
    
    // Try to create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `12c` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Base de datos creada!\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Código: " . $e->getCode() . "\n";
    exit(1);
}
