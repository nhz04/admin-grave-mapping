<?php   
include 'db.php';

if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']); 

   
    $get_grave = "SELECT grave_id FROM deceased WHERE deceased_id = '$id'";
    $result = $conn->query($get_grave);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $grave_id = $row['grave_id'];

        
        $sql_deceased = "DELETE FROM deceased WHERE deceased_id = '$id'";
        if ($conn->query($sql_deceased) === TRUE) {
           
            $sql_update_grave = "UPDATE graves SET status = 'available' WHERE grave_id = '$grave_id'";
            $conn->query($sql_update_grave);
            header("Location: index.php?status=deleteDeceasedSuccess"); 
            exit();
          
        } else {
            echo "Error deleting deceased record: " . $conn->error;
        }
    } else {
        echo "Error: Deceased record not found.";
    }
}
?>
