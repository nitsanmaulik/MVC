<?php
    $conn = new mysqli('db','db','db','db');

    if ($conn->connect_error) {
        die("connection failed: ".$conn->connect_error);
    }
?>
