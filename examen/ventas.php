<?php
include 'conexa.php';
$pdo=new Conexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idProducto = $_POST['Id'];

    // Verificar producto existe en la tabla inventario
    $stmt = $pdo->prepare("SELECT * FROM actualizacion_inventario WHERE Id = :Id");
    $stmt->bindValue(':Id', $idProducto);
    $stmt->execute();
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        // El producto no registrado en el inventario
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(array('error' => 'El producto no esta registrado'));
        exit;
    }

    //datos del producto
  


    // cantidad de piezas a comprar
    $cantidadDeseada = $_POST['Cantidad'];

    if ($cantidadDeseada > $producto['Cantidad']) {
        // No hay cantidad disponible
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(array('error' => 'No hay suficiente cantidad disponible', 'cantidadExistente' => $producto['Cantidad']));
        exit;
    }


	$stmt = $pdo->prepare("SELECT Precio, Nombre FROM registro_productos WHERE Id = :Id");
    $stmt->bindValue(':Id', $idProducto);
    $stmt->execute();
    $registroProducto = $stmt->fetch(PDO::FETCH_ASSOC);

    $precio = $registroProducto['Precio'];
	
    $precioFinal = $precio * $cantidadDeseada;
	echo " 	La cuenta total es: " . $precioFinal;

    // Actualizar la cantidad en el inventario
    $nuevaCantidad = $producto['Cantidad'] - $cantidadDeseada;
    $stmt = $pdo->prepare("UPDATE productos_inventario SET Cantidad = :Cantidad WHERE Id = :Id");
    $stmt->bindValue(':Cantidad', $nuevaCantidad);
    $stmt->bindValue(':Id', $idProducto);
    $stmt->execute();
	
	
	$idVenta = $_POST['Id_venta']; // El ID de la venta ingresado por ti
    $nombreProducto = $registroProducto['Nombre']; // Nombre del producto

    $stmt = $pdo->prepare("INSERT INTO ventas_productos (ID_venta, ID_producto, Fecha_venta, Cantidad_producto, Precio, Nombre_producto) VALUES (:IDVenta, :IDProducto, NOW(), :CantidadProducto, :Precio, :NombreProducto)");
    $stmt->bindValue(':IDVenta', $idVenta);
    $stmt->bindValue(':IDProducto', $idProducto);
    $stmt->bindValue(':CantidadProducto', $cantidadDeseada);
    $stmt->bindValue(':Precio', $precioFinal);
    $stmt->bindValue(':NombreProducto', $nombreProducto);
    $stmt->execute();
	

}
?>