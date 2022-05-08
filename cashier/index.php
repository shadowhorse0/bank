<?php
session_start();
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
    <?php
    $id = $_SESSION['id'];
    $sql = "SELECT * FROM `employees` WHERE `id`='$id'";
    $result = $conn->query($sql)->fetch_assoc();
    echo "<h1>Hello, " . $result['employee_name'] . "</h1>";
    ?>

    <div class="container" style="margin-top: 20px;">
        <a href="/logout/" class="btn btn-primary" style="float: right; margin-bottom:30px;">Logout</a>
        <div style="clear:right;"></div>
        <form method="POST">
            <select name="transaction_type" class="form-select" aria-label="Default select example">
                <option value="withdraw">Withdraw</option>
                <option value="deposit">Deposit</option>
            </select>
            <div class="form-group mt-2">
                <label for="exampleInputEmail1">Account No</label>
                <input type="number" name="account_no" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Account No">

            </div>
            <div class="form-group mt-2">
                <label for="exampleInputPassword1">Amount</label>
                <input type="number" name="amount" value="0" class="form-control" id="exampleInputPassword1" placeholder="Amount">
            </div>

            <?php
            if (isset($_POST['process'])) {
                $transaction_type = $_POST['transaction_type'];
                $account_no = $_POST['account_no'];
                $amount = $_POST['amount'];

                $transaction = true;
                $sql = "SELECT * FROM `accounts` WHERE `account_no`='$account_no'";
                $result = $conn->query($sql);

                if ($result->num_rows == 0) {
                    $transaction = false;
                    $msg = "Account no, does not exist!";
                }

                //withdraw
                if ($transaction) {
                    if ($transaction_type == "withdraw") {
                        $sql = "SELECT `balance` FROM `accounts` WHERE `account_no`='$account_no'";
                        $balance = $conn->query($sql)->fetch_assoc()['balance'];
                        if ($balance < $amount) {
                            $transaction = false;
                            $msg = "You have not sufficient funds!";
                        } else {
                            //withdraw amount
                            $sql = "UPDATE `accounts` SET `balance`=`balance`-$amount WHERE `account_no`='$account_no'";
                            $conn->query($sql);
                        }
                    }
                }

                //deposit
                if ($transaction) {
                    if ($transaction_type == "deposit") {

                        //deposit amount
                        $sql = "UPDATE `accounts` SET `balance`=`balance`+$amount WHERE `account_no`='$account_no'";
                        $conn->query($sql);
                    }
                }

                if ($transaction) {
                    //Inserting transaction
                    $date_t = date("Y-m-d H:i:s");
                    if ($transaction_type == "withdraw") {
                        $sql = "INSERT INTO `transactions`(`transaction_type`,`transfer_from`, `amount`, `transaction_date`) VALUES ('$transaction_type',$account_no, '$amount', '$date_t' )";
                    } else {
                        $sql = "INSERT INTO `transactions`(`transaction_type`,`transfer_to`, `amount`, `transaction_date`) VALUES ('$transaction_type',$account_no, '$amount', '$date_t' )";
                    }

                    $conn->query($sql);

                    echo '<script>alert("Transaction, Successfull!")</script>';
                    // header("Location: http://localhost/cashier/");
                } else {
                    echo '
                    <div class="alert alert-warning mt-3" role="alert">' . $msg . '</div>';
                }
            }

            ?>

            <center>
                <button type="submit" name="process" class="btn btn-primary mt-2">Process</button>
            </center>
        </form>

    </div>


    <!-- Optional JavaScript; choose one of the two! -->
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>