<?php
$servername = "localhost";
$username = "root"; // আপনার cPanel ইউজারনেম
$password = ""; // আপনার cPanel পাসওয়ার্ড
$dbname = "natural_basket"; // আপনার ডাটাবেস নাম

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
$conn->set_charset("utf8mb4");
?>