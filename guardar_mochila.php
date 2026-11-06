<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $marca = $_POST['marca'];
    $precio = $_POST['precio'];
    $capacidad = $_POST['capacidad'];
    $imagen_url = $_POST['imagen_url'];

    $stmt = $conexion->prepare("INSERT INTO productos (nombre, marca, precio, capacidad_litros, imagen_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdis", $nombre, $marca, $precio, $capacidad, $imagen_url);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();
}
?>