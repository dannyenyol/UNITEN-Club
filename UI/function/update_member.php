<?php
session_start();
require_once './db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['member_id']) && isset($_POST['role'])) {
    $memberId = intval($_POST['member_id']);
    $newRole = $_POST['role'];

    // Validate the new role against a list of allowed roles if necessary
    $allowedRoles = ["President", "Vice President", "Secretary", "Treasurer", "Member"];
    if (!in_array($newRole, $allowedRoles)) {
        echo json_encode(['success' => false, 'message' => 'Invalid role.']);
        exit;
    }

    // Check if a role entry already exists for this user in this club
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM club_role WHERE user_id = ? AND club_id = ?");
    $checkStmt->bind_param("ii", $memberId, $_SESSION['club_id']); // Assuming club_id is stored in the session
    $checkStmt->execute();
    $countResult = $checkStmt->get_result()->fetch_row()[0];
    $checkStmt->close();

    if ($countResult > 0) {
        // Update the existing role
        $updateStmt = $conn->prepare("UPDATE club_role SET role = ? WHERE user_id = ? AND club_id = ?");
        $updateStmt->bind_param("sii", $newRole, $memberId, $_SESSION['club_id']); // Assuming club_id is stored in the session
        $success = $updateStmt->execute();
        $updateStmt->close();
    } else {
        // Insert a new role entry
        $insertStmt = $conn->prepare("INSERT INTO club_role (club_id, user_id, role) VALUES (?, ?, ?)");
        $insertStmt->bind_param("iis", $_SESSION['club_id'], $memberId, $newRole); // Assuming club_id is stored in the session
        $success = $insertStmt->execute();
        $insertStmt->close();
    }

    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

$conn->close();
?>