<?php

include 'conexion.php';

$pdo=new Conexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['Id'];
    $cantidad = $_POST['Cantidad'];
    $fechaCompra = $_POST['Fecha_de_compra'];
    $numeroFactura = $_POST['Numero_de_factura'];

    // Verificar si el producto existe en la tabla "productos_inventario"
    $sqlVerificar = "SELECT COUNT(*) FROM productos_inventario WHERE Id = :Id";
    $stmtVerificar = $pdo->prepare($sqlVerificar);
    $stmtVerificar->bindValue(':Id', $id);
    $stmtVerificar->execute();

    if ($stmtVerificar->fetchColumn() > 0) {
        // El producto existe en la tabla "productos_inventario"
        // Actualizar la cantidad en la tabla "productos_inventario"
        $sqlActualizarInventario = "UPDATE productos_inventario SET Cantidad = Cantidad + :Cantidad WHERE Id = :Id";
        $stmtActualizarInventario = $pdo->prepare($sqlActualizarInventario);
        $stmtActualizarInventario->bindValue(':Cantidad', $cantidad);
        $stmtActualizarInventario->bindValue(':Id', $id);
        $stmtActualizarInventario->execute();

        // Insertar en la tabla "actualizacion_inventario"
        $sqlActualizacion = "INSERT INTO actualizacion_inventario (Id, Cantidad, Fecha_de_compra, Numero_de_factura) VALUES (:Id, :Cantidad, :FechaCompra, :NumeroFactura)";
        $stmtActualizacion = $pdo->prepare($sqlActualizacion);
        $stmtActualizacion->bindValue(':Id', $id);
        $stmtActualizacion->bindValue(':Cantidad', $cantidad);
        $stmtActualizacion->bindValue(':FechaCompra', $fechaCompra);
        $stmtActualizacion->bindValue(':NumeroFactura', $numeroFactura);
        $stmtActualizacion->execute();

        header("HTTP/1.1 200 OK");
        echo json_encode("Actualización de inventario realizada correctamente");
        exit;
    } else {
        // El producto no existe en la tabla "productos_inventario"
        header("HTTP/1.1 404 Not Found");
        echo json_encode("El producto no existe en el inventario. Agrega primero el producto.");
        exit;
    }
}

header ("HTTP/1.1 400 Bad REQUEST_METHOD")

?>