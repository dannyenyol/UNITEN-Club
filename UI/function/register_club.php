<?php
// Enable error reporting (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include the database connection
require_once 'db.php';

// Check if the form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $clubName = isset($_POST['clubName']) ? trim($_POST['clubName']) : '';
    $clubDescription = isset($_POST['clubDescription']) ? trim($_POST['clubDescription']) : '';
    $clubCategory = isset($_POST['clubCategory']) ? trim($_POST['clubCategory']) : '';
    $clubLeader = isset($_POST['clubLeader']) ? trim($_POST['clubLeader']) : '';
    $contactNumber = isset($_POST['ContactNumber']) ? trim($_POST['ContactNumber']) : '';

    // Basic validation
    if (empty($clubName) || empty($clubDescription) || empty($clubCategory) || empty($clubLeader) || empty($contactNumber)) {
        echo "All fields are required.";
        exit;
    }

    // Insert into database using prepared statement
    $stmt = $conn->prepare(query: "INSERT INTO clubs (name, description, category, leader, contact_number) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $clubName, $clubDescription, $clubCategory, $clubLeader, $contactNumber);

    if ($stmt->execute()) {
        echo "Club registered successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>