<?php
include 'conexa.php';
$pdo = new conexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmtVerif = $pdo->prepare("SELECT Id FROM productos_inventario WHERE Id = :id");
    $stmtVerif->bindValue(':id', $_POST['Id']);
    $stmtVerif->execute();
    $productoExistente = $stmtVerif->fetch();

    if (!$productoExistente) {
        echo "El producto no está registrado en el inventario. Por favor, regístrelo primero.";
        exit;
    }

    $stmtCantidad = $pdo->prepare("SELECT Cantidad FROM productos_inventario WHERE Id = :id");
    $stmtCantidad->bindValue(':id', $_POST['Id']);
    $stmtCantidad->execute();
    $cantidadExistente = $stmtCantidad->fetchColumn();

    $nuevaCantidad = $cantidadExistente + $_POST['Cantidad'];

    $stmtActualizar = $pdo->prepare("UPDATE productos_inventario SET Cantidad = :cantidad WHERE Id = :id");
    $stmtActualizar->bindValue(':cantidad', $nuevaCantidad);
    $stmtActualizar->bindValue(':id', $_POST['Id']);
    $stmtActualizar->execute();
	
    $sql = "INSERT INTO actualizacion_inventario (Fechadecompra, NumerodeFactura,Id,Cantidad) VALUES (NOW(), :NumerodeFactura,:id,:cantidad)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':NumerodeFactura', $_POST['NumerodeFactura']);
    $stmt->bindValue(':id', $_POST['Id']);
    $stmt->bindValue(':cantidad', $_POST['Cantidad']);
    $stmt->execute();
    $idPost = $pdo->lastInsertId();

    if ($idPost) {
        header("HTTP/1.1 200 OK");
        echo json_encode($idPost);
        exit;
    }

    echo "El inventario se ha actualizado correctamente.";


    exit;
}


?>