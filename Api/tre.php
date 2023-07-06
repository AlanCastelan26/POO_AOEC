<?php

include 'conexion.php';

$pdo=new Conexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener el ID del producto ingresado por el usuario
    $id = $_POST['Id'];

    // Verificar si el producto existe en la tabla "productos_inventario"
    $sqlVerificar = "SELECT * FROM productos_inventario WHERE Id = :Id";
    $stmtVerificar = $pdo->prepare($sqlVerificar);
    $stmtVerificar->bindValue(':Id', $id);
    $stmtVerificar->execute();

    if ($stmtVerificar->rowCount() > 0) {
        // El producto existe en la tabla "productos_inventario"
        $producto = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
        $nombreProducto = $producto['Nombre'];
        $presentacion = $producto['Presentación'];
        $precioProducto = $producto['Precio'];

        // Mostrar información del producto al usuario
        echo "Nombre del producto: $nombreProducto<br>";
        echo "Presentación: $presentacion<br>";
        echo "Precio: $precioProducto<br>";

        // Solicitar al usuario los datos de la venta
        $idVenta = uniqid();
        $fechaVenta = date("Y-m-d");
        $cantidadVenta = (int)readline("Ingrese la cantidad a vender: ");
        $precioVenta = $producto['Precio'];
        $nombreVenta = $producto['Nombre'];

        // Verificar si la cantidad de venta es válida
        if ($cantidadVenta <= $producto['Cantidad']) {
            // Calcular el total de pago
            $totalPago = $precioVenta * $cantidadVenta;

            // Registrar la venta en la tabla "ventas_productos"
            $sqlVenta = "INSERT INTO ventas_productos (Id_venta, Id_producto, Fecha_venta, Cantidad_producto, Precio, Nombre_Producto)
                         VALUES (:IdVenta, :IdProducto, :FechaVenta, :Cantidad, :Precio, :Nombre)";
            $stmtVenta = $pdo->prepare($sqlVenta);
            $stmtVenta->bindValue(':IdVenta', $idVenta);
            $stmtVenta->bindValue(':IdProducto', $id);
            $stmtVenta->bindValue(':FechaVenta', $fechaVenta);
            $stmtVenta->bindValue(':Cantidad', $cantidadVenta);
            $stmtVenta->bindValue(':Precio', $precioVenta);
            $stmtVenta->bindValue(':Nombre', $nombreVenta);
            $stmtVenta->execute();

            // Actualizar la cantidad en la tabla "productos_inventario"
            $nuevaCantidad = $producto['Cantidad'] - $cantidadVenta;
            $sqlActualizarInventario = "UPDATE productos_inventario SET Cantidad = :NuevaCantidad WHERE Id = :Id";
            $stmtActualizarInventario = $pdo->prepare($sqlActualizarInventario);
            $stmtActualizarInventario->bindValue(':NuevaCantidad', $nuevaCantidad);
            $stmtActualizarInventario->bindValue(':Id', $id);
            $stmtActualizarInventario->execute();

            // Actualizar la cantidad en la tabla "actualizacion_inventario"
            $sqlActualizarActualizacion = "UPDATE actualizacion_inventario SET Cantidad = Cantidad + :Cantidad WHERE Id = :Id";
            $stmtActualizarActualizacion = $pdo->prepare($sqlActualizarActualizacion);
            $stmtActualizarActualizacion->bindValue(':Cantidad', $cantidadVenta);
            $stmtActualizarActualizacion->bindValue(':Id', $id);
            $stmtActualizarActualizacion->execute();

            // Mostrar el total de pago al usuario
            echo "Total a pagar: $totalPago<br>";

            // Mostrar mensaje de venta
		}
/*header ("HTTP/1.1 400 Bad REQUEST_METHOD")*/
?>