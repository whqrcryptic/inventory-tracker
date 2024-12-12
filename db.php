<?php
// db.php
$servername = "mysql"; // Change if your MySQL server is different
$username = "toolmaster";        // MySQL username
$password = "dLTvu]eJzwAd";            // MySQL password
$dbname = "toolroom";      // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";  //Will print if connection is successful
?>