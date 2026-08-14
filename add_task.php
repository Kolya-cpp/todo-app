<?php

session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "error" => "Не авторизовано"
    ]);
    exit;
}

if (!empty($_POST['task'])) {

    $task = trim($_POST['task']);
    $description = trim($_POST['description'] ?? '');
    $dueDate = $_POST['due_date'] ?? null;
    $priority = $_POST['priority'] ?? 'medium';
    $category = trim($_POST['category'] ?? '');

    $stmt = $conn->prepare("
        INSERT INTO tasks
        (user_id, title, description, due_date, priority, category)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isssss",
        $_SESSION['user_id'],
        $task,
        $description,
        $dueDate,
        $priority,
        $category
    );

    $stmt->execute();

    echo json_encode([
        "id" => $stmt->insert_id,
        "title" => $task,
        "description" => $description,
        "due_date" => $dueDate,
        "priority" => $priority,
        "category" => $category
    ]);

    exit;
}

echo json_encode([
    "error" => "Порожнє завдання"
]);