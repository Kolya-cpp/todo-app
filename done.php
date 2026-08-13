<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {

    $id = (int)$_GET['id'];

    $stmt = $conn->prepare("
        UPDATE tasks 
        SET is_done = 1 
        WHERE id = ? AND user_id = ?
    ");

    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    $stmt->execute();
}

header("Location: index.php");
exit;