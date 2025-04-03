<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0); // Hide errors but log them for debugging
include('includes/config.php');

if (!isset($_SESSION['alogin'])) {  
    header('location:index.php');
    exit();
}

// Function to fetch count from database
function fetchCount($dbh, $table, $condition = "") {
    $sql = "SELECT COUNT(id) as count FROM $table $condition";
    $query = $dbh->prepare($sql);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}

// Fetch statistics
$listdbooks = fetchCount($dbh, "tblbooks");
$issuedbooks = fetchCount($dbh, "tblissuedbookdetails");
$returnedbooks = fetchCount($dbh, "tblissuedbookdetails", "WHERE ReturnStatus=1");
$regstds = fetchCount($dbh, "tblstudents");
$listdathrs = fetchCount($dbh, "tblauthors");
$listdcats = fetchCount($dbh, "tblcategory");

// Fetch fine
$sqlFine = "SELECT fine FROM tblfine LIMIT 1";
$queryFine = $dbh->prepare($sqlFine);
$queryFine->execute();
$fineData = $queryFine->fetch(PDO::FETCH_ASSOC);
$fine = $fineData['fine'] ?? "Not Set";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="Admin Dashboard - Library Management System" />
    <meta name="author" content="Your Name" />
    <title>Library Management System | Admin Dashboard</title>

    <!-- Bootstrap & FontAwesome -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>
<body>

<!-- Header -->
<?php include('includes/header.php'); ?>

<!-- Content -->
<div class="content-wrapper">
    <div class="container">
        <div class="row pad-botm">
            <div class="col-md-12">
                <h4 class="header-line">ADMIN DASHBOARD</h4>
            </div>
        </div>

        <div class="row">
            <!-- Books Listed -->
            <div class="col-md-3 col-sm-6">
                <div class="alert alert-success text-center">
                    <i class="fa fa-book fa-5x"></i>
                    <h3><?= htmlentities($listdbooks); ?></h3>
                    <span>Books Listed</span>
                </div>
            </div>

            <!-- Times Book Issued -->
            <div class="col-md-3 col-sm-6">
                <div class="alert alert-info text-center">
                    <i class="fa fa-bars fa-5x"></i>
                    <h3><?= htmlentities($issuedbooks); ?></h3>
                    <span>Times Book Issued</span>
                </div>
            </div>

            <!-- Books Returned -->
            <div class="col-md-3 col-sm-6">
                <div class="alert alert-warning text-center">
                    <i class="fa fa-recycle fa-5x"></i>
                    <h3><?= htmlentities($returnedbooks); ?></h3>
                    <span>Times Books Returned</span>
                </div>
            </div>

            <!-- Registered Users -->
            <div class="col-md-3 col-sm-6">
                <div class="alert alert-danger text-center">
                    <i class="fa fa-users fa-5x"></i>
                    <h3><?= htmlentities($regstds); ?></h3>
                    <span>Registered Users</span>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Publications Listed -->
            <div class="col-md-3 col-sm-6">
                <div class="alert alert-success text-center">
                    <i class="fa fa-user fa-5x"></i>
                    <h3><?= htmlentities($listdathrs); ?></h3>
                    <span>Publications Listed</span>
                </div>
            </div>

            <!-- Listed Categories -->
            <div class="col-md-3 col-sm-6">
                <div class="alert alert-info text-center">
                    <i class="fa fa-file-archive-o fa-5x"></i>
                    <h3><?= htmlentities($listdcats); ?></h3>
                    <span>Listed Categories</span>
                </div>
            </div>

            <!-- Current Fine Per Day -->
            <div class="col-md-3 col-sm-6">
                <div class="alert alert-info text-center">
                    <i class="fa fa-money fa-5x"></i>
                    <h3><?= htmlentities($fine); ?></h3>
                    <span>Current Fine Per Day</span>
                </div>
            </div>
        </div>

        <!-- Image Carousel -->
        

                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="assets/js/jquery-1.10.2.js"></script>
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/custom.js"></script>
</body>
</html>
