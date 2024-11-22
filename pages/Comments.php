<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("functions.php");

// Проверка авторизации
if (!isset($_SESSION['ruser'])) {
    echo "<h3 class='text-center text-danger'>Access denied. Please log in to leave a comment.</h3>";
    exit();
}

// Подключение к базе данных
$mysqli = connect();

// Если форма отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hotel_id = intval($_POST['hotel_id']);
    $comment = trim(htmlspecialchars($_POST['comment']));
    $user = $_SESSION['ruser']; // Имя пользователя из сессии

    if (!empty($hotel_id) && !empty($comment)) {
        $stmt = $mysqli->prepare("INSERT INTO comments (hotel_id, user, comment, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $hotel_id, $user, $comment);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success text-center'>Comment added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger text-center'>Failed to add comment! Please try again later.</div>";
        }
    } else {
        echo "<div class='alert alert-danger text-center'>All fields are required.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Comment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
        }
        h3 {
            color: #333;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h3 class="text-center mb-4">Add a Comment</h3>
    <form action="index.php?page=2" method="post" class="p-4 rounded shadow bg-white">
        <div class="form-group mb-4">
            <label for="hotel_id" class="form-label">Select Hotel:</label>
            <select name="hotel_id" id="hotel_id" class="form-select" required>
                <option value="">-- Select Hotel --</option>
                <?php
                // Получение списка отелей
                $result = $mysqli->query("SELECT id, hotel FROM hotels ORDER BY hotel");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>" . htmlspecialchars($row['hotel']) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group mb-4">
            <label for="comment" class="form-label">Comment:</label>
            <textarea name="comment" id="comment" rows="4" class="form-control" placeholder="Write your comment here..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100">Submit</button>
    </form>
</div>
</body>
</html>