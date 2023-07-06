<?php

include 'conexion.php';

$pdo=new Conexion();
if($_SERVER['REQUEST_METHOD']=='GET'){
	if(isset($_GET['id'])){
	$sql=$pdo->prepare("SELECT * FROM clientes WHERE id=:id");
	$sql->bindValue(':id',$_GET['id']);
	$sql->execute();
	$sql->setFetchMode(PDO::FETCH_ASSOC);
	header ("HTTP/1.1 200 Ok");
	echo json_encode($sql->fetchAll());
	exit;
	}
	else{
$sql=$pdo->prepare ("SELECT * FROM clientes");
$sql->execute();
$sql->setFetchMode(PDO::FETCH_ASSOC);
header ("HTTP/1.1 200 Ok");
echo json_encode ($sql->fetchAll());
exit;
}
}
if ($_SERVER['REQUEST_METHOD']=='POST'){
	$sql="INSERT INTO clientes (id,nombre,email, ping) VALUES (:Id,:Nombre,
	:Email, :ping)";
	$stmt=$pdo->prepare($sql);
	$stmt->bindValue(':Id',$_POST['id']);
	$stmt->bindValue(':Nombre',$_POST['nombre']);
	$stmt->bindValue(':Email',$_POST['email']);
	$stmt->bindValue(':ping',$_POST['ping']);
	$stmt->execute();
	$idPost=$pdo->lastInsertId();
	if($idPost){
		header("HTTP/1.1 200 Ok");
		echo json_encode($idPost);
		exit;
	}
}
	

?>