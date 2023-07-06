<?php

// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "examenpoo";

// Obtener todos los usuarios
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Crear la conexión a la base de datos
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

    // Consulta SQL para obtener todos los usuarios
    $sql = "SELECT * FROM usuarios";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Obtener los resultados
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver los usuarios en formato JSON
    header('Content-Type: application/json');
    echo json_encode($usuarios);
}

// Obtener un usuario por su ID
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Crear la conexión a la base de datos
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

    // Consulta SQL para obtener el usuario por su ID
    $sql = "SELECT * FROM usuarios WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Obtener el resultado
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si se encontró el usuario
    if (!$usuario) {
        // Usuario no encontrado
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['error' => 'Usuario no encontrado']);
    } else {
        // Devolver el usuario en formato JSON
        header('Content-Type: application/json');
        echo json_encode($usuario);
    }
}
