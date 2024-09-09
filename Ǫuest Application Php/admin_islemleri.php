<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$db = new PDO('sqlite:veriler.db');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $soru = $_POST['soru'];
    $zorluk = $_POST['zorluk'];
    $sik1 = $_POST['sik1'];
    $sik2 = $_POST['sik2'];
    $sik3 = $_POST['sik3'];
    $sik4 = $_POST['sik4'];
    $dogruSik = $_POST['dogruSik'];

    if (isset($_POST['soruid'])) {
        $id = $_POST['soruid'];
        $stmt = $db->prepare("UPDATE sorular SET soru = :soru, zorluk = :zorluk, sik1 = :sik1, sik2 = :sik2, sik3 = :sik3, sik4 = :sik4, dogruSik = :dogruSik WHERE id = :id");
        $stmt->bindParam(':id', $id);
    } else {
        $stmt = $db->prepare("INSERT INTO sorular (soru, zorluk, sik1, sik2, sik3, sik4, dogruSik) VALUES (:soru, :zorluk, :sik1, :sik2, :sik3, :sik4, :dogruSik)");
    }

    $stmt->bindParam(':soru', $soru);
    $stmt->bindParam(':zorluk', $zorluk);
    $stmt->bindParam(':sik1', $sik1);
    $stmt->bindParam(':sik2', $sik2);
    $stmt->bindParam(':sik3', $sik3);
    $stmt->bindParam(':sik4', $sik4);
    $stmt->bindParam(':dogruSik', $dogruSik);
    $stmt->execute();
    header('Location: admin.php');
    exit();
} elseif (isset($_GET['action'])) {
    if ($_GET['action'] === 'edit') {
        $id = $_GET['id'];
        $stmt = $db->prepare("SELECT * FROM sorular WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $soru = $stmt->fetch(PDO::FETCH_ASSOC);
    } elseif ($_GET['action'] === 'delete') {
        $id = $_GET['id'];
        $stmt = $db->prepare("DELETE FROM sorular WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header('Location: admin.php');
        exit();
    }
}
?>