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
                color: #03fcdb;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1 class="text-center mt-4 mb-4">Weekly Timetable</h1>
            <div class="row">
                <div class="col">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Time</th>
                                <th scope="col">Monday</th>
                                <th scope="col">Tuesday</th>
                                <th scope="col">Wednesday</th>
                                <th scope="col">Thursday</th>
                                <th scope="col">Friday</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $servername = "localhost";
                            $username = "root"; 
                            $database = "test";

                            $conn = new mysqli($servername, $username, null, $database);

                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            } else {
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
                                        if($count>0){
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
                                    ksort($classMap);//order by classHour
                                    foreach($classMap as $hour => $classesAtHour){
                                        $ar=["","","","",""];
                                        echo "<tr>";
                                        echo "<th scope \"row\">".$hour."</th>";
                                        foreach($classesAtHour as $class)
                                        {
                                            $ar[$class['day_of_week']-1] .= $class['class_name']." ";
                                        }
                                        for($i=0;$i<=4;$i++){
                                            echo '<td data-toggle="tooltip" title="' . $class['teacher'] . '">' . $ar[$i] . '</td>';
                                        }
                                        echo "</tr>";
                                    }
                                    
                                } else {
                                    echo "0 results";
                                }
                                
                            }

                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>