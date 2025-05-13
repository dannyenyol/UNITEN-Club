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
    $clubLeaderEmail = isset($_POST['clubLeaderEmail']) ? trim($_POST['clubLeaderEmail']) : '';
    $contactNumber = isset($_POST['ContactNumber']) ? trim($_POST['ContactNumber']) : '';

    // Basic validation
    if (empty($clubName) || empty($clubDescription) || empty($clubCategory) || empty($clubLeaderEmail) || empty($contactNumber)) {
        echo "All fields are required.";
        exit;
    }

    // Query to get the leader's user ID from the user table based on the email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $clubLeaderEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if a user was found with that email
    if ($result->num_rows == 0) {
        echo "No user found with the provided email.";
        $stmt->close();
        $conn->close();
        exit;
    }

    // Fetch the user ID
    $user = $result->fetch_assoc();
    $clubLeaderId = $user['id'];

    // Insert into clubs table using prepared statement
    $stmt = $conn->prepare("INSERT INTO clubs (name, description, category, leader_id, contact_number) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $clubName, $clubDescription, $clubCategory, $clubLeaderId, $contactNumber);

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
