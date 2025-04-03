<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin']) == 0) {   
    header('location:index.php');
} else { 

    if(isset($_POST['add'])) {
        $bookname = trim($_POST['bookname']);
        $category = trim($_POST['category']);
        $author = trim($_POST['author']);
        $isbn = trim($_POST['isbn']);
        $price = trim($_POST['price']);
        $copies = trim($_POST['copies']);

        $sql = "INSERT INTO tblbooks (BookName, CatId, AuthorId, ISBNNumber, BookPrice, Copies) 
                VALUES (:bookname, :category, :author, :isbn, :price, :copies)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':bookname', $bookname, PDO::PARAM_STR);
        $query->bindParam(':category', $category, PDO::PARAM_INT);
        $query->bindParam(':author', $author, PDO::PARAM_INT);
        $query->bindParam(':isbn', $isbn, PDO::PARAM_STR);
        $query->bindParam(':price', $price, PDO::PARAM_STR);
        $query->bindParam(':copies', $copies, PDO::PARAM_INT);
        $query->execute();

        if($dbh->lastInsertId()) {
            $_SESSION['msg'] = "Book added successfully!";
            header('location:manage-books.php');
        } else {
            $_SESSION['error'] = "Something went wrong. Please try again!";
            header('location:manage-books.php');
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Book | Library Management System</title>
    
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="assets/css/font-awesome.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<?php include('includes/header.php'); ?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h4 class="header-line">Add Book</h4>
        </div>
    </div>

    <!-- Display Messages -->
    <?php if(isset($_SESSION['msg'])) { ?>
        <div class="alert alert-success"><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
    <?php } elseif(isset($_SESSION['error'])) { ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php } ?>

    <div class="row">
        <div class="col-md-6 col-sm-8 col-xs-12 col-md-offset-3">
            <div class="panel panel-info">
                <div class="panel-heading">Book Information</div>
                <div class="panel-body">
                    <form method="post">

                        <div class="form-group">
                            <label>Book Name <span style="color:red;">*</span></label>
                            <input type="text" class="form-control" name="bookname" required>
                        </div>

                        <div class="form-group">
                            <label>Category <span style="color:red;">*</span></label>
                            <select class="form-control" name="category" required>
                                <option value="">Select Category</option>
                                <?php 
                                $status = 1;
                                $sql = "SELECT * FROM tblcategory WHERE Status=:status";
                                $query = $dbh->prepare($sql);
                                $query->bindParam(':status', $status, PDO::PARAM_INT);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                foreach($results as $result) { ?>
                                    <option value="<?php echo htmlspecialchars($result->id); ?>">
                                        <?php echo htmlspecialchars($result->CategoryName); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Publication <span style="color:red;">*</span></label>
                            <select class="form-control" name="author" required>
                                <option value="">Select Publication</option>
                                <?php 
                                $sql = "SELECT * FROM tblauthors";
                                $query = $dbh->prepare($sql);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                foreach($results as $result) { ?>
                                    <option value="<?php echo htmlspecialchars($result->id); ?>">
                                        <?php echo htmlspecialchars($result->AuthorName); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>ISBN Number <span style="color:red;">*</span></label>
                            <input type="text" class="form-control" name="isbn" required>
                            <small class="help-block">ISBN must be unique.</small>
                        </div>

                        <div class="form-group">
                            <label>Number of Copies <span style="color:red;">*</span></label>
                            <input type="number" class="form-control" name="copies" required>
                        </div>

                        <div class="form-group">
                            <label>Price <span style="color:red;">*</span></label>
                            <input type="text" class="form-control" name="price" required>
                        </div>

                        <button type="submit" name="add" class="btn btn-info">Add Book</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="assets/js/jquery-1.10.2.js"></script>
<!-- Bootstrap Scripts -->
<script src="assets/js/bootstrap.js"></script>
<!-- Custom Scripts -->
<script src="assets/js/custom.js"></script>

</body>
</html>
