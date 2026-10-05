<?php

session_start();
require "includes/db.php";
require "includes/functions.php";

$errors = [];
$full_name = " ";
$email = " ";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = clean_input($_POST["full_name"] ?? "");
    $email     = clean_input($_POST["email"] ?? "");
    $password  = $_POST["password"] ?? "";
    $confirm   = $_POST["confirm_password"] ?? "";

    // Full name
    if ($full_name === "") {
        $errors["full_name"] = "Full name is required.";
    } elseif (strlen($full_name) < 2) {
        $errors["full_name"] = "Name must be at least 2 characters.";
    } elseif (!preg_match("/^[\p{L} .'-]+$/u", $full_name)) {
        $errors["full_name"] = "Name can only contain letters, spaces, and . ' -";
    }

    // Email
    if ($email === "") {
        $errors["email"] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Please enter a valid email address.";
    }

    // Password
    if ($password === "") {
        $errors["password"] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors["password"] = "Password must be at least 8 characters.";
    } elseif (!preg_match("/[A-Za-z]/", $password) || !preg_match("/[0-9]/", $password)) {
        $errors["password"] = "Password must contain at least one letter and one number.";
    }

    // Confirm password
    if ($confirm === "") {
        $errors["confirm_password"] = "Please confirm your password.";
    } elseif ($password !== $confirm) {
        $errors["confirm_password"] = "Passwords do not match.";
    }

    // Check if the email is already registered
    if (!isset($errors["email"])) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors["email"] = "This email is already registered.";
        }
        $stmt->close();
    }

    // No errors: save the user
    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $full_name, $email, $hashed);
        $stmt->execute();
        $stmt->close();

        $_SESSION["success"] = "Account created! You can now log in.";
        header("Location: login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Travel Voyage</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="auth-wrapper">

    <section class="auth-brand">
        <h1 class="logo">Travel Voyage</h1>
        <p>Your gateway to unforgettable getaways<br>explore, book, and experience your dream escape today!</p>
    </section>

    <section class="auth-card glass">
        <div class="auth-logo logo">TV</div>

        <div class="switch">
            <a href="register.php" class="active">Register</a>
            <a href="login.php">Login</a>
        </div>

        <form method="POST" action="register.php" novalidate>

            <div class="field">
                <label for="full_name">Full name</label>
                <input type="text" id="full_name" name="full_name"
                       placeholder="eg. Lyka Jeans" value="<?= e($full_name) ?>">
                <?php if (isset($errors["full_name"])): ?>
                    <span class="error"><?= e($errors["full_name"]) ?></span>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email"
                       placeholder="example@gmail.com" value="<?= e($email) ?>">
                <?php if (isset($errors["email"])): ?>
                    <span class="error"><?= e($errors["email"]) ?></span>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="password">Create password</label>
                <input type="password" id="password" name="password" placeholder="********">
                <?php if (isset($errors["password"])): ?>
                    <span class="error"><?= e($errors["password"]) ?></span>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="confirm_password">Confirm password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="********">
                <?php if (isset($errors["confirm_password"])): ?>
                    <span class="error"><?= e($errors["confirm_password"]) ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-full">Create Account</button>
        </form>
    </section>

</div>

</body>
</html>