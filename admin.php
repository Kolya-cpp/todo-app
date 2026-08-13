<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Статистика

$totalUsers = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) AS total FROM users")
);

$totalTasks = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) AS total FROM tasks")
);

$doneTasks = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) AS total FROM tasks WHERE is_done=1")
);

// Користувачі

$users = mysqli_query(
    $conn,
    "SELECT id, email, username FROM users"
);

if ($users === false) {
    die("Помилка SQL users: " . mysqli_error($conn));
}

// JOIN

$tasks = mysqli_query(
    $conn,
    "SELECT
        u.email,
        t.title,
        t.is_done
     FROM tasks AS t
     INNER JOIN users AS u
        ON u.id = t.user_id
     ORDER BY u.email"
);

if ($tasks === false) {
    die("Помилка SQL: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="uk">

<head>

<meta charset="UTF-8">

<title>Admin Panel</title>

<style>

body{
margin:0;
font-family:Arial;
background:#0f172a;
color:white;
padding:20px;
}

h1{
text-align:center;
}

.grid{
display:grid;
grid-template-columns:repeat(3,1fr);
gap:20px;
margin-bottom:30px;
}

.card{
background:#020b2d;
padding:20px;
border-radius:15px;
text-align:center;
}

.card h2{
font-size:36px;
margin:0;
}

.box{
background:#020b2d;
padding:20px;
border-radius:15px;
margin-bottom:20px;
}

table{
width:100%;
border-collapse:collapse;
}

td,th{
padding:12px;
border-bottom:1px solid #334155;
text-align:left;
}

.btn{
display:inline-block;
padding:12px 20px;
background:#22c55e;
color:black;
text-decoration:none;
border-radius:10px;
font-weight:bold;
margin-right:10px;
}

</style>

</head>

<body>

<h1>⚙ Адміністрування бази даних</h1>

<div class="grid">

<div class="card">
<h2><?= $totalUsers['total'] ?></h2>
<p>Користувачів</p>
</div>

<div class="card">
<h2><?= $totalTasks['total'] ?></h2>
<p>Всього задач</p>
</div>

<div class="card">
<h2><?= $doneTasks['total'] ?></h2>
<p>Виконано</p>
</div>
</div>

<div class="box">
<h2>Резервне копіювання</h2>
<a class="btn" href="backup.php">
📥 Backup
</a>

<a class="btn" href="dashboard.php">
📊 Dashboard
</a>
</div>
<div class="box">

<h2>Користувачі</h2>

<table>
<tr>
<th>ID</th>
<th>Email</th>
<th>Username</th>
</tr>

<?php while($user=mysqli_fetch_assoc($users)): ?>

<tr>
<td><?= $user['id'] ?></td>
<td><?= htmlspecialchars($user['email']) ?></td>
<td><?= htmlspecialchars($user['username'] ?? '') ?></td>
</tr>

</tr>
<?php endwhile; ?>
</table>
</div>

<div class="box">

<h2>Задачі користувачів</h2>

<?php

$currentUser="";

mysqli_data_seek($tasks,0);

while($task=mysqli_fetch_assoc($tasks)):

if($currentUser != $task['email']):

if($currentUser!=""){
echo "</ul>";
}

$currentUser=$task['email'];

?>

<h3 style="
margin-top:25px;
color:#60a5fa;
">

👤 <?= $task['email'] ?>

</h3>

<ul>

<?php endif; ?>

<li style="padding:8px;">

<?= $task['is_done']

? "✅ ".$task['title']
: "⏳ ".$task['title']

?>

</li>
<?php endwhile; ?>

</ul>
</div>

</body>
</html>