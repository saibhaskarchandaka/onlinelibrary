<?php
session_start();

require_once 'includes/config.php';
// Database Configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "library";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("❌ Database connection failed: " . htmlspecialchars($e->getMessage()));
}

// Initialize messages
$errors = [];
$success_message = null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
    
    // Validate Input Fields
    $title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $author = trim(filter_input(INPUT_POST, 'author', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $category = trim(filter_input(INPUT_POST, 'category', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

    if (!$title || !$author || !$category) {
        $errors[] = "⚠ All fields are required.";
    }

    // Handle Cover Image Upload
    $cover_image_name = "default-cover.png"; // Default image

    if (!empty($_FILES['cover_image']['name'])) {
        $cover_image_tmp_name = $_FILES['cover_image']['tmp_name'];
        $cover_image_ext = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png'];

        if (!in_array($cover_image_ext, $allowed_types)) {
            $errors[] = "⚠ Invalid cover image format. Only JPG, JPEG, and PNG are allowed.";
        }

        if ($_FILES['cover_image']['size'] > 2 * 1024 * 1024) {
            $errors[] = "⚠ Cover image size must be under 2MB.";
        }

        if (empty($errors)) {
            $cover_image_name = uniqid("cover_") . "." . $cover_image_ext;
            $cover_image_upload_path = "uploads/covers/" . $cover_image_name;

            if (!is_dir("uploads/covers")) {
                mkdir("uploads/covers", 0777, true);
            }

            if (!move_uploaded_file($cover_image_tmp_name, $cover_image_upload_path)) {
                $errors[] = "❌ Failed to upload cover image.";
            }
        }
    }

    // Handle E-Book File Upload
    $ebook_file_name = null;

    if (!empty($_FILES['ebook_file']['name'])) {
        $ebook_file_tmp_name = $_FILES['ebook_file']['tmp_name'];
        $ebook_file_ext = strtolower(pathinfo($_FILES['ebook_file']['name'], PATHINFO_EXTENSION));

        if ($ebook_file_ext !== 'pdf') {
            $errors[] = "⚠ Invalid file format. Only PDF is allowed.";
        }

        if ($_FILES['ebook_file']['size'] > 100 * 1024 * 1024) {
            $errors[] = "⚠ E-Book file must be under 100MB.";
        }

        if (empty($errors)) {
            $ebook_file_name = uniqid("ebook_") . ".pdf";
            $ebook_file_upload_path = "uploads/ebooks/" . $ebook_file_name;

            if (!is_dir("uploads/ebooks")) {
                mkdir("uploads/ebooks", 0777, true);
            }

            if (!move_uploaded_file($ebook_file_tmp_name, $ebook_file_upload_path)) {
                $errors[] = "❌ Failed to upload E-Book file.";
            }
        }
    } else {
        $errors[] = "⚠ Please upload an E-Book file.";
    }

    // Insert Data into Database
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO ebooks (title, author, category, cover_image, file_path) VALUES (:title, :author, :category, :cover_image, :file_path)");
            $stmt->execute([
                ':title' => $title,
                ':author' => $author,
                ':category' => $category,
                ':cover_image' => $cover_image_name,
                ':file_path' => $ebook_file_name
            ]);
            $success_message = "✅ E-Book uploaded successfully!";
        } catch (PDOException $e) {
            $errors[] = "❌ Database error: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload E-Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .container { max-width: 800px; margin: 50px auto; }
        .card { border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .card-header { background-color: #e3f2fd; font-weight: bold; }
        .form-control { margin-bottom: 15px; }
        .btn-upload { background-color: #007bff; border: none; padding: 10px; color: white; font-size: 16px; border-radius: 4px; width: 100%; }
        .btn-upload:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<div class="container">
    

    <div class="card">
        <div class="card-header">
            Upload a New E-Book
        </div>
        <div class="card-body">
            
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="upload-ebook.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Author <span class="text-danger">*</span></label>
                    <input type="text" name="author" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category <span class="text-danger">*</span></label>
                    <input type="text" name="category" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Cover Image (JPG, PNG)</label>
                    <input type="file" name="cover_image" class="form-control" accept="image/jpeg, image/png">
                </div>

                <div class="mb-3">
                    <label class="form-label">E-Book File (PDF only) <span class="text-danger">*</span></label>
                    <input type="file" name="ebook_file" class="form-control" accept="application/pdf" required>
                </div>

                <button type="submit" name="submit" class="btn btn-upload">Upload E-Book</button>
            </form>

        </div>
    </div>
</div>

</body>
</html>
