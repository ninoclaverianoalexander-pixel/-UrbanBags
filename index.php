<?php
include 'conexion.php';

$sql = "SELECT * FROM productos";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UrbanBags - Tienda Oficial</title>
    <!-- Importamos una tipografía moderna y amigable -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { margin: 0; background-color: #f8fafc; color: #1e293b; }
        
        /* Header amigable y limpio */
        header { background-color: #ffffff; padding: 18px 5%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); position: sticky; top: 0; z-index: 100; }
        .logo { font-size: 1.6rem; font-weight: 700; color: #2563eb; text-decoration: none; display: flex; align-items: center; gap: 8px; }
        .btn-admin { background-color: #f1f5f9; color: #334155; padding: 8px 16px; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 0.85rem; transition: all 0.2s; border: 1px solid #e2e8f0; }
        .btn-admin:hover { background-color: #e2e8f0; color: #0f172a; }

        /* Banner Principal con estilo amigable */
        .hero { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #ffffff; text-align: center; padding: 70px 20px; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; margin-bottom: 30px; }
        .hero h1 { font-size: 2.3rem; margin: 0 0 10px 0; font-weight: 700; }
        .hero p { color: #94a3b8; font-size: 1rem; margin: 0; font-weight: 300; }

        /* Catálogo */
        .container { padding: 20px 5% 50px 5%; max-width: 1200px; margin: 0 auto; }
        .section-title { font-size: 1.4rem; font-weight: 600; margin-bottom: 25px; color: #0f172a; display: flex; align-items: center; gap: 10px; }
        
        .grid-productos { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 25px; }
        
        /* Tarjetas de Producto suaves */
        .card-producto { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.2s ease, box-shadow 0.2s ease; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        .card-producto:hover { transform: translateY(-5px); box-shadow: 0 12px 20px -3px rgba(0,0,0,0.08); }
        
        .imagen-box { width: 100%; height: 210px; background-color: #f1f5f9; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative; }
        .imagen-box img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
        .card-producto:hover .imagen-box img { transform: scale(1.05); }
        .imagen-placeholder { color: #94a3b8; font-size: 0.9rem; font-weight: 500; text-align: center; padding: 10px; }

        .info-producto { padding: 18px; flex-grow: 1; }
        .nombre-producto { font-size: 1rem; font-weight: 600; color: #0f172a; margin: 0 0 8px 0; }
        .meta-producto { font-size: 0.82rem; color: #64748b; margin: 3px 0; }
        
        .footer-card { padding: 15px 18px; background-color: #fafafa; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
        .precio { font-size: 1.2rem; font-weight: 700; color: #059669; }
        .btn-comprar { background-color: #2563eb; color: #ffffff; border: none; padding: 9px 18px; border-radius: 50px; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: background 0.2s; box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2); }
        .btn-comprar:hover { background-color: #1d4ed8; }
    </style>
</head>
<body>

    <header>
        <a href="index.php" class="logo">🎒 UrbanBags</a>
        <a href="login.php" class="btn-admin">⚙️ Panel Admin</a>
    </header>

    <section class="hero">
        <h1>Mochilas Urbanas de Alta Gama</h1>
        <p>Diseño, comodidad y seguridad para tu día a día en la ciudad.</p>
    </section>

    <main class="container">
        <h2 class="section-title">🛍️ Catálogo de Mochilas Disponibles</h2>

        <div class="grid-productos">
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while($row = $resultado->fetch_assoc()): ?>
                    <?php 
                        // Detección automática de columnas
                        $img = $row['imagen'] ?? ($row['foto'] ?? ($row['img'] ?? ''));
                        $marca = $row['marca'] ?? ($row['categoria'] ?? 'N/A');
                        $raw_cap = $row['capacidad'] ?? ($row['litros'] ?? ($row['volumen'] ?? ($row['stock'] ?? '')));
                        $capacidad = ($raw_cap !== '') ? $raw_cap . (is_numeric($raw_cap) ? ' L' : '') : 'N/A';
                    ?>
                    <div class="card-producto">
                        <div>
                            <div class="imagen-box">
                                <?php if(!empty($img) && file_exists("uploads/" . $img)): ?>
                                    <img src="uploads/<?php echo $img; ?>" alt="<?php echo $row['nombre']; ?>">
                                <?php else: ?>
                                    <div class="imagen-placeholder">Sin imagen</div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="info-producto">
                                <h3 class="nombre-producto"><?php echo $row['nombre']; ?></h3>
                                <p class="meta-producto"><strong>Marca:</strong> <?php echo $marca; ?></p>
                                <p class="meta-producto"><strong>Capacidad:</strong> <?php echo $capacidad; ?></p>
                            </div>
                        </div>

                        <div class="footer-card">
                            <span class="precio">S/ <?php echo number_format($row['precio'], 2); ?></span>
                            <button class="btn-comprar" onclick="alert('¡Gracias por tu interés! Producto añadido al carrito de compras de UrbanBags por S/ <?php echo number_format($row['precio'], 2); ?>.')">Comprar</button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No hay mochilas registradas en el catálogo actual.</p>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>