<?php

$conn = new mysqli("127.0.0.1", "root", "root", "todoapp");

if ($conn->connect_error) {
    die("MYSQL ERROR: " . $conn->connect_error);
}
