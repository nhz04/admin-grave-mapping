<?php   //DELETE DECEASED RECORD
include 'db.php';

if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']); // Prevent SQL injection

    // Fetch the grave_id before deleting the deceased record
    $get_grave = "SELECT grave_id FROM deceased WHERE deceased_id = '$id'";
    $result = $conn->query($get_grave);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $grave_id = $row['grave_id'];

        // Delete from deceased first
        $sql_deceased = "DELETE FROM deceased WHERE deceased_id = '$id'";
        if ($conn->query($sql_deceased) === TRUE) {
            // Update the graves table to set status to "available"
            $sql_update_grave = "UPDATE graves SET status = 'available' WHERE grave_id = '$grave_id'";
            $conn->query($sql_update_grave);
            echo "Record deleted successfully.";
        } else {
            echo "Error deleting deceased record: " . $conn->error;
        }
    } else {
        echo "Error: Deceased record not found.";
    }
}
?>
