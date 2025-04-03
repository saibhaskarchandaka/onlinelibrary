<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/config.php');

// Redirect if not logged in
if (strlen($_SESSION['alogin']) == 0) {   
    header('location:index.php');
    exit();
}

if (isset($_POST['issue'])) {
    $studentid = strtoupper($_POST['studentid']);
    $bookid = $_POST['bookdetails'];

    try {
        // Start Transaction
        $dbh->beginTransaction();

        // Insert Issue Record
        $sql = "INSERT INTO tblissuedbookdetails (StudentID, BookId) VALUES (:studentid, :bookid)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':studentid', $studentid, PDO::PARAM_STR);
        $query->bindParam(':bookid', $bookid, PDO::PARAM_STR);
        $query->execute();

        // Update IssuedCopies Count
        $sql = "UPDATE tblbooks SET IssuedCopies = IssuedCopies - 1 WHERE id = :bookid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':bookid', $bookid, PDO::PARAM_STR);
        $query->execute();

        // Commit Transaction
        $dbh->commit();

        $_SESSION['msg'] = "Book issued successfully";
        header('location:manage-issued-books.php');
        exit();
    } catch (PDOException $e) {
        $dbh->rollBack();
        $_SESSION['error'] = "Something went wrong: " . $e->getMessage();
        header('location:manage-issued-books.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Issue a New Book | Library Management System</title>
    
    <!-- Stylesheets -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />

    <!-- jQuery -->
    <script src="assets/js/jquery-1.10.2.js"></script>

    <script>
        function getStudent() {
            $("#loaderIcon").show();
            $.ajax({
                url: "get_student.php",
                type: "POST",
                data: { studentid: $("#studentid").val() },
                success: function(data) {
                    $("#get_student_name").html(data);
                    $("#loaderIcon").hide();
                },
                error: function() {
                    alert("Failed to fetch student details.");
                }
            });
        }

        function getBook() {
            $("#loaderIcon").show();
            $.ajax({
                url: "get_book.php",
                type: "POST",
                data: { bookid: $("#bookid").val() },
                success: function(data) {
                    $("#get_book_name").html(data);
                    $("#loaderIcon").hide();
                },
                error: function() {
                    alert("Failed to fetch book details.");
                }
            });
        }
    </script>

    <style>
        .others {
            color: red;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include('includes/header.php'); ?>

    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Issue a New Book</h4>
                </div>
            </div>

            <div class="row">
                <div class="col-md-10 col-sm-6 col-xs-12 col-md-offset-1">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Issue a New Book
                        </div>

                        <div class="panel-body">
                            <form method="post">
                                <div class="form-group">
                                    <label>Student ID <span style="color:red;">*</span></label>
                                    <input type="text" class="form-control" name="studentid" id="studentid" onBlur="getStudent()" required />
                                </div>

                                <div class="form-group">
                                    <span id="get_student_name" style="font-size:16px;"></span>
                                </div>

                                <div class="form-group">
                                    <label>Book ID <span style="color:red;">*</span></label>
                                    <input type="text" class="form-control" name="bookid" id="bookid" onBlur="getBook()" required />
                                </div>

                                <div class="form-group">
                                    <label>Book Title <span style="color:red;">*</span></label>
                                    <select class="form-control" name="bookdetails" id="get_book_name" readonly>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <button type="submit" name="issue" class="btn btn-info">Issue Book</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
