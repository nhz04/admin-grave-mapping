<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    
    $check = "SELECT * FROM deceased WHERE grave_id = '$id'";
    $result = $conn->query($check);

    if ($result->num_rows > 0) {
        echo "Error: Cannot delete. This grave is assigned to a deceased individual.";
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
