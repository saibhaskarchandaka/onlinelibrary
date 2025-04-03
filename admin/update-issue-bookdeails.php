<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {   
    header('location:index.php');
} else { 
    // Fetch fine amount from tblfine
    $sql = "SELECT Fine FROM tblfine LIMIT 1";
    $query = $dbh->prepare($sql);
    $query->execute();
    $fineData = $query->fetch(PDO::FETCH_OBJ);
    
    $finePerDay = ($fineData) ? intval($fineData->Fine) : 1; // Default fine = 1 if not found

    // Get required parameters
    $days = isset($_GET['days']) ? intval($_GET['days']) : 0;
    $totalFine = $days * $finePerDay;

    if (isset($_POST['return'])) {
        $rid = intval($_GET['rid']);
        $fine = $_POST['fine'];
        $ISBNNumber = $_GET['ISBNNumber'];

        $rstatus = 1;

        // Update fine and return status
        $sql = "UPDATE tblissuedbookdetails SET fine = :fine, ReturnStatus = :rstatus WHERE id = :rid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':rid', $rid, PDO::PARAM_INT);
        $query->bindParam(':fine', $fine, PDO::PARAM_INT);
        $query->bindParam(':rstatus', $rstatus, PDO::PARAM_INT);
        $query->execute();

        // Prevent negative IssuedCopies
        $sql = "UPDATE tblbooks SET IssuedCopies = CASE 
                      WHEN IssuedCopies > 0 THEN IssuedCopies - 1 
                      ELSE 0 
                  END WHERE ISBNNumber = :ISBNNumber";
        $query = $dbh->prepare($sql);
        $query->bindParam(':ISBNNumber', $ISBNNumber, PDO::PARAM_STR);
        $query->execute();

        $_SESSION['msg'] = "Book Returned successfully";
        header('location:manage-issued-books.php');
    }
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Issued Book Details</title>
    
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
                    <h4 class="header-line">Issued Book Details</h4>
                </div>
            </div>

            <div class="row">
                <div class="col-md-10 col-sm-6 col-xs-12 col-md-offset-1">
                    <div class="panel panel-info">
                        <div class="panel-heading">Issued Book Details</div>
                        <div class="panel-body">
                            <form role="form" method="post">
                                <?php 
                                $rid = intval($_GET['rid']);
                                $sql = "SELECT tblstudents.FullName, tblbooks.BookName, tblbooks.id, 
                                        tblbooks.ISBNNumber, tblissuedbookdetails.IssuesDate, 
                                        tblissuedbookdetails.ReturnDate, tblissuedbookdetails.id as rid, 
                                        tblissuedbookdetails.fine, tblissuedbookdetails.ReturnStatus 
                                        FROM tblissuedbookdetails 
                                        JOIN tblstudents ON tblstudents.StudentId = tblissuedbookdetails.StudentId 
                                        JOIN tblbooks ON tblbooks.id = tblissuedbookdetails.BookId 
                                        WHERE tblissuedbookdetails.id = :rid";
                                $query = $dbh->prepare($sql);
                                $query->bindParam(':rid', $rid, PDO::PARAM_INT);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                
                                if ($query->rowCount() > 0) {
                                    foreach ($results as $result) { ?>                                      
                                        <div class="form-group">
                                            <label>Student Name :</label>
                                            <?php echo htmlentities($result->FullName); ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Book Name :</label>
                                            <?php echo htmlentities($result->BookName); ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Book ID :</label>
                                            <?php echo htmlentities($result->id); ?>
                                        </div>

                                        <div class="form-group">
                                            <label>ISBN :</label>
                                            <?php echo htmlentities($result->ISBNNumber); ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Book Issued Date :</label>
                                            <?php echo htmlentities($result->IssuesDate); ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Book Returned Date :</label>
                                            <?php echo $result->ReturnDate ? htmlentities($result->ReturnDate) : "Not Returned Yet"; ?>
                                        </div>

                                        <div class="form-group">
                                            <?php
                                            $flag = 0;
                                            if (strpos($_GET['status'], 'exceeded') !== false && !$result->ReturnDate) {
                                                $flag = 1;
                                            ?>
                                            <span><b>Fine To Be Paid:</b> <?php echo htmlentities($totalFine); ?> Rs</span>
                                            <input type="hidden" name="fine" value="<?php echo $totalFine; ?>">
                                            <?php } ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Fine (in Rs) :</label>
                                            <?php
                                            if ($flag === 1) { ?>
                                                <input class="form-control" type="text" name="fine" id="fine" value="<?php echo $totalFine; ?>" readonly />
                                            <?php } else { 
                                                echo $result->fine !== NULL ? htmlentities($result->fine) : "0";
                                            } ?>
                                        </div>

                                        <?php if ($result->ReturnStatus == 0) { ?>
                                        <div class="form-group">
                                            <button type="submit" name="return" id="submit" class="btn btn-info">Return Book</button>
                                        </div>
                                        <br>
                                        <?php } } } ?>
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
