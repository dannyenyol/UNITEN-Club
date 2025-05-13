<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $clubName = isset($_POST['clubName']) ? trim($_POST['clubName']) : '';
    $clubDescription = isset($_POST['clubDescription']) ? trim($_POST['clubDescription']) : '';
    $clubCategory = isset($_POST['clubCategory']) ? trim($_POST['clubCategory']) : '';
    $clubLeader = isset($_POST['clubLeader']) ? trim($_POST['clubLeader']) : '';
    $contactNumber = isset($_POST['ContactNumber']) ? trim($_POST['ContactNumber']) : '';

    if (empty($clubName) || empty($clubDescription) || empty($clubCategory) || empty($clubLeader) || empty($contactNumber)) {
        echo "All fields are required.";
        exit;
    }

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