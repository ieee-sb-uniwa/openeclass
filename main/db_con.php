<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Timetable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        th, td {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: center;
            border-radius: 8px; 
        }
        th:hover, td:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>

<?php
$servername = "localhost";
$username = "root"; 
$database = "ieee_test";

$conn = new mysqli($servername, $username, null, $database);

if ($conn->connect_error) {
    echo "<h1>Connection failed</h1>";
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "<h1>Connection was established</h1>";
    $sql = "SELECT * FROM courses_timetable";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          echo "<h2>" ."id: " . $row["course_id"]. " - Start time: " . $row["start_hour"]. " " . $row["end_hour"]." " .$row["day_of_week"]. "</h2>". "<br>";
        }
      } else {
        echo "0 results";
      }
    
}

$conn->close();
?>
