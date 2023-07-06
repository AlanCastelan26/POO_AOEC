<?php

include 'conexion.php';

$pdo=new Conexion();

if($_SERVER['REQUEST_METHOD']=='GET')
{
	if(isset($_GET['id']))
	{
		$sql=$pdo->prepare("SELECT * FROM registro_productos WHERE :id");
		$sql->bindValue(':id',$_GET['Id']);
		$sql->execute();
		$sql->setFetchMode(PDO::FETCH_ASSOC);
		header ("http/1.1 200 OK");
		echo json_encode($sql->fetchAll());
		exit;
	}
	else
	{
		$sql=$pdo->prepare("SELECT * FROM registro_productos");
		$sql->execute();
		$sql->setFetchMode(PDO::FETCH_ASSOC);
		header ("http/1.1 200 OK");
		echo json_encode($sql->fetchAll());
		exit;
	}
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['Id'];
    $nombreProducto = $_POST['Nombre'];

    // Insertar en la tabla "registro_productos"
    $sqlRegistro = "INSERT INTO registro_productos (Id, Nombre, Marca, Presentación, Precio) VALUES (:Id, :Nom, :Mar, :Pre, :Precio)";
    $stmtRegistro = $pdo->prepare($sqlRegistro);
    $stmtRegistro->bindValue(':Id', $id);
    $stmtRegistro->bindValue(':Nom', $nombreProducto);
    $stmtRegistro->bindValue(':Mar', $_POST['Marca']);
    $stmtRegistro->bindValue(':Pre', $_POST['Presentación']);
    $stmtRegistro->bindValue(':Precio', $_POST['Precio']);
    $stmtRegistro->execute();

    // Obtener el último ID insertado en la tabla "registro_productos"
    $lastInsertId = $pdo->lastInsertId();

    // Insertar en la tabla "productos_inventario"
    $sqlInventario = "INSERT INTO productos_inventario (Id, Nombre, Cantidad) VALUES (:Id, :Nom, 0)";
    $stmtInventario = $pdo->prepare($sqlInventario);
    $stmtInventario->bindValue(':Id', $lastInsertId);
    $stmtInventario->bindValue(':Nom', $nombreProducto);
    $stmtInventario->execute();

    header("HTTP/1.1 200 OK");
    echo json_encode("complet");
    exit;
}



if($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    
    if(isset($data['Id'])) {
        $id = $data['Id'];

        $sql = "DELETE FROM registro_productos WHERE Id = :Id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':Id', $id);
        $stmt->execute();

        $rowCount = $stmt->rowCount();
        
        if($rowCount > 0) {
            header("HTTP/1.1 200 OK");
            echo json_encode("Producto eliminado correctamente");
            exit;
        } else {
            header("HTTP/1.1 404 Not Found");
            echo json_encode("No se encontró ningún producto con el ID proporcionado");
            exit;
        }
    }
}



if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Obtener los datos enviados en formato JSON
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Verificar si se proporcionó el ID
    if (isset($data['Id'])) {
        $id = $data['Id'];

        // Verificar si el registro existe en la base de datos
        $checkSql = "SELECT COUNT(*) FROM registro_productos WHERE Id = :Id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->bindValue(':Id', $id);
        $checkStmt->execute();

        if ($checkStmt->fetchColumn() > 0) {
            // Actualizar el registro
            $sql = "UPDATE registro_productos SET Nombre = :Nom, Marca = :Mar, Presentación = :Pre, Precio = :Precio WHERE Id = :Id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':Nom', $data['Nombre']);
            $stmt->bindValue(':Mar', $data['Marca']);
            $stmt->bindValue(':Pre', $data['Presentación']);
            $stmt->bindValue(':Precio', $data['Precio']);
            $stmt->bindValue(':Id', $id);
            $stmt->execute();

            // Verificar si se realizó la actualización correctamente
            $rowCount = $stmt->rowCount();
            if ($rowCount > 0) {
                header("HTTP/1.1 200 OK");
                echo json_encode("Producto modificado correctamente");
                exit;
            }
        }
    }


    // Si el registro no existe o no se pudo actualizar
    header("HTTP/1.1 404 Not Found");
    echo json_encode("No se encontró ningún producto con el ID proporcionado");
    exit;
}

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
        // El producto existe en la tabla "actualizacion_inventario"
        // Actualizar la cantidad y otros campos en la tabla "actualizacion_inventario"
        $sqlActualizar = "UPDATE actualizacion_inventario SET Cantidad = Cantidad + :Cantidad, Fecha_de_compra = :FechaCompra, Numero_de_factura = :NumeroFactura WHERE Id = :Id";
        $stmtActualizar = $pdo->prepare($sqlActualizar);
        $stmtActualizar->bindValue(':Cantidad', $cantidad);
        $stmtActualizar->bindValue(':FechaCompra', $fechaCompra);
        $stmtActualizar->bindValue(':NumeroFactura', $numeroFactura);
        $stmtActualizar->bindValue(':Id', $id);
        $stmtActualizar->execute();

        header("HTTP/1.1 200 OK");
        echo json_encode("Actualización de inventario realizada correctamente");
        exit;
    } else {
        // El producto no existe en la tabla "productos_inventario"
        header("HTTP/1.1 404 Not Found");
        echo json_encode("El producto no existe en el inventario. Primero debes registrar el producto.");
        exit;
    }
}

/*header ("HTTP/1.1 400 Bad REQUEST_METHOD")*/
?>
