<?php
session_start();
require "includes/functions.php";

// Block anyone who isn't logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Travel Voyage</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <nav class="nav glass">
        <a href="index.php">Home</a>
        <a href="#">Gallery</a>
        <a href="#">Packages</a>
        <a href="logout.php" class="active">Logout</a>
    </nav>

    <main class="hero">
        <h1 class="logo">Welcome, <?= e($_SESSION["user_name"]) ?>!</h1>
        <p>You are logged in. Your next getaway starts here.</p>
        <a href="logout.php" class="btn">Log out</a>
    </main>

</body>
</html>