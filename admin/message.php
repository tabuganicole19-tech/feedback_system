<?php
session_start();
require_once "../db.php";

if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

if (!$id || $id < 1) {
    exit("Invalid project ID.");
}

$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$project) {
    exit("Project not found.");
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
            "UPDATE projects
             SET title = ?, description = ?,
                 technologies = ?, project_link = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "ssssi",
            $title,
            $description,
            $technologies,
            $project_link,
            $id
        );

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Unable to update the project.";
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Project</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <h1>Edit Project</h1>

    <p><?= htmlspecialchars($error) ?></p>

    <form method="POST">
        <input type="hidden" name="id" value="<?= $id ?>">

        <label>Project Title:</label><br>
        <input type="text" name="title"
               value="<?= htmlspecialchars($project['title']) ?>"
               required><br><br>

        <label>Description:</label><br>
        <textarea name="description" required><?= htmlspecialchars($project['description']) ?></textarea><br><br>

        <label>Technologies:</label><br>
        <input type="text" name="technologies"
               value="<?= htmlspecialchars($project['technologies'] ?? '') ?>"><br><br>

        <label>Project Link:</label><br>
        <input type="url" name="project_link"
               value="<?= htmlspecialchars($project['project_link'] ?? '') ?>"><br><br>

        <button type="submit">Update Project</button>
    </form>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>