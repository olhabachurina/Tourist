<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Info</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9e5e5;
            color: #343a40;
            margin: 0;
            padding: 0;
        }
        h2 {
            font-size: 2.5rem;
            color: #5a5a5a;
            text-align: center;
            margin-bottom: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .price-stars {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.25rem;
            margin-bottom: 20px;
        }
        .price-stars .stars img {
            width: 24px;
            height: 24px;
        }
        .price-stars .badge {
            font-size: 1.5rem;
            background-color: #ff5757;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .row {
            display: flex;
            gap: 20px;
        }
        .slider-container {
            position: relative;
            flex: 1;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .slider-container img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }
        .slider-container img.active {
            opacity: 1;
        }
        .info-block {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            font-size: 1.1rem;
            line-height: 1.6;
        }
        .comments-section {
            margin-top: 50px;
        }
        .comments-section h3 {
            font-size: 2rem;
            color: #343a40;
            margin-bottom: 20px;
            text-align: center;
        }
        .card {
            margin-bottom: 20px;
            border: none;
            background-color: #fff5f5;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .card-body {
            padding: 20px;
        }
        .card-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #4caf50;
        }
        .card-text {
            font-size: 1rem;
            color: #343a40;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .text-muted {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .btn-back {
            display: block;
            margin: 30px auto;
            font-size: 1.25rem;
            padding: 10px 20px;
            border-radius: 10px;
            background-color: #007bff;
            border: none;
            color: white;
            transition: all 0.3s ease-in-out;
        }
        .btn-back:hover {
            background-color: #0056b3;
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
        @media (max-width: 768px) {
            .row {
                flex-direction: column;
            }
            .price-stars {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'functions.php';

if (isset($_GET['hotel'])) {
    $hotel = intval($_GET['hotel']);
    $mysqli = connect();

    // Получение информации об отеле
    $stmt = $mysqli->prepare("SELECT * FROM hotels WHERE id = ?");
    $stmt->bind_param("i", $hotel);
    $stmt->execute();
    $hotel_result = $stmt->get_result();
    $hotel_info = $hotel_result->fetch_assoc();

    if (!$hotel_info) {
        echo "<h3 class='text-danger text-center'>Hotel not found!</h3>";
        exit();
    }

    $hname = $hotel_info['hotel'];
    $hstars = intval($hotel_info['stars']);
    $hcost = $hotel_info['cost'];
    $hinfo = $hotel_info['info'];

    echo '<div class="container">';
    echo '<h2>' . htmlspecialchars($hname) . '</h2>';
    echo '<div class="price-stars">';
    echo '<div class="stars">';
    for ($i = 0; $i < $hstars; $i++) {
        echo '<img src="../images/star.png" alt="star">';
    }
    echo '</div>';
    echo '<span class="badge">' . $hcost . ' Евро</span>';
    echo '</div>';
    echo '<div class="row">';
    echo '<div class="slider-container" id="slider">';

    // Получение изображений
    $imgStmt = $mysqli->prepare("SELECT imagepath FROM images WHERE hotelid = ?");
    $imgStmt->bind_param("i", $hotel);
    $imgStmt->execute();
    $imgResult = $imgStmt->get_result();

    while ($imgRow = $imgResult->fetch_assoc()) {
        echo '<img src="../' . htmlspecialchars($imgRow['imagepath']) . '" alt="Hotel Image">';
    }
    echo '</div>';
    echo '<div class="info-block">' . nl2br(htmlspecialchars($hinfo)) . '</div>';
    echo '</div>'; // Close row
    echo '</div>'; // Close container

    // Секция комментариев
    echo '<div class="container comments-section">';
    echo '<h3>Comments</h3>';

    // Вывод комментариев
    $commentDisplayStmt = $mysqli->prepare("SELECT user, comment, created_at FROM comments WHERE hotel_id = ? ORDER BY created_at DESC");
    $commentDisplayStmt->bind_param("i", $hotel);
    $commentDisplayStmt->execute();
    $commentResult = $commentDisplayStmt->get_result();

    if ($commentResult->num_rows > 0) {
        while ($commentRow = $commentResult->fetch_assoc()) {
            echo '<div class="card">';
            echo '<div class="card-body">';
            echo '<h5 class="card-title">' . htmlspecialchars($commentRow['user']) . '</h5>';
            echo '<p class="card-text">' . nl2br(htmlspecialchars($commentRow['comment'])) . '</p>';
            echo '<p class="text-muted small">Posted on: ' . htmlspecialchars($commentRow['created_at']) . '</p>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p class="text-muted">No comments yet. Be the first to comment!</p>';
    }
    echo '</div>';

    // Кнопка возврата
    echo '<a href="../index.php" class="btn btn-back">Back to Main Page</a>';
}
?>
<!-- JavaScript -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const slider = document.getElementById('slider');
        const images = slider.querySelectorAll('img');
        let currentIndex = 0;

        function showNextImage() {
            images[currentIndex].classList.remove('active');
            currentIndex = (currentIndex + 1) % images.length;
            images[currentIndex].classList.add('active');
        }

        if (images.length > 0) {
            images[currentIndex].classList.add('active');
            setInterval(showNextImage, 3000);
        }
    });
</script>
</body>
</html>