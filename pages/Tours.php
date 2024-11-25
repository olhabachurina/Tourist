<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Tours</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .loading {
            font-style: italic;
            color: #aaa;
        }
        #hotels {
            display: none;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Select Tours</h2>
    <hr>

    <div id="error-message" class="alert alert-danger d-none"></div>

    <!-- Country Selection -->
    <div class="mb-3">
        <label for="country" class="form-label">Select Country:</label>
        <select id="country" class="form-select">
            <option value="0">-- Select Country --</option>
            <?php

            require_once 'functions.php';
            $mysqli = connect();
            $result = $mysqli->query("SELECT id, country FROM countries ORDER BY country");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['id']}'>{$row['country']}</option>";
            }
            ?>
        </select>
    </div>

    <!-- City Selection -->
    <div class="mb-3">
        <label for="city" class="form-label">Select City:</label>
        <select id="city" class="form-select" disabled>
            <option value="0">-- Select City --</option>
        </select>
        <p id="city-loading" class="loading d-none">Loading cities...</p>
    </div>

    <!-- Hotels -->
    <div id="hotels" class="mt-4">
        <h4>Hotels</h4>
        <p id="hotels-loading" class="loading d-none">Loading hotels...</p>
        <div id="hotels-list">
            <p>Select a city to view hotels</p>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {

        function resetCityAndHotels() {
            $("#city").prop("disabled", true).html("<option value='0'>-- Select City --</option>");
            $("#hotels").hide();
            $("#hotels-list").html("<p>Select a city to view hotels</p>");
        }

        // Загрузка городов
        $("#country").change(function () {
            const countryId = $(this).val();
            resetCityAndHotels();

            if (countryId !== "0") {
                $("#city-loading").removeClass("d-none");
                $.ajax({
                    type: "POST",
                    url: "index.php",
                    data: { action: "getCities", country_id: countryId },
                    dataType: "json",
                    success: function (response) {
                        $("#city-loading").addClass("d-none");
                        $("#city").prop("disabled", false).html("<option value='0'>-- Select City --</option>");
                        response.forEach(function (city) {
                            $("#city").append(`<option value="${city.id}">${city.city}</option>`);
                        });
                    },
                    error: function () {
                        $("#city-loading").addClass("d-none");
                        $("#error-message").text("Failed to load cities. Please try again.").removeClass("d-none");
                    }
                });
            }
        });

        // Загрузка отелей
        $("#city").change(function () {
            const cityId = $(this).val();

            if (cityId !== "0") {
                $("#hotels").show();
                $("#hotels-loading").removeClass("d-none");
                $.ajax({
                    type: "POST",
                    url: "index.php",
                    data: { action: "getHotels", city_id: cityId },
                    dataType: "json",
                    success: function (response) {
                        $("#hotels-loading").addClass("d-none");
                        $("#hotels-list").html("");
                        if (response.length === 0) {
                            $("#hotels-list").html("<p>No hotels found.</p>");
                        } else {
                            let tableContent = `
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Hotel</th>
                                            <th>Country</th>
                                            <th>City</th>
                                            <th>Price</th>
                                            <th>Stars</th>
                                            <th>Link</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            `;
                            response.forEach(function (hotel) {
                                tableContent += `
                                    <tr>
                                        <td>${hotel.hotel}</td>
                                        <td>${hotel.country}</td>
                                        <td>${hotel.city}</td>
                                        <td>$${hotel.cost}</td>
                                        <td>${hotel.stars}</td>
                                        <td><a href="pages/hotelinfo.php?hotel=${hotel.id}" target="_blank">more info</a></td>
                                    </tr>
                                `;
                            });
                            tableContent += `
                                    </tbody>
                                </table>
                            `;
                            $("#hotels-list").html(tableContent);
                        }
                    },
                    error: function () {
                        $("#hotels-loading").addClass("d-none");
                        $("#error-message").text("Failed to load hotels. Please try again.").removeClass("d-none");
                    }
                });
            }
        });
    });
</script>
</body>
</html>