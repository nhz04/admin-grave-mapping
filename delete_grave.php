<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    
    $check = "SELECT * FROM deceased WHERE grave_id = '$id'";
    $result = $conn->query($check);

    if ($result->num_rows > 0) {
        header("Location: index.php?status=deleteGraveFailed"); 
        exit();
    } else {
        $sql = "DELETE FROM graves WHERE grave_id = '$id'";
        if ($conn->query($sql) === TRUE) {
            header("Location: index.php?status=deleteGraveSuccess"); 
            exit();
           
         
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>
