<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    exit;
}

if (!empty($_POST['task'])) {

    $task = trim($_POST['task']);

    $stmt = $conn->prepare("INSERT INTO tasks (user_id, title) VALUES (?, ?)");
    $stmt->bind_param("is", $_SESSION['user_id'], $task);
    $stmt->execute();

    echo json_encode([
        "id" => $stmt->insert_id,
        "title" => $task,
        "date" => date("Y-m-d H:i:s")
    ]);
}