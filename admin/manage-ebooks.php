<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['admin_login'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $sql = "DELETE FROM ebooks WHERE id=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id);
    $query->execute();
    $_SESSION['msg'] = "E-Book deleted successfully!";
    header("Location: manage_ebooks.php");
}

include('includes/header.php');
?>

<div class="container mt-5">
    <h2 class="text-center">Manage E-Books</h2>

    <?php if(isset($_SESSION['msg'])) { ?>
        <div class="alert alert-success"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
    <?php } ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Cover</th>
                <th>Download</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM ebooks ORDER BY id DESC";
            $query = $dbh->prepare($sql);
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_OBJ);
            $cnt = 1;
            if ($query->rowCount() > 0) {
                foreach ($results as $result) { ?>
                    <tr>
                        <td><?= htmlentities($cnt); ?></td>
                        <td><?= htmlentities($result->title); ?></td>
                        <td><?= htmlentities($result->author); ?></td>
                        <td><?= htmlentities($result->category); ?></td>
                        <td><img src="uploads/covers/<?= htmlentities($result->cover_image); ?>" width="50"></td>
                        <td><a href="uploads/ebooks/<?= htmlentities($result->file_path); ?>" class="btn btn-info btn-sm" download>Download</a></td>
                        <td><a href="manage_ebooks.php?del=<?= htmlentities($result->id); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a></td>
                    </tr>
                    <?php $cnt++; }
            } ?>
        </tbody>
    </table>
</div>

<?php include('includes/footer.php'); ?>
