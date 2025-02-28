<?php //MAIN 

include 'db.php';
include 'functions.php';

$graves = fetchGraves($conn);
$deceasedList = fetchDeceased($conn);
$total_graves = getTotalGraves($conn);
$total_deceased = getTotalDeceased($conn);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($conn)) {
        die("Database connection error");
    }

    $status = "error"; // Default status

    if (isset($_POST['add_deceased'])) {
        $status = addDeceased($conn, $_POST, $_FILES);
    } elseif (isset($_POST['add_grave'])) {
        $status = addGrave(
            $conn, 
            $_POST['section'] ?? '', 
            $_POST['block_number'] ?? '', 
            $_POST['lot_number'] ?? ''
        );
    } elseif (isset($_POST['update_grave'])) {
        editGrave($conn);
        $status = "editGraveSuccess";
    } 

    $status = urlencode($status); 
    header("Location: index.php?status=$status");
    exit();
}










?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memorium - Admin</title>
    
 
    <link rel="stylesheet" href="style3.css">

    <script src="sidebar-nav.js" defer></script>
    <script src="cemetery.js"></script>
    <script src="viewDeceased.js"></script>
    <script src="updateFileName.js"></script>
    

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-details">
            <div class="logo_name">Memorium</div>
            <i class='bx bx-menu' id="btn"></i>
        </div>
        <ul class="nav-list">

            <li>
                <a href="#" onclick="showSection('dashboard')">
                    <i class='bx bxs-dashboard'></i>
                    <span class="links_name">Dashboard</span>
                </a>
                <span class="tooltip">Dashboard</span>
            </li>
            <li>
                <a href="#" onclick="showSection('grave')">
                <i class='bx bxs-folder-plus'></i>
                    <span class="links_name">Grave</span>
                </a>
                <span class="tooltip">Grave</span>
            </li>
            <li>
                <a href="#" onclick="showSection('deceased')">
                <i class='bx bxs-folder-open'></i>
                    <span class="links_name">Deceased</span>
                </a>
                <span class="tooltip">Deceased</span>
            </li>
            <li>
                <a href="">
                    <i class='bx bx-user'></i>
                    <span class="links_name">Profile</span>
                </a>
                <span class="tooltip">Profile</span>
            </li>

            <!-- Profile -->
            <li class="profile">
                <div class="profile-details">
                    <img src="profile.png" alt="profileImg">
                    <div class="name_job">
                        <div class="name">Memorium</div>
                        <div class="job">Cemetery Find</div>
                    </div>
                </div>
                <i class='bx bx-log-out' id="log_out"></i>
            </li>
        </ul>
    </div>
    <!-- Dashboard -->
    <section id="dashboard" class="home-section">
    <div class="header">
        <img src="profile.png" alt="Logo" class="logo">
        <div class="welcome">Welcome to Admin Dashboard</div>
    </div>
    <!-- Stats -->
    <div class="stats-container">
    <div class="stat-item">
        <div class="stat-box">
            <h3>Total Graves</h3>
            <p><?php echo $total_graves; ?></p>
        </div>
        <button id="openAddGrave" data-modal="addGraveModal" class="btn">Add Grave</button>
    </div>
    <div class="stat-item">
        <div class="stat-box">
            <h3>Total Deceased</h3>
            <p><?php echo $total_deceased; ?></p>
        </div>
        <button id="openAddDeceased" data-modal="addDeceasedModal" class="btn">Add Deceased</button>
    </div>
</div>


