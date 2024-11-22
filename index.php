<?php
session_start();
include_once("pages/functions.php");

$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Agency</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <!-- Шапка -->
    <header class="mb-4 text-center">
        <h1 class="text-primary fw-bold">Travel Agency</h1>
        <?php include_once("pages/login.php"); ?>
    </header>

    <!-- Навигация -->
    <nav class="mb-4 text-center">
        <a href="index.php?page=1" class="btn btn-primary mx-1">Tours</a>
        <a href="index.php?page=2" class="btn btn-primary mx-1">Comments</a>
        <a href="index.php?page=3" class="btn btn-primary mx-1">Registration</a>
        <a href="index.php?page=4" class="btn btn-primary mx-1">Admin Panel</a>
        <a href="index.php?page=5" class="btn btn-primary mx-1">Private</a>
    </nav>

    <!-- Основной контент -->
    <main>
        <?php
        switch ($page) {
            case 1:
                if (isset($_SESSION['ruser'])) {
                    include_once("pages/tours.php");
                } else {
                    echo "<p class='text-danger text-center'>Access denied. Please log in.</p>";
                }
                break;
            case 2:
                if (isset($_SESSION['ruser'])) {
                    include_once("pages/comments.php");
                } else {
                    echo "<p class='text-danger text-center'>Access denied. Please log in.</p>";
                }
                break;
            case 3:
                include_once("pages/registration.php");
                break;
            case 4:
                if (isset($_SESSION['ruser'])) {
                    include_once("pages/admin.php");
                } else {
                    echo "<p class='text-danger text-center'>Access denied. Please log in.</p>";
                }
                break;
            case 5:
                if (isset($_SESSION['radmin'])) {
                    include_once("pages/private.php");
                } else {
                    echo "<p class='text-danger text-center'>Access denied. Admin only.</p>";
                }
                break;
            default:
                echo "<h3 class='text-center mb-4 text-secondary'>Welcome to our Travel Agency!</h3>";
                echo "<div class='video-container d-flex justify-content-center align-items-center'>";
                echo "<video controls autoplay muted loop class='shadow-lg rounded'>";
                echo "<source src='videos/летоскоро.mp4' type='video/mp4'>";
                echo "Your browser does not support the video tag.";
                echo "</video>";
                echo "</div>";
                break;
        }
        ?>
    </main>

    <!-- Футер -->
    <footer class="text-center mt-4 text-muted">
        <p>&copy; 2024 Travel Agency. All rights reserved.</p>
    </footer>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>