<?php

include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM graves WHERE grave_id = '$id'";
    $result = $conn->query($sql);
    $grave = $result->fetch_assoc();
}

if (isset($_POST['update_grave'])) {

    $id = $_POST['grave_id']; 
    $section = $_POST['section'];
    $block_number = $_POST['block_number'];
    $lot_number = $_POST['lot_number'];
    $status = $_POST['status'];

   
    $section = mysqli_real_escape_string($conn, $section);
    $block_number = mysqli_real_escape_string($conn, $block_number);
    $lot_number = mysqli_real_escape_string($conn, $lot_number);
    $status = mysqli_real_escape_string($conn, $status);

  
    $sql = "UPDATE graves SET 
                section = '$section', 
                block_number = '$block_number', 
                lot_number = '$lot_number', 
                status = '$status' 
            WHERE grave_id = '$id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?status=editGraveSuccess"); 
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
