<?php
include $_SERVER['DOCUMENT_ROOT'] . '/classes/page.php';
include $_SERVER['DOCUMENT_ROOT'] . '/db/db.php';

$page = new page();
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="/logo.jpg">
    <link rel="icon" type="image/png" sizes="32x32" href="/logo.jpg">
    <link rel="icon" type="image/png" sizes="16x16" href="/logo.jpg">

    <title>AVP Bank</title>
</head>

<body>
    <?php
    $page->nav();
    ?>

    <div class="container" style="margin-top: 100px;">
        <form method="POST">
            <select name="wru" class="form-select" aria-label="Default select example">
                <option value="user">User</option>
                <option value="cashier">Cashier</option>
            </select>
            <div class="form-group mt-2">
                <label for="exampleInputEmail1">Email address</label>
                <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">

            </div>
            <div class="form-group mt-2">
                <label for="exampleInputPassword1">Password</label>
                <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
            </div>

            <?php
            if (isset($_POST['login'])) {
                $wru = $_POST['wru'];
                $email = $_POST['email'];
                $password = $_POST['password'];

                if ($wru == "user") {
                    $sql = "SELECT * FROM `accounts` WHERE `email`='$email' AND `password`='$password'";
                    $result = $conn->query($sql);
                } else if ($wru == "bank_manager" || $wru == "cashier") {
                    $sql = "SELECT * FROM `employees` WHERE `email`='$email' AND `password`='$password'";
                    $result = $conn->query($sql);
                }
                if ($result->num_rows > 0) {
                    session_start();
                    $result = $result->fetch_assoc();
                    $_SESSION['id'] = $result['id'];
                    if ($wru == "user") {
                        header("Location: http://localhost/user/");
                    } else if ($wru == "bank_manager") {
                        header("Location: http://localhost/manager/");
                    } else if ($wru == "cashier") {
                        header("Location: http://localhost/cashier/");
                    }
                } else {
                    echo '
                  <div class="alert alert-warning mt-3" role="alert">
                        Invalid Credentials!
                  </div>
                 ';
                }
            }

            ?>

            <center>
                <button type="submit" name="login" class="btn btn-primary mt-2">Log In</button>
            </center>
        </form>
    </div>


    <!-- Optional JavaScript; choose one of the two! -->
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>