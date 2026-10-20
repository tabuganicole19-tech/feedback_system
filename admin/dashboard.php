<?php
session_start();
require_once "../db.php";

if (!isset($_SESSION['admin']) ||
    $_SESSION['admin_logged_in'] !==true ){
    header("Location: ../login.php");
    exit;
}
?>

$result = $conn->query("SELECT * FROM projects ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <h1>Admin Dashboard</h1>

    <p>Welcome, Admin!</p>

    <a href="add_project.php">Add New Project</a> |
    <a href="messages.php">View Messages</a> |
    <a href="../index.php">View Portfolio</a> |
    <a href="../logout.php">Logout</a>

    <hr>

    <h2>My Projects</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Technologies</th>
            <th>Actions</th>
        </tr>

        <?php while ($project = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $project['id'] ?></td>
            <td><?= htmlspecialchars($project['title']) ?></td>
            <td><?= htmlspecialchars($project['technologies'] ?? '') ?></td>
            <td>
                <a href="edit_project.php?id=<?= $project['id'] ?>">Edit</a>

                <form action="delete_project.php" method="POST"
                      style="display:inline"
                      onsubmit="return confirm('Delete this project?');">
                    <input type="hidden" name="id"
                           value="<?= $project['id'] ?>">
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>