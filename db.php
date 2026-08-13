<?php
session_start();

$conn = new mysqli("127.0.0.1", "root", "root", "todoapp");

if ($conn->connect_error) {
    die("Помилка підключення: " . $conn->connect_error);
}
?>