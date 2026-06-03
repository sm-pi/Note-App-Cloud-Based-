<?php

$conn = mysqli_connect(
    "localhost",
    "samin",          // The user we just created
    "samin1234",      // The password you set
    "cloud_notes"     // The database holding your tables
);

if(!$conn){
    // Adding mysqli_connect_error() helps debug if the password or DB name is ever wrong
    die("Connection Failed: " . mysqli_connect_error()); 
}

?>
