<?php
include 'db.php';

if (isset($_POST['add_deceased'])) {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $birth_date = $conn->real_escape_string($_POST['birth_date']);
    $death_date = $conn->real_escape_string($_POST['death_date']);
    $obituary = $conn->real_escape_string($_POST['obituary']);
    $section = $conn->real_escape_string($_POST['section']);
    $block_number = $conn->real_escape_string($_POST['block_number']);
    $lot_number = $conn->real_escape_string($_POST['lot_number']);

    // Fetch the correct grave_id and check if it is already taken
    $grave_query = "SELECT grave_id, status FROM graves WHERE section = '$section' AND block_number = '$block_number' AND lot_number = '$lot_number'";
    $result = $conn->query($grave_query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $grave_id = $row['grave_id'];
        $grave_status = $row['status'];

        // Check if the grave is already taken
        if ($grave_status === 'Taken') {
            header("Location: index.php?status=addDeceasedFailed"); // Failure message
            exit();
        }

        // Insert into deceased table
        $sql = "INSERT INTO deceased (first_name, last_name, birth_date, death_date, obituary, grave_id) 
                VALUES ('$first_name', '$last_name', '$birth_date', '$death_date', '$obituary', '$grave_id')";

        if ($conn->query($sql) === TRUE) {
            // Update grave status to 'Taken'
            $update_grave = "UPDATE graves SET status = 'Taken' WHERE grave_id = '$grave_id'";
            $conn->query($update_grave);

            header("Location: index.php?status=addDeceasedSuccess");
            exit();
        } else {
            header("Location: index.php?status=graveNotExist"); // Insertion failed
            exit();
        }
    } else {
        header("Location: index.php?status=graveNotExist"); // Failure message
            exit();
    }
}
?>
