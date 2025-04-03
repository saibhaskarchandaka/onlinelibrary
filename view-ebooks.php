<?php
session_start();

// Database Configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "library";

// Establish Database Connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("<div class='alert alert-danger text-center'>Database connection failed: " . htmlspecialchars($e->getMessage()) . "</div>");
}

// Fetch eBooks from Database
try {
    $stmt = $pdo->query("SELECT * FROM ebooks ORDER BY id DESC");
    $ebooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<div class='alert alert-danger text-center'>Database error: " . htmlspecialchars($e->getMessage()) . "</div>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Catalog - E-Books</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1100px;
        }
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.2s ease-in-out;
        }
        .card:hover {
            transform: scale(1.02);
        }
        .card-img-top {
            height: 180px;
            object-fit: cover;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .card-body {
            padding: 15px;
        }
        .card-title {
            font-size: 16px;
            font-weight: bold;
            color: #343a40;
            margin-bottom: 8px;
            height: 40px;
            overflow: hidden;
        }
        .btn-view {
            background-color: #007bff;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 14px;
            text-decoration: none;
            color: white;
            display: inline-block;
        }
        .btn-view:hover {
            background-color: #0056b3;
        }
        .search-bar {
            margin-bottom: 20px;
            max-width: 400px;
        }
    </style>
</head>
<body>

    <div class="container mt-5">
        <h2 class="text-center mb-4">📚 Library Catalog - E-Books</h2>

        <!-- Search Bar -->
        <div class="d-flex justify-content-center">
            <input type="text" id="searchInput" class="form-control search-bar" 
                   placeholder="Search by Title, Author, or Category..." 
                   onkeyup="filterBooks()">
        </div>

        <!-- Display E-Books -->
        <?php if (empty($ebooks)): ?>
            <div class="alert alert-warning text-center mt-4">
                No e-books available in the library.
            </div>
        <?php else: ?>
            <div class="row mt-4" id="booksContainer">
                <?php foreach ($ebooks as $ebook): ?>
                    <div class="col-md-3 mb-4 book-card">
                        <div class="card">
                            <img src="uploads/covers/<?= htmlspecialchars($ebook['cover_image']) ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($ebook['title']) ?>">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= htmlspecialchars($ebook['title']) ?></h5>
                                <p class="mb-1"><strong>Author:</strong> <?= htmlspecialchars($ebook['author']) ?></p>
                                <p class="mb-3"><strong>Category:</strong> <?= htmlspecialchars($ebook['category']) ?></p>
                                <a href="uploads/ebooks/<?= htmlspecialchars($ebook['file_path']) ?>" 
                                   class="btn-view" target="_blank">
                                    View E-Book
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function filterBooks() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            let books = document.getElementsByClassName('book-card');

            Array.from(books).forEach(book => {
                let title = book.getElementsByClassName('card-title')[0].innerText.toLowerCase();
                let author = book.getElementsByTagName('p')[0].innerText.toLowerCase();
                let category = book.getElementsByTagName('p')[1].innerText.toLowerCase();

                if (title.includes(input) || author.includes(input) || category.includes(input)) {
                    book.style.display = "block";
                } else {
                    book.style.display = "none";
                }
            });
        }
    </script>

</body>
</html>
