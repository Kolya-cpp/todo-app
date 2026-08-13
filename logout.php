<?php
session_start();

// очищаємо сесію
$_SESSION = [];

// знищуємо cookie сесії (важливо!)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// знищуємо сесію
session_destroy();

// редірект
header("Location: login.php");
exit;