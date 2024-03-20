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
$database = "test";

$conn = new mysqli($servername, $username, null, $database);

if ($conn->connect_error) {
    echo "<h1>Connection failed</h1>";
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "<h1>Connection was established</h1>";
    $sql = "SELECT * FROM courses_timetable";
    $result = $conn->query($sql);
    if ($result) {
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $formated=array();
        foreach($data as $dt){
            $count = intval((int)$dt['end_hour'] - (int)$dt['start_hour']);
            if($count>1){
                for($i=0;$i<$count;$i++){
                    $formatted_row = $dt;
                    unset($formatted_row['end_hour']);
                    if($i != 0){
                        $time_object = DateTime::createFromFormat('H:i:s', $formatted_row['start_hour']);
                        $time_object->add(new DateInterval('PT' . ($i) . 'H'));
                        $formatted_row['start_hour'] = $time_object->format('H:i:s');
                           
                    }
                    unset($formatted_row['end_hour']);
                    array_push($formated, $formatted_row);
                }
            }
        }
        $classMap = [];
        foreach ($formated as $ft) { //Loop through the classes
            $startHour = $ft['start_hour']; // Take the selected classes start hour
            if (!isset($classMap[$startHour])) {//if the starting hour has been found again ignore, else add it.
                $classMap[$startHour] = [];
            }
            $classMap[$startHour][] = $ft;//Add the class under that key
        }   
        //classmap has access to the day that each class is so that makes for easier distribution on the timetable. Use the 
        //following on the loop to access the day: $class['day_of_week']
        foreach ($classMap as $hour => $classesAtHour) {
            echo "<h1>Classes starting at hour $hour:\n</h1>";
            foreach ($classesAtHour as $class) {
                echo "<h1>- {$class['class_name']} - {$class['day_of_week']}\n</h1>";
                //echo "<h1>- {$class['day_of_week']}\n</h1>";
            }
            echo "\n";
        }
    } else {
        echo "0 results";
    }
    
}

$conn->close();
?>
