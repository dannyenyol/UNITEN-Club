<?php
session_start();

$allowed_user_id = 10001;

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== $allowed_user_id) {
    header("Location: ./function/logout.php");
    exit;
}
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
        </style>
</head>

<body>
    <div class="sidebar">
        <img src="../assets/images/Universiti_Tenaga_Nasional_Logo.png" alt="Admin Logo">
        <h2>Admin Panel</h2>
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="manageclub.php">Manage Clubs</a>
        <a href="registerclub.php">Register Club</a>
        <a href="./function/logout.php">Logout</a>
    </div>
    <div class="main-content">
        <h1>Welcome, Admin!</h1>
        <p>We’re glad to have you here. Use the navigation menu to manage clubs, register new clubs, or view the
            dashboard.</p>
    </div>

</body>

</html>