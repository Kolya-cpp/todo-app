<?php
session_start();
require 'db.php';

$user_id = $_SESSION['user_id'];

$total = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) as total FROM tasks WHERE user_id = $user_id"
));

$done = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) as done FROM tasks WHERE user_id = $user_id AND is_done = 1"
));

$active = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) as active FROM tasks WHERE user_id = $user_id AND is_done = 0"
));

$progress = 0;

if ($total['total'] > 0) {
    $progress = round(($done['done'] / $total['total']) * 100);
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>

<style>

body{
    margin:0;
    font-family:Arial;
    background: linear-gradient(to right,#111827,#1e293b);
    color:white;
}

.container{
    width:90%;
    margin:auto;
    margin-top:50px;
}

.cards{
    display:flex;
    gap:20px;
    flex-wrap:wrap;
}

.card{
    background:#020b2d;
    padding:30px;
    border-radius:20px;
    width:220px;
    box-shadow:0 0 20px rgba(0,0,0,0.4);
}

.card h2{
    margin:0;
    font-size:40px;
}

.card p{
    color:#aaa;
}

.progress-box{
    margin-top:40px;
    background:#020b2d;
    padding:30px;
    border-radius:20px;
}

.progress{
    width:100%;
    height:20px;
    background:#334155;
    border-radius:20px;
    overflow:hidden;
}

.progress-bar{
    height:100%;
    background:#22c55e;
}

.sidebar a{
    text-decoration:none;
    color:white;
}

.sidebar a .card{
    color:white;
}

</style>
</head>

<body>

<div class="container">

<h1>📊 Dashboard</h1>

<div class="cards">

<div class="card">
<h2><?= $total['total'] ?></h2>
<p>Total Tasks</p>
</div>

<div class="card">
<h2><?= $active['active'] ?></h2>
<p>Active</p>
</div>

<div class="card">
<h2><?= $done['done'] ?></h2>
<p>Done</p>
</div>

</div>

<div class="progress-box">

<h2>Progress</h2>

<div class="progress">
    <div class="progress-bar" style="width: <?= $progress ?>%;"></div>
</div>

<p><?= $progress ?>% done</p>

</div>

</div>

</body>
</html>