<?php
require_once './function/db.php'; // Corrected path

$sql = "SELECT 
            c.id AS club_id,
            c.name AS club_name,
            c.leader AS club_leader,
            c.contact_number,
            c.category AS club_type,
            COUNT(u.id) AS total_members,
            c.status AS club_status
        FROM clubs c
        LEFT JOIN USER u ON c.id = u.club_id
        GROUP BY c.id";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td><input type='checkbox' value='" . $row['club_id'] . "'></td>";
        echo "<td>" . htmlspecialchars($row['club_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['club_leader']) . "</td>";
        echo "<td>" . htmlspecialchars($row['contact_number']) . "</td>";
        echo "<td>" . htmlspecialchars($row['club_type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['total_members']) . "</td>";
        if ($row['club_status'] == 1) {
            echo "<td>Active</td>";
        } else {
            echo "<td>Not Active</td>";
        }
        echo "<td>
                <button class='status-btn' data-club-id='" . $row['club_id'] . "' onclick='toggleStatus(this)'>Set " . ($row['club_status'] === 'Active' ? 'Not Active' : 'Active') . "</button>
                <button class='edit-btn' onclick='editClub(" . $row['club_id'] . ")'>Edit</button>
                <button class='delete-btn' onclick='deleteClub(" . $row['club_id'] . ")'>Delete</button>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>No clubs found or query failed.</td></tr>";
    if (!$result) {
        echo "<!-- SQL Error: " . $conn->error . " -->";
    }
}

$conn->close();
?>