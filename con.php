<?php
// con.php
$conn = mysqli_connect(
    "localhost",
    "samin",          
    "samin1234",      
    "cloud_notes"     
);

if(!$conn){
    die("Connection Failed: " . mysqli_connect_error()); 
}
?>
