<?php
require_once './db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicationId = $_POST['application_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if (!$applicationId || !in_array($action, ['accept', 'reject'])) {
        http_response_code(400);
        echo "Invalid request";
        exit;
    }

    $newStatus = $action === 'accept' ? 'accepted' : 'rejected';

    // Update the application status
    $stmt = $conn->prepare("UPDATE club_registrations SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $applicationId);

    if ($stmt->execute()) {
        // If accepted, update the user's club_id
        if ($action === 'accept') {
            // Get user_id and club_id from the application
            $stmt->close();
            $stmt = $conn->prepare("SELECT user_id, club_id FROM club_registrations WHERE id = ?");
            $stmt->bind_param("i", $applicationId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $userId = $row['user_id'];
                $clubId = $row['club_id'];
                $stmt->close();

                // Update user's club_id
                $stmt = $conn->prepare("UPDATE user SET club_id = ? WHERE id = ?");
                $stmt->bind_param("ii", $clubId, $userId);

                if ($stmt->execute()) {
                    echo "Application accepted and user updated successfully.";
                } else {
                    http_response_code(500);
                    echo "Application accepted but failed to update user.";
                }
            } else {
                echo "Application accepted, but user/club information could not be retrieved.";
            }
        } else {
            echo "Application rejected successfully.";
        }
    } else {
        http_response_code(500);
        echo "Failed to update application.";
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo "Method Not Allowed";
}
?>