<?php 
// EDIT DECEASED RECORD
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM deceased WHERE deceased_id = '$id'";
    $result = $conn->query($sql);
    $person = $result->fetch_assoc();
}

if (isset($_POST['update_deceased'])) {
    // Fetch the values from the form
    $id = $_POST['deceased_id'];  // Make sure this is the correct name attribute in the form
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $birth_date = $_POST['birth_date'];
    $death_date = $_POST['death_date'];
    $obituary = $_POST['obituary'];

    // Sanitize inputs to prevent SQL injection
    $first_name = mysqli_real_escape_string($conn, $first_name);
    $last_name = mysqli_real_escape_string($conn, $last_name);
    $birth_date = mysqli_real_escape_string($conn, $birth_date);
    $death_date = mysqli_real_escape_string($conn, $death_date);
    $obituary = mysqli_real_escape_string($conn, $obituary);

    // SQL query to update record
    $sql = "UPDATE deceased SET 
                first_name = '$first_name', 
                last_name = '$last_name', 
                birth_date = '$birth_date', 
                death_date = '$death_date', 
                obituary = '$obituary' 
            WHERE deceased_id = '$id'";

    if ($conn->query($sql) === TRUE) {
        // Redirect after update (to avoid re-submit on refresh)
        header("Location: index.php");  // Change list.php to your page that shows the list of deceased
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
