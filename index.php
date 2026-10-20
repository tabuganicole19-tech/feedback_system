<?php
require_once 'db.php';

$success_msg = "";
$error_msg = "";

// Handle Contact Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Server-side validation
    if (empty($name) || empty($email) || empty($message)) {
        $error_msg = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Please enter a valid email address.";
    } else {
        // Use prepared statement to insert message safely
        $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);
        
        if ($stmt->execute()) {
            $success_msg = "Thank you! Your message has been sent successfully.";
        } else {
            $error_msg = "Failed to send message. Please try again later.";
        }
        $stmt->close();
    }
}

// Fetch Projects from Database
$projects_result = $conn->query("SELECT * FROM projects ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Portfolio</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="navbar">
        <div class="logo">Portfolio</div>
        <nav>
            <a href="#about">About</a>
            <a href="#projects">Projects</a>
            <a href="#contact">Contact</a>
            <a href="login.php" class="btn-login">Admin Login</a>
        </nav>
    </header>

    <section id="about" class="hero">
        <h1>Hello, I'm Nicole Tabuga</h1>
        <p>Welcome to my dynamic PHP & MySQL portfolio management system.</p>
    </section>

    <section id="projects" class="container">
        <h2>My Projects</h2>
        <div class="projects-grid">
            <?php if ($projects_result && $projects_result->num_rows > 0): ?>
                <?php while ($row = $projects_result->fetch_assoc()): ?>
                    <div class="project-card">
                        <h3><?= htmlspecialchars($row['title']) ?></h3>
                        <p><?= htmlspecialchars($row['description']) ?></p>
                        <?php if (!empty($row['technologies'])): ?>
                            <p class="tech"><strong>Tech:</strong> <?= htmlspecialchars($row['technologies']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($row['project_link'])): ?>
                            <a href="<?= htmlspecialchars($row['project_link']) ?>" target="_blank" class="btn">View Project</a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No projects added yet.</p>
            <?php endif; ?>
        </div>
    </section>

    <section id="contact" class="container">
        <h2>Contact Me</h2>
        
        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
        <?php endif; ?>

        <form action="index.php#contact" method="POST" class="form">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="5" required></textarea>
            </div>
            <button type="submit" name="send_message" class="btn">Send Message</button>
        </form>
    </section>

    <footer>
        <p>&copy; <?= date('Y') ?> Student Portfolio. All rights reserved.</p>
    </footer>

</body>
</html>
<?php
session_start();
require_once 'db.php';

// If already logged in, redirect to admin dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin/dashboard.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        // Compute SHA-256 hash of the entered password to match database setup
        $hashed_password = hash('sha256', $password);

        $stmt = $conn->prepare("SELECT id, username FROM admins WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $hashed_password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];

            header("Location: admin/dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="center-body">

    <div class="login-card">
        <h2>Admin Login</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <p style="margin-top: 15px; text-align: center;">
            <a href="index.php">&larr; Back to Public Site</a>
        </p>
    </div>

</body>
</html>