<?php
// Проверяем, активна ли сессия
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Запускаем сессию только если она еще не активна
}

include_once("functions.php");

// Проверяем, имеет ли пользователь права администратора
if (!isset($_SESSION['radmin'])) {
    echo "<h3 class='text-center text-danger mt-5'>Access Denied: For Administrators Only!</h3>";
    exit(); // Прерываем выполнение скрипта, если пользователь не администратор
}

// Подключение к базе данных
$mysqli = connect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3 class="text-center mb-4">Admin Panel</h3>

    <!-- Управление странами -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <h4 class="card-title text-center mt-3">Manage Countries</h4>
                <div class="card-body">
                    <?php
                    $sel = 'SELECT * FROM countries ORDER BY id';
                    $res = $mysqli->query($sel);

                    echo '<form action="index.php?page=4" method="post">';
                    echo '<table class="table table-striped table-hover">';
                    echo '<thead><tr><th>ID</th><th>Country</th><th>Select</th></tr></thead>';
                    echo '<tbody>';
                    while ($row = mysqli_fetch_array($res, MYSQLI_NUM)) {
                        echo '<tr>';
                        echo '<td>' . $row[0] . '</td>';
                        echo '<td>' . $row[1] . '</td>';
                        echo '<td><input type="checkbox" name="cb' . $row[0] . '"></td>';
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                    mysqli_free_result($res);

                    echo '<input type="text" name="country" class="form-control mb-3" placeholder="Add Country">';
                    echo '<div class="d-flex justify-content-between">';
                    echo '<button type="submit" name="addcountry" class="btn btn-info">Add</button>';
                    echo '<button type="submit" name="delcountry" class="btn btn-warning">Delete</button>';
                    echo '</div>';
                    echo '</form>';

                    if (isset($_POST['addcountry'])) {
                        $country = trim(htmlspecialchars($_POST['country']));
                        if ($country != "") {
                            $stmt = $mysqli->prepare('INSERT INTO countries (country) VALUES (?)');
                            $stmt->bind_param("s", $country);
                            $stmt->execute();
                            echo "<script>window.location=document.URL;</script>";
                        }
                    }

                    if (isset($_POST['delcountry'])) {
                        foreach ($_POST as $k => $v) {
                            if (substr($k, 0, 2) == "cb") {
                                $idc = substr($k, 2);
                                $stmt = $mysqli->prepare('DELETE FROM countries WHERE id = ?');
                                $stmt->bind_param("i", $idc);
                                $stmt->execute();
                            }
                        }
                        echo "<script>window.location=document.URL;</script>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Управление городами -->
        <div class="col-md-6">
            <div class="card">
                <h4 class="card-title text-center mt-3">Manage Cities</h4>
                <div class="card-body">
                    <?php
                    echo '<form action="index.php?page=4" method="post">';
                    $sel = 'SELECT ci.id, ci.city, co.country FROM countries co, cities ci WHERE ci.countryid=co.id ORDER BY id';
                    $res = $mysqli->query($sel);

                    echo '<table class="table table-striped table-hover">';
                    echo '<thead><tr><th>ID</th><th>City</th><th>Country</th><th>Select</th></tr></thead>';
                    echo '<tbody>';
                    while ($row = mysqli_fetch_array($res, MYSQLI_NUM)) {
                        echo '<tr>';
                        echo '<td>' . $row[0] . '</td>';
                        echo '<td>' . $row[1] . '</td>';
                        echo '<td>' . $row[2] . '</td>';
                        echo '<td><input type="checkbox" name="ci' . $row[0] . '"></td>';
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                    mysqli_free_result($res);

                    $res = $mysqli->query('SELECT * FROM countries ORDER BY id');
                    echo '<select name="countryname" class="form-select mb-3">';
                    while ($row = mysqli_fetch_array($res, MYSQLI_NUM)) {
                        echo '<option value="' . $row[0] . '">' . $row[1] . '</option>';
                    }
                    echo '</select>';
                    echo '<input type="text" name="city" class="form-control mb-3" placeholder="Add City">';
                    echo '<div class="d-flex justify-content-between">';
                    echo '<button type="submit" name="addcity" class="btn btn-info">Add</button>';
                    echo '<button type="submit" name="delcity" class="btn btn-warning">Delete</button>';
                    echo '</div>';
                    echo '</form>';

                    if (isset($_POST['addcity'])) {
                        $city = trim(htmlspecialchars($_POST['city']));
                        if ($city != "") {
                            $countryid = $_POST['countryname'];
                            $stmt = $mysqli->prepare('INSERT INTO cities (city, countryid) VALUES (?, ?)');
                            $stmt->bind_param("si", $city, $countryid);
                            $stmt->execute();
                            echo "<script>window.location=document.URL;</script>";
                        }
                    }

                    if (isset($_POST['delcity'])) {
                        foreach ($_POST as $k => $v) {
                            if (substr($k, 0, 2) == "ci") {
                                $idc = substr($k, 2);
                                $stmt = $mysqli->prepare('DELETE FROM cities WHERE id = ?');
                                $stmt->bind_param("i", $idc);
                                $stmt->execute();
                            }
                        }
                        echo "<script>window.location=document.URL;</script>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Управление отелями -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <h4 class="card-title text-center mt-3">Manage Hotels</h4>
                <div class="card-body">
                    <?php
                    $sel = 'SELECT ho.id, ho.hotel, co.country, ci.city, ho.stars, ho.cost 
                            FROM hotels ho 
                            JOIN countries co ON ho.countryid = co.id
                            JOIN cities ci ON ho.cityid = ci.id
                            ORDER BY ho.id';
                    $res = $mysqli->query($sel);

                    echo '<form action="index.php?page=4" method="post">';
                    echo '<table class="table table-striped table-hover">';
                    echo '<thead><tr><th>ID</th><th>Hotel</th><th>Country</th><th>City</th><th>Stars</th><th>Cost</th><th>Select</th></tr></thead>';
                    echo '<tbody>';
                    while ($row = mysqli_fetch_array($res, MYSQLI_NUM)) {
                        echo '<tr>';
                        echo '<td>' . $row[0] . '</td>';
                        echo '<td>' . $row[1] . '</td>';
                        echo '<td>' . $row[2] . '</td>';
                        echo '<td>' . $row[3] . '</td>';
                        echo '<td>' . $row[4] . '</td>';
                        echo '<td>' . $row[5] . '</td>';
                        echo '<td><input type="checkbox" name="hb' . $row[0] . '"></td>';
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                    mysqli_free_result($res);

                    $cities = $mysqli->query('SELECT ci.id, ci.city, co.country 
                                              FROM cities ci 
                                              JOIN countries co ON ci.countryid = co.id');
                    echo '<select name="hotel_city" class="form-select mb-3">';
                    while ($row = mysqli_fetch_array($cities, MYSQLI_NUM)) {
                        echo '<option value="' . $row[0] . '">' . $row[2] . ' - ' . $row[1] . '</option>';
                    }
                    mysqli_free_result($cities);

                    echo '<input type="text" name="hotel_name" class="form-control mb-3" placeholder="Hotel Name">';
                    echo '<input type="number" name="hotel_stars" class="form-control mb-3" placeholder="Stars (1-5)" min="1" max="5">';
                    echo '<input type="number" name="hotel_cost" class="form-control mb-3" placeholder="Cost">';
                    echo '<textarea name="hotel_info" class="form-control mb-3" placeholder="Description"></textarea>';
                    echo '<div class="d-flex justify-content-between">';
                    echo '<button type="submit" name="add_hotel" class="btn btn-info">Add Hotel</button>';
                    echo '<button type="submit" name="del_hotel" class="btn btn-warning">Delete Selected</button>';
                    echo '</div>';
                    echo '</form>';

                    if (isset($_POST['add_hotel'])) {
                        $hotel_name = trim(htmlspecialchars($_POST['hotel_name']));
                        $hotel_stars = intval($_POST['hotel_stars']);
                        $hotel_cost = intval($_POST['hotel_cost']);
                        $hotel_info = trim(htmlspecialchars($_POST['hotel_info']));
                        $city_id = intval($_POST['hotel_city']);

                        if (!empty($hotel_name) && $hotel_stars >= 1 && $hotel_stars <= 5 && $hotel_cost > 0) {
                            $stmt = $mysqli->prepare('INSERT INTO hotels (hotel, cityid, countryid, stars, cost, info) 
                                                      VALUES (?, ?, (SELECT countryid FROM cities WHERE id = ?), ?, ?, ?)');
                            $stmt->bind_param("siiids", $hotel_name, $city_id, $city_id, $hotel_stars, $hotel_cost, $hotel_info);
                            $stmt->execute();
                            echo "<script>window.location=document.URL;</script>";
                        }
                    }

                    if (isset($_POST['del_hotel'])) {
                        foreach ($_POST as $k => $v) {
                            if (substr($k, 0, 2) == "hb") {
                                $hotel_id = intval(substr($k, 2));
                                $stmt = $mysqli->prepare('DELETE FROM hotels WHERE id = ?');
                                $stmt->bind_param("i", $hotel_id);
                                $stmt->execute();
                            }
                        }
                        echo "<script>window.location=document.URL;</script>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Управление изображениями -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <h4 class="card-title text-center mt-3">Manage Hotel Images</h4>
                <div class="card-body">
                    <?php
                    echo '<form action="index.php?page=4" method="post" enctype="multipart/form-data">';
                    $hotels = $mysqli->query('SELECT id, hotel FROM hotels');
                    echo '<select name="image_hotel" class="form-select mb-3">';
                    while ($row = mysqli_fetch_array($hotels, MYSQLI_NUM)) {
                        echo '<option value="' . $row[0] . '">' . $row[1] . '</option>';
                    }
                    mysqli_free_result($hotels);

                    echo '</select>';
                    echo '<input type="file" name="hotel_images[]" class="form-control mb-3" multiple accept="image/*">';
                    echo '<button type="submit" name="add_image" class="btn btn-info">Upload Images</button>';
                    echo '</form>';

                    if (isset($_POST['add_image'])) {
                        $hotel_id = intval($_POST['image_hotel']);
                        foreach ($_FILES['hotel_images']['name'] as $key => $name) {
                            if ($_FILES['hotel_images']['error'][$key] == 0) {
                                $target = 'images/' . basename($name);
                                if (move_uploaded_file($_FILES['hotel_images']['tmp_name'][$key], $target)) {
                                    $stmt = $mysqli->prepare('INSERT INTO images (hotelid, imagepath) VALUES (?, ?)');
                                    $stmt->bind_param("is", $hotel_id, $target);
                                    $stmt->execute();
                                }
                            }
                        }
                        echo "<script>window.location=document.URL;</script>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-5">
        <p>&copy; 2024 Travel Agency. All rights reserved.</p>
    </footer>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>