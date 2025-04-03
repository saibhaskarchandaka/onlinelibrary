<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['login']) == 0) {   
    header('location:index.php');
} else { 
    $sid = $_SESSION['stdid'];

    $sql = "SELECT tblbooks.BookName, tblbooks.ISBNNumber, 
                   tblissuedbookdetails.IssuesDate, tblissuedbookdetails.ReturnDate, 
                   tblissuedbookdetails.id as rid, COALESCE(tblissuedbookdetails.fine, 0) as fine 
            FROM tblissuedbookdetails 
            JOIN tblstudents ON tblstudents.StudentId = tblissuedbookdetails.StudentId 
            JOIN tblbooks ON tblbooks.id = tblissuedbookdetails.BookId 
            WHERE tblstudents.StudentId = :sid 
            ORDER BY tblissuedbookdetails.id DESC";

    $query = $dbh->prepare($sql);
    $query->bindParam(':sid', $sid, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Issued Books</title>

    <!-- BOOTSTRAP CORE STYLE -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <!-- FONT AWESOME STYLE -->
    <link href="assets/css/font-awesome.css" rel="stylesheet">
    <!-- DATATABLE STYLE -->
    <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <!-- CUSTOM STYLE -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- MENU SECTION -->
<?php include('includes/header.php'); ?>

<div class="content-wrapper">
    <div class="container">
        <div class="row pad-botm">
            <div class="col-md-12">
                <h4 class="header-line">Manage Issued Books</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <!-- Advanced Tables -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Issued Books
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr class="bg-primary text-white">
                                        <th>#</th>
                                        <th>Book Name</th>
                                        <th>ISBN</th>
                                        <th>Issued Date</th>
                                        <th>Return Date</th>
                                        <th>Fine (Rs)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $cnt = 1;
                                    if ($query->rowCount() > 0) {
                                        foreach ($results as $result) { ?>                                      
                                            <tr>
                                                <td class="text-center"><?php echo htmlentities($cnt); ?></td>
                                                <td><?php echo htmlentities($result->BookName); ?></td>
                                                <td><?php echo htmlentities($result->ISBNNumber); ?></td>
                                                <td><?php echo date("d-m-Y", strtotime($result->IssuesDate)); ?></td>
                                                <td>
                                                    <?php if ($result->ReturnDate == "") { ?>
                                                        <span class="text-danger">Not Returned Yet</span>
                                                    <?php } else {
                                                        echo date("d-m-Y", strtotime($result->ReturnDate));
                                                    } ?>
                                                </td>
                                                <td class="text-center"><?php echo htmlentities($result->fine); ?></td>
                                            </tr>
                                        <?php $cnt++; 
                                        }
                                    } else { ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-danger">No records found</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--End Advanced Tables -->
            </div>
        </div>
    </div>
</div>

<!-- JAVASCRIPT FILES -->
<script src="assets/js/jquery-1.10.2.js"></script>
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/dataTables/jquery.dataTables.js"></script>
<script src="assets/js/dataTables/dataTables.bootstrap.js"></script>
<script src="assets/js/custom.js"></script>

</body>
</html>
<?php } ?>
