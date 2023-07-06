<?php

include 'conexion.php';

$pdo=new conexion();

if($_SERVER['REQUEST_METHOD']=='GET')
{
	if(isset($_GET['id']))
	{
		$sql=$pdo->prepare("SELECT * FROM productos_inventario WHERE id=:Id");
		$sql->bindValue(':Id',$_GET['id']);
		$sql->execute();
		$sql->setFetchMode(PDO::FETCH_ASSOC);
		header ("http/1.1 200 OK");
		echo json_encode($sql->fetchAll());
		exit;
	}
	else
	{
		$sql=$pdo->prepare("SELECT * FROM productos_inventario");
		$sql->execute();
		$sql->setFetchMode(PDO::FETCH_ASSOC);
		header ("http/1.1 200 OK");
		echo json_encode($sql->fetchAll());
		exit;
	}
}

if($_SERVER['REQUEST_METHOD']=='POST')
{
	$sql="INSERT INTO productos_inventario (id, cantidad) VALUES (:Id, :Cantidad)";
	$stmt=$pdo->prepare($sql);	
	$stmt->bindValue(':Id',$_POST['id']);
	$stmt->bindValue(':Cantidad',$_POST['cantidad']);
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
	$sql="DELETE FROM productos_inventario WHERE id=:Id";
	$stmt=$pdo->prepare($sql);
	$stmt->bindValue(':Id',$_GET['id']);
	$stmt->execute();
	header("HTTP/1.1 200 OK");
	exit;
}

if($_SERVER['REQUEST_METHOD']=='PUT')
{
	$sql="UPDATE productos_inventario SET cantidad=:Cantidad WHERE id=:Id";
	$stmt=$pdo->prepare($sql);
	$stmt->bindValue(':Id',$_GET['id']);
	$stmt->bindValue(':Cantidad',$_GET['cantidad']);
	$stmt->execute();
	header("HTTP/1.1 200 OK");
	exit;
}

header ("HTTP/1.1 400 Bad REQUEST_METHOD")

?>