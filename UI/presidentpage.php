<?php
session_start();

if (!isset($_SESSION['user_id']) || trim($_SESSION['role']) !== 'President') {
    header("Location: login.php");
    exit;
}

require_once './function/db.php';

$userId = $_SESSION['user_id'];

$clubQuery = "SELECT id, name FROM clubs WHERE leader = ?";
$clubStmt = $conn->prepare($clubQuery);
$clubStmt->bind_param("i", $userId);
$clubStmt->execute();
$clubResult = $clubStmt->get_result()->fetch_assoc();
$clubStmt->close();

$clubId = $clubResult['id'] ?? null;
$clubName = $clubResult['name'] ?? null;

$members = [];
if ($clubId) {

    $memberQuery = "SELECT u.id, u.username, u.email, cr.role AS position
                          FROM user u
                          LEFT JOIN club_role cr ON u.id = cr.user_id AND cr.club_id = ?
                          WHERE u.club_id = ?";
    $stmt = $conn->prepare($memberQuery);
    $stmt->bind_param("ii", $clubId, $clubId);
    $stmt->execute();
    $members = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $presidentIndex = -1;
    foreach ($members as $key => $member) {
        if ($member['id'] == $userId) {
            $presidentIndex = $key;
            break;
        }
    }

    if ($presidentIndex > 0) {
        $president = $members[$presidentIndex];
        unset($members[$presidentIndex]);
        array_unshift($members, $president);
    } elseif ($presidentIndex === 0) {

    } else {     
        error_log("President user ID not found in club members.");
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Page</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background-color: #237ad2;
            color: white;
            height: 100vh;
            padding: 20px;
            box-sizing: border-box;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }

        .sidebar a:hover {
            background-color: #34495e;
        }

        .sidebar img {
            width: 150px;
            height: auto;
            margin-bottom: 10px;
        }

        .main-content {
            flex-grow: 1;
            padding: 20px;
        }

        .main-content h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #237ad2;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }

        th {
            background-color: #237ad2;
            color: white;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <img src="../assets/images/Universiti_Tenaga_Nasional_Logo.png" alt="Admin Logo">
        <h2>President Panel</h2>
        <p>Welcome, <span id="username">President</span>!</p>
        <a href="presidentpage.php" class="active">My Club</a>
        <a href="pendingrequest.php" class="active">Pending Request</a>
        <a href="#" onclick="logout()">Logout</a>
    </div>

    <div class="main-content">
        <h1><?= htmlspecialchars($clubName ?? 'Club Dashboard') ?></h1>
        <h2>Club Members</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Email</th>
                    <th>Actions</th>
                    <th>Set Position</th>
                </tr>
            </thead>
            <tbody id="membersTable">
            </tbody>
        </table>
    </div>

    <script>
        const members = <?= json_encode($members); ?>;
        const positions = ["President", "Vice President", "Secretary", "Treasurer", "Member"];

        function renderTable() {
            const tableBody = document.getElementById("membersTable");
            tableBody.innerHTML = "";

            members.forEach((member, index) => {
                console.log(member.position);
                let trimmedPosition = member.position ? member.position.trim() : "Member"; // Trim whitespace or default to "Member"
                const isPresident = trimmedPosition === "President";

                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${member.username}</td>
                        <td>${trimmedPosition}</td>
                        <td>${member.email}</td>
                        <td>
                            ${!isPresident ? `
                                <button onclick="deleteMember(${member.id})" style="padding: 5px; background-color: #e74c3c; color: white; border: none; border-radius: 5px;">Delete</button>
                            ` : ''}
                        </td>
                        <td>
                            <select onchange="updatePosition(${member.id}, this.value)" style="padding: 5px; border-radius: 5px;" ${isPresident ? "disabled" : ""}>
                                ${positions.filter(position => isPresident || position !== "President").map(position =>
                    `<option value="${position}" ${trimmedPosition === position ? "selected" : ""}>${position}</option>`
                ).join("")}
                            </select>
                        </td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        }


        function updatePosition(memberId, newPosition) {
            const member = members.find(m => m.id === memberId);
            if (member) {
                member.position = newPosition;
                renderTable(); 

                const xhr = new XMLHttpRequest();
                xhr.open("POST", "./function/update_member.php"); 
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            console.log("Position updated successfully in the database.");
                        } else {
                            console.error("Failed to update position in the database:", response.message);
                        }
                    } else {
                        console.error("Error: " + xhr.status + " " + xhr.statusText);
                    }
                };
                xhr.onerror = function () {
                    console.error("Network error occurred while updating position.");
                };
                const params = "member_id=" + encodeURIComponent(memberId) + "&role=" + encodeURIComponent(newPosition);
                xhr.send(params);
            }
        }

        function deleteMember(memberId) {
            if (confirm("Are you sure you want to delete this member?")) {
                const index = members.findIndex(m => m.id === memberId);
                if (index !== -1) {
                    
                    const xhr = new XMLHttpRequest();
                    xhr.open("POST", "delete_member.php"); 
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                members.splice(index, 1);
                                renderTable();
                                alert("Member deleted successfully!");
                            } else {
                                alert("Failed to delete member: " + response.message);
                            }
                        } else {
                            alert("Error: " + xhr.status + " " + xhr.statusText);
                        }
                    };
                    xhr.onerror = function () {
                        alert("Network error occurred.");
                    };
                    const params = "member_id=" + encodeURIComponent(memberId);
                    xhr.send(params);
                }
            }
        }

        function logout() {
            window.location.href = "login.php";
        }

        renderTable();
    </script>
</body>

</html>