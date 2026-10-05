<?php
session_start();
require "includes/db.php";
require "includes/functions.php";

// If already logged in, skip the login page
if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

$errors = [];
$email = "";

// Show the success message from registration (only once)
$success = $_SESSION["success"] ?? "";
unset($_SESSION["success"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = clean_input($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    // Email
    if ($email === "") {
        $errors["email"] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Please enter a valid email address.";
    }

    // Password
    if ($password === "") {
        $errors["password"] = "Password is required.";
    }

    // Check the credentials
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true);
            $_SESSION["user_id"]   = $user["id"];
            $_SESSION["user_name"] = $user["full_name"];
            header("Location: dashboard.php");
            exit;
        } else {
            $errors["general"] = "Incorrect email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Travel Voyage</title>
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
            <a href="register.php">Register</a>
            <a href="login.php" class="active">Login</a>
        </div>

        <?php if ($success !== ""): ?>
            <div class="success"><?= e($success) ?></div>
        <?php endif; ?>

        <?php if (isset($errors["general"])): ?>
            <div class="error error-box"><?= e($errors["general"]) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php" novalidate>

            <div class="field">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email"
                       placeholder="Enter email address" value="<?= e($email) ?>">
                <?php if (isset($errors["email"])): ?>
                    <span class="error"><?= e($errors["email"]) ?></span>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="********">
                <?php if (isset($errors["password"])): ?>
                    <span class="error"><?= e($errors["password"]) ?></span>
                <?php endif; ?>
            </div>

            <a href="#" class="forgot">Forgot password?</a>

            <button type="submit" class="btn btn-full">Login</button>
        </form>
    </section>

</div>

</body>
</html>