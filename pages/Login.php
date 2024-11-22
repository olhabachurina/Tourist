<?php
if (isset($_SESSION['ruser'])) {
    echo '<form action="index.php" class="form-inline pull-right" method="post">';
    echo '<h4>Hello, <span>' . $_SESSION['ruser'] . '</span>&nbsp;';
    echo '<input type="submit" value="Logout" id="ex" name="ex" class="btn btn-default btn-xs"></h4>';
    echo '</form>';

    if (isset($_POST['ex'])) {
        unset($_SESSION['ruser']);
        unset($_SESSION['radmin']);
        echo '<script>window.location.reload()</script>';
    }
} else {
    if (isset($_POST['press'])) {
        if (login($_POST['login'], $_POST['pass'])) {
            echo '<script>window.location.reload()</script>';
        }
    } else {
        echo '<form action="index.php" class="input-group input-group-sm pull-right" method="post">';
        echo '<input type="text" name="login" size="10" placeholder="Login" class="form-control">';
        echo '<input type="password" name="pass" size="10" placeholder="Password" class="form-control">';
        echo '<input type="submit" value="Login" id="press" name="press" class="btn btn-default btn-xs">';
        echo '</form>';
    }
}
?>