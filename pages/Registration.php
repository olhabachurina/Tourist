<h3 class="text-center text-light bg-dark py-3 rounded shadow">Registration Form</h3>
<div class="container my-5">
    <?php
    if (!isset($_POST['regbtn'])) {
        ?>
        <form action="index.php?page=3" method="post" class="p-4 rounded shadow-lg" style="background-color: #f9f9f9;">
            <div class="form-group mb-4">
                <label for="login" class="form-label fw-bold">Login:</label>
                <input type="text" class="form-control" id="login" name="login" placeholder="Enter your login" required>
            </div>
            <div class="form-group mb-4">
                <label for="pass1" class="form-label fw-bold">Password:</label>
                <input type="password" class="form-control" id="pass1" name="pass1" placeholder="Enter your password" required>
            </div>
            <div class="form-group mb-4">
                <label for="pass2" class="form-label fw-bold">Confirm Password:</label>
                <input type="password" class="form-control" id="pass2" name="pass2" placeholder="Confirm your password" required>
            </div>
            <div class="form-group mb-4">
                <label for="email" class="form-label fw-bold">Email Address:</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email address" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" name="regbtn">Register</button>
        </form>
        <?php
    } else {
        if (register($_POST['login'], $_POST['pass1'], $_POST['pass2'], $_POST['email'])) {
            echo "<div class='alert alert-success mt-3 text-center'>New User Added!</div>";
        }
    }
    ?>
</div>