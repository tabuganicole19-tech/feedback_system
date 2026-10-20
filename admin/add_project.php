<?php
session_start();
require_once "../db.php";

if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $technologies = trim($_POST['technologies'] ?? '');
    $project_link = trim($_POST['project_link'] ?? '');

    if ($title === '' || $description === '') {
        $error = "Title and description are required.";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO projects
            (title, description, technologies, project_link)
            VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssss",
            $title,
            $description,
            $technologies,
            $project_link
        );

        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Unable to save the project.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Project</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <h1>Add New Project</h1>

    <p><?= htmlspecialchars($error) ?></p>

    <form method="POST">
        <label>Project Title:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" required></textarea><br><br>

        <label>Technologies Used:</label><br>
        <input type="text" name="technologies"><br><br>

        <label>Project Link:</label><br>
        <input type="url" name="project_link"><br><br>

        <button type="submit">Save Project</button>
    </form>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>