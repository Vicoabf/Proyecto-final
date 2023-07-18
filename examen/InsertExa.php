<?php
include 'conexa.php';
$pdo = new conexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sql = "INSERT INTO registro_productos (Id, Nombre, Marca, Presentacion, Precio) VALUES (:id,
    :nombre, :marca, :presentacion, :precio)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $_POST['Id']);
    $stmt->bindValue(':nombre', $_POST['Nombre']);
    $stmt->bindValue(':marca', $_POST['Marca']);
    $stmt->bindValue(':presentacion', $_POST['Presentacion']);
    $stmt->bindValue(':precio', $_POST['Precio']);
    $stmt->execute();
    $idPost = $pdo->lastInsertId();

    if ($idPost) {
        

        header("HTTP/1.1 200 OK");
        echo json_encode($idPost);
        exit;
    }

}
?>

