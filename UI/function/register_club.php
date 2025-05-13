<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $clubName = isset($_POST['clubName']) ? trim($_POST['clubName']) : '';
    $clubDescription = isset($_POST['clubDescription']) ? trim($_POST['clubDescription']) : '';
    $clubCategory = isset($_POST['clubCategory']) ? trim($_POST['clubCategory']) : '';
    $clubLeaderEmail = isset($_POST['clubLeaderEmail']) ? trim($_POST['clubLeaderEmail']) : '';
    $contactNumber = isset($_POST['ContactNumber']) ? trim($_POST['ContactNumber']) : '';

    if (empty($clubName) || empty($clubDescription) || empty($clubCategory) || empty($clubLeaderEmail) || empty($contactNumber)) {
        echo "All fields are required.";
        exit;
    }

    $stmt = $conn->prepare("SELECT id FROM user WHERE email = ?");
    $stmt->bind_param("s", $clubLeaderEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        echo "No user found with the provided email.";
        $stmt->close();
        $conn->close();
        exit;
    }

    $user = $result->fetch_assoc();
    $clubLeaderId = $user['id'];
$stmt = $conn->prepare("INSERT INTO clubs (name, description, category, leader, contact_number) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $clubName, $clubDescription, $clubCategory, $clubLeaderId, $contactNumber);

if ($stmt->execute()) {
    // Get the last inserted club ID
    $clubId = $stmt->insert_id;
    $stmt->close();

    // Insert into club_role table
    $role = 'President';
    $stmt = $conn->prepare("INSERT INTO club_role (user_id, club_id, role) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $clubLeaderId, $clubId, $role);

    if ($stmt->execute()) {
        echo "Club registered successfully and role assigned!";
    } else {
        echo "Club registered, but failed to assign role: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Error: " . $stmt->error;
}}
?>
