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

    <style>
        .promotions {
            padding-left: 0;
            padding-right: 0;
        }

        #promotions {
            overflow: scroll;
        }

        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #005cc7;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }

        .fs20 {
            font-size: 20px;
        }

        .fs15 {
            font-size: 15px;
        }
    </style>
</head>

<body>
    <?php
    $page->nav();
    ?>
    <?php
    $id = $_SESSION['id'];
    $sql = "SELECT * FROM `accounts` WHERE `id`='$id'";
    $result = $conn->query($sql)->fetch_assoc();
    $transfer_from = $result['account_no'];
    echo "<h1>Hello, " . $result['account_name'] . "</h1>";
    ?>

    <div class="container" style="margin-top: 20px;">
        <a href="/logout/" class="btn btn-primary" style="float: right;">Logout</a>
        <center>
            <button type="button" class="btn btn-success" style="margin-top: 60px; margin-bottom:40px;">Balance: <?php echo $result['balance']; ?></button>
        </center>
        <form method="POST">

            <div class="form-group mt-2">
                <label for="exampleInputEmail1">Beneficiary Account No</label>
                <input type="number" name="account_no" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Beneficiary Account No">

            </div>
            <div class="form-group mt-2">
                <label for="exampleInputPassword1">Amount</label>
                <input type="number" name="amount" value="0" class="form-control" id="exampleInputPassword1" placeholder="Amount">
            </div>

            <?php
            if (isset($_POST['transfer'])) {
                $transfer_to = $_POST['account_no'];
                $amount = $_POST['amount'];

                $sql = "SELECT `balance` FROM `accounts` WHERE `id`='$id'";
                $balance = $conn->query($sql)->fetch_assoc()['balance'];

                $transaction = true;
                if ($balance < $amount) {
                    $transaction = false;
                    $msg = "You have not sufficient funds!";
                }

                if ($transaction) {
                    $sql = "SELECT * FROM `accounts` WHERE `account_no`='$transfer_to'";
                    $check = $conn->query($sql);
                    if ($check->num_rows == 0) {
                        $transaction = false;
                        $msg = "Beneficiary Account No, doesn't exist!";
                    }
                }

                if ($transaction) {
                    //Inserting transaction
                    $date_t = date("Y-m-d H:i:s");
                    $sql = "INSERT INTO `transactions`(`transaction_type`, `transfer_from`, `transfer_to`, `amount`, `transaction_date`) VALUES ('transfer', '$transfer_from', '$transfer_to', '$amount', '$date_t' )";
                    $conn->query($sql);

                    //deducting amount
                    $sql = "UPDATE `accounts` SET `balance`=`balance`-$amount WHERE `account_no`='$transfer_from'";
                    $conn->query($sql);

                    //deducting amount
                    $sql = "UPDATE `accounts` SET `balance`=`balance`+$amount WHERE `account_no`='$transfer_to'";
                    $conn->query($sql);

                    echo '<script>alert("Transaction, Successfull!")</script>';
                    header("Location: http://localhost/user/");
                } else {
                    echo '
                    <div class="alert alert-warning mt-3" role="alert">' . $msg . '</div>';
                }
            }

            ?>

            <center>
                <button type="submit" name="transfer" class="btn btn-primary mt-2">Transfer</button>
            </center>
        </form>

        <div class="container" style="overflow: scroll;">
            <?php
            $sql = "SELECT * FROM `transactions` WHERE `transfer_from`='$transfer_from' OR `transfer_to`='$transfer_from' ORDER BY `id` DESC";
            $transactions = $conn->query($sql); ?>

            <table class="my-4">
                <tbody>
                    <tr>
                        <th class="text-center">transaction_type</th>
                        <th class="text-center">transfer_from</th>
                        <th class="text-center">transfer_to</th>
                        <th class="text-center">Amount</th>
                        <th class="text-center">transaction_date</th>
                    </tr>

                    <?php
                    while ($row = $transactions->fetch_assoc()) {
                        echo "<tr>";
                        echo '<td class="text-center">' . $row['transaction_type'] . '</td>';

                        // //transfer from
                        // if ($row['transfer_to'] == $transfer_from) {
                        //     echo '<td class="text-center">' . $row['transfer_from'] . '</td>';
                        // } else {
                        //     echo '<td class="text-center"></td>';
                        // }

                        // //transfer to
                        // if ($row['transfer_from'] == $transfer_from) {
                        //     echo '<td class="text-center">' . $row['transfer_to'] . '</td>';
                        // } else {
                        //     echo '<td class="text-center"></td>';
                        // }
                        echo '<td class="text-center">' . $row['transfer_from'] . '</td>';
                        echo '<td class="text-center">' . $row['transfer_to'] . '</td>';
                        echo '<td class="text-center">' . $row['amount'] . '</td>';
                        echo '<td class="text-center">' . $row['transaction_date'] . '</td>';
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>

        </div>
    </div>


    <!-- Optional JavaScript; choose one of the two! -->
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>