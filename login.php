<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'conexion.php';

$mensaje = "";
$error_db = "";

// 1. Lógica para eliminar un producto
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    
    $res = $conexion->query("SELECT imagen FROM productos WHERE id = $id");
    if ($res && $row = $res->fetch_assoc()) {
        if (!empty($row['imagen']) && file_exists("uploads/" . $row['imagen'])) {
            @unlink("uploads/" . $row['imagen']);
        }
    }
    
    $conexion->query("DELETE FROM productos WHERE id = $id");
    header("Location: admin.php");
    exit();
}

// 2. Lógica para guardar un nuevo producto (con imagen y stock)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar'])) {
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $marca = $conexion->real_escape_string($_POST['marca']);
    $capacidad = $conexion->real_escape_string($_POST['capacidad']);

    $imagen = "";
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $imagen = time() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", $_FILES['imagen']['name']);
        $ruta_temporal = $_FILES['imagen']['tmp_name'];
        
        if (!file_exists('uploads')) {
            mkdir('uploads', 0777, true);
        }
        
        move_uploaded_file($ruta_temporal, "uploads/" . $imagen);
    }

    $sql = "INSERT INTO productos (nombre, precio, marca, stock, imagen) VALUES ('$nombre', '$precio', '$marca', '$capacidad', '$imagen')";
    
    if ($conexion->query($sql)) {
        $mensaje = "¡Mochila agregada con éxito!";
    } else {
        $error_db = "Error al guardar en la base de datos: " . $conexion->error;
    }
}

$lista_productos = $conexion->query("SELECT * FROM productos");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - UrbanBags</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { margin: 0; background-color: #f1f5f9; color: #1e293b; display: flex; height: 100vh; }
        
        aside { width: 260px; background: #0f172a; color: white; padding: 30px 20px; display: flex; flex-direction: column; justify-content: space-between; }
        aside h2 { font-size: 1.2rem; margin-top: 0; color: #38bdf8; }
        aside a { color: #94a3b8; text-decoration: none; padding: 10px 15px; border-radius: 8px; margin: 5px 0; display: block; transition: 0.2s; }
        aside a:hover { background: #1e293b; color: white; }
        .logout { color: #f87171 !important; }

        main { flex-grow: 1; padding: 40px; overflow-y: auto; display: flex; flex-direction: column; gap: 30px; }
        
        .card-form, .card-tabla { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        h1 { margin-top: 0; font-size: 1.4rem; color: #0f172a; }
        
        label { font-size: 0.85rem; font-weight: 600; color: #475569; display: block; margin-top: 15px; }
        input[type="text"], input[type="number"], input[type="file"] { width: 100%; padding: 10px 12px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; }
        button { background: #2563eb; color: white; border: none; padding: 12px 20px; border-radius: 6px; font-weight: 600; margin-top: 20px; cursor: pointer; width: 100%; }
        button:hover { background: #1d4ed8; }
        
        .alert-success { background: #dcfce7; color: #166534; padding: 12px; border-radius: 6px; margin-bottom: 15px; font-size: 0.9rem; border: 1px solid #bbf7d0; }
        .alert-danger { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 6px; margin-bottom: 15px; font-size: 0.9rem; border: 1px solid #fecaca; }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; font-size: 0.9rem; }
        th { background-color: #f8fafc; color: #475569; font-weight: 600; }
        .btn-eliminar { background-color: #dc2626; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: 600; display: inline-block; }
        .btn-eliminar:hover { background-color: #b91c1c; }
    </style>
</head>
<body>

    <aside>
        <div>
            <h2>UrbanBags Admin</h2>
            <a href="admin.php">📦 Gestionar Productos</a>
            <a href="index.php" target="_blank">🌐 Ver Tienda Pública</a>
        </div>
        <div>
            <a href="login.php?logout=true" class="logout">🚪 Cerrar Sesión</a>
        </div>
    </aside>

    <main>
        <div class="card-form">
            <h1>➕ Registrar Nueva Mochila</h1>
            <?php if(!empty($mensaje)) echo "<div class='alert-success'>$mensaje</div>"; ?>
            <?php if(!empty($error_db)) echo "<div class='alert-danger'>$error_db</div>"; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <label>Nombre de la Mochila:</label>
                <input type="text" name="nombre" placeholder="Ej. Mochila Urban Pro" required>

                <label>Precio (en Soles S/):</label>
                <input type="number" step="0.01" name="precio" placeholder="Ej. 149.90" required>

                <label>Marca:</label>
                <input type="text" name="marca" placeholder="Ej. Caterpillar / Nordic" required>

                <label>Capacidad (Litros):</label>
                <input type="text" name="capacidad" placeholder="Ej. 20 L" required>

                <label>Imagen del Producto:</label>
                <input type="file" name="imagen" accept="image/*" required>

                <button type="submit" name="agregar">Guardar Producto en la Tienda</button>
            </form>
        </div>

        <div class="card-tabla">
            <h1>📋 Lista de Mochilas en el Catálogo</h1>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Marca</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($lista_productos && $lista_productos->num_rows > 0): ?>
                        <?php while($prod = $lista_productos->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $prod['id']; ?></td>
                                <td><strong><?php echo $prod['nombre']; ?></strong></td>
                                <td>S/ <?php echo number_format($prod['precio'], 2); ?></td>
                                <td><?php echo $prod['marca'] ?? 'N/A'; ?></td>
                                <td>
                                    <a href="admin.php?eliminar=<?php echo $prod['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar esta mochila?')">🗑️ Eliminar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #64748b;">No hay mochilas registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>