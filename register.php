<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $password);

    if (!$stmt->execute()) {
        $error = "Email вже існує або помилка";
    } else {
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
<meta charset="UTF-8">
<title>Реєстрація</title>

<style>
body {
    margin: 0;
    font-family: system-ui, sans-serif;
    background: #0f172a;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Контейнер */
.card {
    background: #020617;
    padding: 40px;
    border-radius: 20px;
    width: 320px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.4);
}

/* Заголовок */
h2 {
    margin-bottom: 20px;
}

/* Інпути */
input {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border-radius: 10px;
    border: none;
    font-size: 14px;
    outline: none;
}

/* Кнопка */
button {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 10px;
    background: #22c55e;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s;
}

button:hover {
    transform: scale(1.05);
    background: #16a34a;
}

/* Лінк */
a {
    display: block;
    margin-top: 15px;
    color: #94a3b8;
    text-decoration: none;
    font-size: 14px;
}

a:hover {
    color: white;
}

/* Помилка */
.error {
    color: #ef4444;
    margin-bottom: 10px;
    font-size: 14px;
}
</style>

</head>
<body>

<div class="card">

<h2>📝 Реєстрація</h2>

<?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

<form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Пароль" required>
    <button type="submit">Зареєструватися</button>
</form>

<a href="login.php">Вже маєш акаунт? Увійти</a>

</div>

</body>
</html>