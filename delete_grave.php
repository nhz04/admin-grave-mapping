<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Check if the grave is assigned to a deceased individual
    $check = "SELECT * FROM deceased WHERE grave_id = '$id'";
    $result = $conn->query($check);

    if ($result->num_rows > 0) {
        echo "Error: Cannot delete. This grave is assigned to a deceased individual.";
    } else {
        $sql = "DELETE FROM graves WHERE grave_id = '$id'";
        if ($conn->query($sql) === TRUE) {
            echo "Grave deleted successfully.";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>
