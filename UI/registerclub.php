<?php
session_start();

if (!isset($_SESSION['user_id']) && $_SESSION['user_id'] == 10001) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Club</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=2"> 
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
    </style>
</head>

<body>
    <div class="sidebar">
        <img src="../assets/images/Universiti_Tenaga_Nasional_Logo.png" alt="Admin Logo"> 
        <h2>Admin Panel</h2>
        <a href="./dashboard.php">Dashboard</a>
        <a href="./manageclub.php">Manage Clubs</a>
        <a href="./registerclub.php" class="active">Register Club</a>
        <a href="./function/logout.php">Logout</a>
    </div>
    <div class="main-content">
        <h1>Register a New Club</h1>
        <form id="clubForm">
            <label for="clubName">Club Name:</label>
            <input type="text" id="clubName" name="clubName" required>

            <label for="clubDescription">Club Description:</label>
            <textarea id="clubDescription" name="clubDescription" rows="4" required></textarea>

            <label for="clubCategory">Club Category:</label>
            <select id="clubCategory" name="clubCategory" required>
                <option value="sports">Sports</option>
                <option value="academic">Academic</option>
                <option value="arts">Arts</option>
                <option value="others">Others</option>
            </select>

            <label for="clubLeader">Club Leader:</label>
            <input type="text" id="clubLeader" name="clubLeader" required>

            <label for="ContactNumber">Contact Number:</label>
            <input type="text" id="ContactNumber" name="ContactNumber" required>

            <button type="submit">Register Club</button>
        </form>

    </div>
    <script>
        document.getElementById("clubForm").addEventListener("submit", function (e) {
            e.preventDefault(); 

            const formData = new FormData(this);

            fetch("/functions/register_club.php", {
                method: "POST",
                body: formData
            })
                .then(response => response.text())
                .then(result => {
                    if (result.includes("successfully")) {
                        alert("Club registered successfully!");
                        document.getElementById("clubForm").reset();
                    } else {
                        alert("Error: " + result); 
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Submission failed. Please try again.");
                });
        });
    </script>
</body>

</html>