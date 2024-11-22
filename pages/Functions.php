<?php

// Подключение к базе данных
function connect($host = 'localhost', $user = 'root', $pass = '', $dbname = 'TouristAgency') {
    $mysqli = new mysqli($host, $user, $pass, $dbname);
    if ($mysqli->connect_errno) {
        die('Database connection error: ' . $mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");
    return $mysqli;
}

// Регистрация пользователя
function register($name, $pass, $pass2, $email) {
    $name = trim(htmlspecialchars($name));
    $pass = trim(htmlspecialchars($pass));
    $pass2 = trim(htmlspecialchars($pass2));
    $email = trim(htmlspecialchars($email));

    if (empty($name) || empty($pass) || empty($pass2) || empty($email)) {
        displayError("Fill all required fields!");
        return false;
    }

    if (strlen($name) < 6 || strlen($name) > 30 || strlen($pass) < 6 || strlen($pass) > 30) {
        displayError("Values length must be between 6 and 30!");
        return false;
    }

    if ($pass !== $pass2) {
        displayError("Passwords do not match!");
        return false;
    }

    $mysqli = connect();
    $stmt = $mysqli->prepare('INSERT INTO users (login, pass, email, roleid) VALUES (?, ?, ?, ?)');
    $hashedPass = password_hash($pass, PASSWORD_DEFAULT); // Используем password_hash
    $roleid = 2; // По умолчанию роль пользователя

    $stmt->bind_param("sssi", $name, $hashedPass, $email, $roleid);

    if (!$stmt->execute()) {
        if ($stmt->errno === 1062) {
            displayError("This login is already taken!");
        } else {
            displayError("Database error: " . $stmt->error);
        }
        return false;
    }

    return true;
}

// Авторизация пользователя
function login($name, $pass) {
    $name = trim(htmlspecialchars($name));
    $pass = trim(htmlspecialchars($pass));

    $mysqli = connect();

    // Ищем пользователя по логину
    $stmt = $mysqli->prepare('SELECT * FROM users WHERE login = ?');
    if (!$stmt) {
        echo "SQL Error: " . $mysqli->error;
        return false;
    }
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Проверяем пароль
        if (password_verify($pass, $row['pass'])) {
            $_SESSION['ruser'] = $name;
            if ($row['roleid'] == 1) {
                $_SESSION['radmin'] = $name; // Администратор
            }
            return true;
        } else {
            echo "<h3><span style='color:red;'>Incorrect password!</span></h3>";
            return false;
        }
    } else {
        echo "<h3><span style='color:red;'>No such user!</span></h3>";
        return false;
    }
}

// Вывод сообщения об ошибке
function displayError($message) {
    echo "<h3><span style='color:red;'>$message</span></h3>";
    echo "<script>setTimeout(() => window.location.reload(), 2000);</script>";
}
?>