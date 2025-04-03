<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {   
    header('location:index.php');
    exit();
}

if (isset($_POST['update'])) {
    $fine = $_POST['finetf'];

    // Check if a fine record exists
    $sql = "SELECT COUNT(*) as count FROM tblfine";
    $query = $dbh->prepare($sql);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    $listedbooks = $result['count'];

    if ($listedbooks == 0) {
        // Insert a new fine value if not exists
        $sql = "INSERT INTO tblfine (fine) VALUES (:fine)";
    } else {    
        // Update the existing fine value
        $sql = "UPDATE tblfine SET fine = :fine";
    }

    $query = $dbh->prepare($sql);
    $query->bindParam(':fine', $fine, PDO::PARAM_STR);
    $query->execute();
    
    // Check if rows were affected
    if ($query->rowCount() > 0) {
        $_SESSION['msg'] = "Fine Updated successfully";
    } else {
        $_SESSION['error'] = "Something went wrong. Please try again";
    }
    
    header('location:set-fine.php');
    exit();
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Set Fine</title>
    
    <!-- BOOTSTRAP CORE STYLE -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

</head>
<body>
    <!-- MENU SECTION START -->
    <?php include('includes/header.php'); ?>
    <!-- MENU SECTION END -->

    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Set Fine</h4>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Fine Update Section
                        </div>
                        <div class="panel-body">
                            <?php if (!empty($_SESSION['msg'])) { ?>
                                <div class="alert alert-success"><?php echo htmlentities($_SESSION['msg']); ?></div>
                                <?php unset($_SESSION['msg']); ?>
                            <?php } ?>
                            <?php if (!empty($_SESSION['error'])) { ?>
                                <div class="alert alert-danger"><?php echo htmlentities($_SESSION['error']); ?></div>
                                <?php unset($_SESSION['error']); ?>
                            <?php } ?>

                            <form role="form" method="post">
                                <div class="form-group">
                                    <label>Fine Per Day</label>
                                    <input class="form-control" type="number" name="finetf" required />
                                </div>
                                <button type="submit" name="update" class="btn btn-info">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT FILES -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
