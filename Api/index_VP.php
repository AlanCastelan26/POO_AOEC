<?php

include 'conexion.php';

$pdo=new conexion();

if($_SERVER['REQUEST_METHOD']=='GET')
{
	if(isset($_GET['id']))
	{
		$sql=$pdo->prepare("SELECT * FROM ventas_productos WHERE id_venta=:IV");
		$sql->bindValue(':IV',$_GET['id_venta']);
		$sql->execute();
		$sql->setFetchMode(PDO::FETCH_ASSOC);
		header ("http/1.1 200 OK");
		echo json_encode($sql->fetchAll());
		exit;
	}
	else
	{
		$sql=$pdo->prepare("SELECT * FROM ventas_productos");
		$sql->execute();
		$sql->setFetchMode(PDO::FETCH_ASSOC);
		header ("http/1.1 200 OK");
		echo json_encode($sql->fetchAll());
		exit;
	}
}

if($_SERVER['REQUEST_METHOD']=='POST')
{
	$sql="INSERT INTO ventas_productos (id_venta, id_producto, fecha_venta, cantidad_producto, precio, nombre_producto) VALUES (:IV, :IP, :FV, :CP, :Pre, ::NP)";
	$stmt=$pdo->prepare($sql);	
	$stmt->bindValue(':IV',$_POST['id_venta']);
	$stmt->bindValue(':IP',$_POST['id_producto']);
	$stmt->bindValue(':FV',$_POST['fecha_de_compra']);
	$stmt->bindValue(':CP',$_POST['cantidad_producto']);
	$stmt->bindValue(':Pre',$_POST['precio']);
	$stmt->bindValue(':NP',$_POST['nombre_producto']);
	$stmt->execute();
	$idPost=$pdo->lastInsertId();
	
	if($idPost)
	{
		header ("http/1.1 200 OK");
		echo json_encode($idPost);
		exit;
	}
}

if($_SERVER['REQUEST_METHOD']=='DELETE')
{
	$sql="DELETE FROM ventas_productos WHERE id_venta=:IV";
	$stmt=$pdo->prepare($sql);
	$stmt->bindValue(':IV',$_GET['id_venta']);
	$stmt->execute();
	header("HTTP/1.1 200 OK");
	exit;
}

if($_SERVER['REQUEST_METHOD']=='PUT')
{
	$sql="UPDATE ventas_productos SET id_producto=:IP, fecha_venta=:FV, cantidad_producto=:CP, precio=:Pre, nombre_producto=:NP WHERE id_venta=:IV";
	$stmt=$pdo->prepare($sql);
	$stmt->bindValue(':IV',$_GET['id_venta']);
	$stmt->bindValue(':IP',$_GET['id_producto']);
	$stmt->bindValue(':FV',$_GET['fecha_venta']);
	$stmt->bindValue(':CP',$_GET['cantidad_producto']);
	$stmt->bindValue(':Pre',$_GET['precio']);
	$stmt->bindValue(':NP',$_GET['nombre_producto']);
	$stmt->execute();
	header("HTTP/1.1 200 OK");
	exit;
}

header ("HTTP/1.1 400 Bad REQUEST_METHOD")

?>