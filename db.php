<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "portfolio_system";

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed.");
    }
} catch (Exception $e) {
    // Show a user-friendly error without exposing raw SQL details
    die("<div style='text-align:center; padding:20px; font-family:sans-serif;'>
            <h2>Service Temporarily Unavailable</h2>
            <p>We are currently experiencing technical issues. Please try again later.</p>
         </div>");
}
?>