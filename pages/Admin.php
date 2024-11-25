<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("functions.php");

// Проверяем права администратора
if (!isset($_SESSION['radmin'])) {
    echo json_encode(["error" => "Access Denied: For Administrators Only!"]);
    exit();
}

$mysqli = connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // Управление странами
    if ($action === 'getCountries') {
        $query = "SELECT * FROM countries ORDER BY id";
        $result = $mysqli->query($query);

        $countries = [];
        while ($row = $result->fetch_assoc()) {
            $countries[] = $row;
        }
        echo json_encode($countries);
        exit;
    }

    if ($action === 'addCountry') {
        $country = isset($_POST['country']) ? trim(htmlspecialchars($_POST['country'])) : '';
        if (!empty($country)) {
            $stmt = $mysqli->prepare("INSERT INTO countries (country) VALUES (?)");
            $stmt->bind_param("s", $country);
            if ($stmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["error" => "Failed to add country."]);
            }
        } else {
            echo json_encode(["error" => "Country name cannot be empty."]);
        }
        exit;
    }

    if ($action === 'deleteCountry') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id > 0) {
            $stmt = $mysqli->prepare("DELETE FROM countries WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["error" => "Failed to delete country."]);
            }
        } else {
            echo json_encode(["error" => "Invalid ID."]);
        }
        exit;
    }

    // Управление городами
    if ($action === 'getCities') {
        $query = "SELECT ci.id, ci.city, co.country FROM cities ci JOIN countries co ON ci.countryid = co.id ORDER BY ci.id";
        $result = $mysqli->query($query);

        $cities = [];
        while ($row = $result->fetch_assoc()) {
            $cities[] = $row;
        }
        echo json_encode($cities);
        exit;
    }

    if ($action === 'addCity') {
        $city = isset($_POST['city']) ? trim(htmlspecialchars($_POST['city'])) : '';
        $countryId = isset($_POST['country_id']) ? intval($_POST['country_id']) : 0;
        if (!empty($city) && $countryId > 0) {
            $stmt = $mysqli->prepare("INSERT INTO cities (city, countryid) VALUES (?, ?)");
            $stmt->bind_param("si", $city, $countryId);
            if ($stmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["error" => "Failed to add city."]);
            }
        } else {
            echo json_encode(["error" => "City name or country ID is invalid."]);
        }
        exit;
    }

    if ($action === 'deleteCity') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id > 0) {
            $stmt = $mysqli->prepare("DELETE FROM cities WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["error" => "Failed to delete city."]);
            }
        } else {
            echo json_encode(["error" => "Invalid ID."]);
        }
        exit;
    }

    // Управление отелями
    if ($action === 'getHotels') {
        $query = "SELECT ho.id, ho.hotel, co.country, ci.city, ho.stars, ho.cost 
                  FROM hotels ho 
                  JOIN cities ci ON ho.cityid = ci.id 
                  JOIN countries co ON ho.countryid = co.id 
                  ORDER BY ho.id";
        $result = $mysqli->query($query);

        $hotels = [];
        while ($row = $result->fetch_assoc()) {
            $hotels[] = $row;
        }
        echo json_encode($hotels);
        exit;
    }

    if ($action === 'addHotel') {
        $hotel = isset($_POST['hotel']) ? trim(htmlspecialchars($_POST['hotel'])) : '';
        $cityId = isset($_POST['city_id']) ? intval($_POST['city_id']) : 0;
        $stars = isset($_POST['stars']) ? intval($_POST['stars']) : 0;
        $cost = isset($_POST['cost']) ? intval($_POST['cost']) : 0;
        $info = isset($_POST['info']) ? trim(htmlspecialchars($_POST['info'])) : '';

        if (!empty($hotel) && $cityId > 0 && $stars >= 1 && $stars <= 5 && $cost > 0) {
            $stmt = $mysqli->prepare("INSERT INTO hotels (hotel, cityid, countryid, stars, cost, info) 
                                      VALUES (?, ?, (SELECT countryid FROM cities WHERE id = ?), ?, ?, ?)");
            $stmt->bind_param("siiids", $hotel, $cityId, $cityId, $stars, $cost, $info);
            if ($stmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["error" => "Failed to add hotel."]);
            }
        } else {
            echo json_encode(["error" => "Invalid data provided."]);
        }
        exit;
    }

    if ($action === 'deleteHotel') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id > 0) {
            $stmt = $mysqli->prepare("DELETE FROM hotels WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["error" => "Failed to delete hotel."]);
            }
        } else {
            echo json_encode(["error" => "Invalid ID."]);
        }
        exit;
    }

    // Управление изображениями
    if ($action === 'uploadImages') {
        $hotelId = isset($_POST['hotel_id']) ? intval($_POST['hotel_id']) : 0;

        if ($hotelId > 0 && isset($_FILES['images'])) {
            $uploadedFiles = [];
            foreach ($_FILES['images']['name'] as $key => $name) {
                if ($_FILES['images']['error'][$key] === 0) {
                    $target = "images/" . basename($name);
                    if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target)) {
                        $stmt = $mysqli->prepare("INSERT INTO images (hotelid, imagepath) VALUES (?, ?)");
                        $stmt->bind_param("is", $hotelId, $target);
                        $stmt->execute();
                        $uploadedFiles[] = $target;
                    }
                }
            }
            echo json_encode(["success" => true, "files" => $uploadedFiles]);
        } else {
            echo json_encode(["error" => "Invalid hotel ID or no files uploaded."]);
        }
        exit;
    }
}
?>