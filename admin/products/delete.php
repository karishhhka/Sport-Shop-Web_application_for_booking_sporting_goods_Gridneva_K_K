<?php 
session_start();

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../index.html"); 
    exit;
}
require '../../database/db.php';
$id = $_GET['id'];
$pdo->query("DELETE FROM Products WHERE id_product = $id");
header('Location: index.php');
?>