<!--LIST AND SEARCH SECTION -->
<section class="list-section">
    <h2>List of Deceased</h2>
    <p>Below is a list of all deceased individuals in the cemetery.</p>

    <!-- Search Input -->
    <input type="" id="searchInput" placeholder="Search by name..." onkeyup="searchTable()">
    <table border="1" id="deceasedTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Birth Date</th>
                <th>Death Date</th>
                <th>Obituary</th>
                <th>Grave Location</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($deceasedList as $person): ?>
                <tr>
                    <td><?php echo $person['deceased_id']; ?></td>
                    <td><?php echo htmlspecialchars($person['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($person['last_name']); ?></td>
                    <td><?php echo $person['birth_date']; ?></td>
                    <td><?php echo $person['death_date']; ?></td>
                    <td><?php echo htmlspecialchars($person['obituary'] ?: 'N/A'); ?></td>
                    <td>Section <?php echo $person['section']; ?>, Block <?php echo $person['block_number']; ?>, Lot <?php echo $person['lot_number']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
</section>
<!-- Display List of Graves -->
    <section id="grave" class="grave-section" style="display: none;" >
    <h2>List of Graves</h2>
    <table border="1">
    
    <thead>
        <tr>
            <th>Grave ID</th>
            <th>Section</th>
            <th>Block Number</th>
            <th>Lot Number</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($graves as $grave): ?>
            <tr>
                <td><?php echo $grave['grave_id']; ?></td>
                <td><?php echo htmlspecialchars($grave['section'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($grave['block_number'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($grave['lot_number'] ?? 'N/A'); ?></td>
                <td><?php echo ucfirst($grave['status']); ?></td>
                <td>
                <button class="editGraveBtn"
        data-modal="editGraveModal"
        data-id="<?php echo $grave['grave_id']; ?>"
        data-section="<?php echo htmlspecialchars($grave['section'] ?? ''); ?>"
        data-block="<?php echo htmlspecialchars($grave['block_number'] ?? ''); ?>"
        data-lot="<?php echo htmlspecialchars($grave['lot_number'] ?? ''); ?>">
        Edit
    </button>

    

    <button class="delete-btn" onclick="openDeleteGraveModal(<?php echo $grave['grave_id']; ?>)">Delete</button>
                    
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</section>
<!-- Delete Grave Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeDeleteGraveModal()">&times;</span>
        <h3 style="color: red;">Confirm Delete</h3>
        <p>Are you sure you want to delete this grave record?</p>
        <button onclick="confirmDeleteGraveModal()" class="confirm-btn">Yes, Delete</button>
        <button onclick="closeDeleteGraveModal()" class="cancel-btn">Cancel</button>
    </div>
</div>
<!-- EDIT MODAL GRAVE -->
<div id="editGraveModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Edit Grave</h3>
        <form method="POST" action="">
            <input type="hidden" name="grave_id" value="<?php echo isset($grave['grave_id']) ? $grave['grave_id'] : ''; ?>">
            <input type="text" name="section" value="<?php echo isset($grave['section']) ? $grave['section'] : ''; ?>" required>
            <input type="text" name="block_number" value="<?php echo isset($grave['block_number']) ? $grave['block_number'] : ''; ?>" required>
            <input type="text" name="lot_number" value="<?php echo isset($grave['lot_number']) ? $grave['lot_number'] : ''; ?>" required>
            <select name="status">
                <option value="available" <?php if (isset($grave['status']) && $grave['status'] == "available") echo "selected"; ?>>Available</option>
                <option value="taken" <?php if (isset($grave['status']) && $grave['status'] == "taken") echo "selected"; ?>>Taken</option>
            </select>
            <button type="submit" name="update_grave">Update Grave</button>
        </form>
    </div>
</div>
<!-- Display List of Deceased -->
<section id="deceased" class="deceased-section" style="display: none;">
<div class="table-container">
<h2>List of Deceased</h2>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Birth Date</th>
            <th>Death Date</th>
            <th>Obituary</th>
            <th>Grave Location</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($deceasedList as $person): ?>
            <tr>
                <td><?php echo $person['deceased_id']; ?></td>
                <td><?php echo htmlspecialchars($person['first_name']); ?></td>
                <td><?php echo htmlspecialchars($person['last_name']); ?></td>
                <td><?php echo $person['birth_date']; ?></td>
                <td><?php echo $person['death_date']; ?></td>
                <td><?php echo htmlspecialchars($person['obituary'] ?: 'N/A'); ?></td>
                <td>Section <?php echo $person['section']; ?>, Block <?php echo $person['block_number']; ?>, Lot <?php echo $person['lot_number']; ?></td>
                <td>

                <button class="editDeceasedBtn"
    data-modal="editDeceasedModal"
    data-id="<?php echo $person['deceased_id']; ?>"
    data-firstname="<?php echo htmlspecialchars($person['first_name']); ?>"
    data-lastname="<?php echo htmlspecialchars($person['last_name']); ?>"
    data-birthdate="<?php echo $person['birth_date']; ?>"
    data-deathdate="<?php echo $person['death_date']; ?>"
    data-obituary="<?php echo htmlspecialchars($person['obituary']); ?>">
    Edit
</button>
<!-- Delete by deceased_id -->
<button class="delete-btn" onclick="openDeleteDeceasedModal(<?php echo $person['deceased_id']; ?>)">Delete</button>

<!-- View Button -->
<button class="view-btn" onclick="console.log('Clicked ID:', <?php echo $person['deceased_id']; ?>); openViewDeceasedModal(<?php echo $person['deceased_id']; ?>)">View</button>     
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
</section>

<!-- VIEW MODAL DECEASED -->
<div id="viewDeceasedModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>View Deceased Individual</h3>
        <div>
            <p><strong>ID:</strong> <span id="viewDeceasedId"></span></p>
            <p><strong>First Name:</strong> <span id="viewFirstName"></span></p>
            <p><strong>Last Name:</strong> <span id="viewLastName"></span></p>
            <p><strong>Birth Date:</strong> <span id="viewBirthDate"></span></p>
            <p><strong>Death Date:</strong> <span id="viewDeathDate"></span></p>
            <p><strong>Obituary:</strong> <span id="viewObituary"></span></p>
            <p><strong>Grave Location:</strong> <span id="viewGraveLocation"></span></p>
            <p><strong>Death Certificate:</strong></p>
            <img id="viewDeathCertificate" src="" alt="Death Certificate" style="max-width: 100%; display: none;">
        </div>
    </div>
</div>
<!-- Delete Deceased Confirmation Modal -->
<div id="deleteModal2" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeDeleteDeceasedModal()">&times;</span>
        <h3 style="color: red;">Confirm Delete</h3>
        <p id="deleteMessage">Are you sure you want to delete this deceased record?</p>
        <button onclick="confirmDeleteDeceasedModal()" class="confirm-btn">Yes, Delete</button>
        <button onclick="closeDeleteDeceasedModal()" class="cancel-btn">Cancel</button>
    </div>
</div>
   <!-- EDIT MODAL DECEASED -->
<div id="editDeceasedModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Edit Deceased Individual</h3>
        <form action="edit_deceased.php" method="POST">
    <input type="hidden" name="deceased_id" id="editDeceasedId">
    
    <label>First Name:</label>
    <input type="text" name="first_name" id="editFirstName" required>

    <label>Last Name:</label>
    <input type="text" name="last_name" id="editLastName" required>

    <label>Birth Date:</label>
    <input type="date" name="birth_date" id="editBirthDate" required>

    <label>Death Date:</label>
    <input type="date" name="death_date" id="editDeathDate" required>

    <label>Obituary:</label>
    <textarea name="obituary" id="editObituary"></textarea>

    <button type="submit" name="update_deceased">Save Changes</button>
</form>

    </div>
</div>
    <!-- Modal ADD GRAVE -->
<div id="addGraveModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Add Grave</h3>
        <form action="" method="POST">
            <label>Section:</label>
            <input type="text" name="section" required>

            <label>Block Number:</label>
            <input type="text" name="block_number" required>

            <label>Lot Number:</label>
            <input type="text" name="lot_number" required>

            <button type="submit" name="add_grave">Add Grave</button>
        </form>
    </div>
</div>
<!-- ADD GRAVE Success Message Popup -->
<div id="addGraveSuccess" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('addGraveSuccess').style.display='none'">&times;</span>
        <h3 style="color: green;">Success!</h3>
        <p>Grave Added successfully.</p>
    </div>
</div>
<!-- ADD GRAVE Failed Message Popup -->
<div id="addGraveFailed" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('addGraveFailed').style.display='none'">&times;</span>
        <h3 style="color: red;">Failed!</h3>
        <p>Grave Already Exist. Please try again.</p>
    </div>
</div>
<!-- Modal ADD DECEASED -->
<div id="addDeceasedModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Add Deceased Individual</h3>
        <form action="" method="POST" enctype="multipart/form-data">
    <label>First Name:</label>
    <input type="text" name="first_name" required>
    
    <label>Last Name:</label>
    <input type="text" name="last_name" required>

    <label>Birth Date:</label>
    <input type="date" name="birth_date" required>

    <label>Death Date:</label>
    <input type="date" name="death_date" required>

    <label>Obituary:</label>
    <textarea name="obituary"></textarea>

    <h3>Assign Grave</h3>
    <label>Section:</label>
    <input type="text" name="section" required>

    <label>Block Number:</label>
    <input type="text" name="block_number" required>

    <label>Lot Number:</label>
    <input type="text" name="lot_number" required>

    <!-- File input for death certificate -->
    <div class="file-input-container">
        <label class="file-input-label">Upload Death Certificate:</label>
        <input type="file" id="death_certificate" name="death_certificate" accept="image/*" class="custom-file-input" onchange="updateFileName()">
        <label for="death_certificate" class="custom-file-label">Choose File</label>
        <span id="file-name">No file chosen</span>
    </div>

    <button type="submit" name="add_deceased">Add</button>
</form>

    </div>
</div>
<!-- ADD DECEASED Success Message Popup -->
<div id="addDeceasedSuccess" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('addDeceasedSuccess').style.display='none'">&times;</span>
        <h3 style="color: green;">Success!</h3>
        <p>Success! Deceased record has been added.</p>
    </div>
</div>
<!-- ADD DECEASED Failed Message Popup -->
<div id="addDeceasedFailed" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('addDeceasedFailed').style.display='none'">&times;</span>
        <h3 style="color: red;">Failed!</h3>
        <p>Error: The selected grave is already occupied.</p>
    </div>
</div>
<div id="graveNotExist" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('graveNotExist').style.display='none'">&times;</span>
        <h3 style="color: red;">Error!</h3>
        <p>The specified grave does not exist.</p>
    </div>
</div>  
<!-- EDIT GRAVE Success Message Popup -->
<div id="editGraveSuccess" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('editGraveSuccess').style.display='none'">&times;</span>
        <h3 style="color: green;">Success!</h3>
        <p>Grave details successfully updated.</p>
    </div>
</div>
<!-- EDIT DECEASED Success Message Popup -->
<div id="editDeceasedSuccess" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('editDeceasedSuccess').style.display='none'">&times;</span>
        <h3 style="color: green;">Success!</h3>
        <p>Deceased details successfully updated.</p>
    </div>
</div>
<!-- DELETE GRAVE Success Message Popup -->
<div id="deleteGraveSuccess" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('deleteGraveSuccess').style.display='none'">&times;</span>
        <h3 style="color: green;">Success!</h3>
        <p>Grave record successfully deleted.</p>
    </div>
</div>
<!-- DELETE GRAVE Failed Message Popup -->
<div id="deleteGraveFailed" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('deleteGraveFailed').style.display='none'">&times;</span>
        <h3 style="color: red;">Failed!</h3>
        <p>Cannot delete. This grave is assigned to a deceased individual.</p>
    </div>
</div>
<!-- DELETE DECEASED Success Message Popup -->
<div id="deleteDeceasedSuccess" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('deleteDeceasedSuccess').style.display='none'">&times;</span>
        <h3 style="color: green;">Success!</h3>
        <p>Deceased record successfully deleted.</p>
    </div>
</div>

<!-- UPLOAD Failed Message Popup -->
<div id="uploadFailed" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('uploadFailed').style.display='none'">&times;</span>
        <h3 style="color: red;">Failed!</h3>
        <p>Error: File upload failed.</p>
    </div>


    <!-- Invalid File Type Message Popup -->
<div id="invalidFileType" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('invalidFileType').style.display='none'">&times;</span>
        <h3 style="color: red;">Invalid File Type!</h3>
        <p>Error: Only specific file types are allowed.</p>
    </div>
</div>


</body>
</html>







