<?php //MAIN 

include 'db.php';

// Fetch all graves 
$sql = "SELECT g.grave_id, g.section, g.block_number, g.lot_number,g.status, d.first_name, d.last_name
        FROM graves g
        LEFT JOIN deceased d ON g.grave_id = d.grave_id
        ORDER BY g.grave_id ASC";  
$result = $conn->query($sql);
$graves = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $graves[] = $row;
    }
}

// Fetch all deceased individuals
$sql = "SELECT d.deceased_id, d.first_name, d.last_name, d.birth_date, d.death_date, d.obituary, 
               g.grave_id, g.section, g.block_number, g.lot_number, g.status
        FROM deceased d
        LEFT JOIN graves g ON d.grave_id = g.grave_id
        ORDER BY g.grave_id ASC";
$result = $conn->query($sql);
$deceasedList = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $deceasedList[] = $row;
    }
}


// Fetch total graves
$sql = "SELECT COUNT(*) AS total_graves FROM graves";
$result = $conn->query($sql);
$total_graves = ($result->num_rows > 0) ? $result->fetch_assoc()['total_graves'] : 0;

// Fetch total deceased
$sql = "SELECT COUNT(*) AS total_deceased FROM deceased";
$result = $conn->query($sql);
$total_deceased = ($result->num_rows > 0) ? $result->fetch_assoc()['total_deceased'] : 0;

?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memorium - Admin</title>
    <link rel="stylesheet" href="style/index-style2.css">
    

    <script src="script.js" defer></script>
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
        <img src="style/image/logo.png" alt="Logo" class="logo">
        <div class="text">Welcome to Admin Dashboard</div>
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
    <input type="text" id="searchInput" placeholder="Search by name..." onkeyup="searchTable()">

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

    <section id="grave" class="grave-section" style="display: none;" >

    <!-- Display List of Graves -->
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

                    <a href="delete_grave.php?id=<?php echo $grave['grave_id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</section>

<!-- EDIT MODAL GRAVE -->
<div id="editGraveModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Edit Grave</h3>
        <form method="POST" action="edit_grave.php"> <!-- Set correct action -->
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


                    <a href="delete.php?id=<?php echo $person['deceased_id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
    </section>


   <!-- EDIT MODAL DECEASED -->
<div id="editDeceasedModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Edit Deceased Individual</h3>
        <form action="edit.php" method="POST">
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
        <form action="add_grave.php" method="POST">
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
        <form action="add_deceased.php" method="POST">
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







   


    

</body>

</html>

<script>


document.addEventListener("DOMContentLoaded", function () {
    const editButtons = document.querySelectorAll(".editGraveBtn");
    const modal = document.getElementById("editGraveModal");
    const closeBtn = modal.querySelector(".close");

    editButtons.forEach(button => {
        button.addEventListener("click", function () {
            document.querySelector("input[name='grave_id']").value = this.dataset.id;
            document.querySelector("input[name='section']").value = this.dataset.section;
            document.querySelector("input[name='block_number']").value = this.dataset.block;
            document.querySelector("input[name='lot_number']").value = this.dataset.lot;

            modal.style.display = "block";
        });
    });

    closeBtn.addEventListener("click", function () {
        modal.style.display = "none";
    });

    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
});






document.addEventListener("DOMContentLoaded", function () {
    function openModal(modalId) {
        let modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = "block";
        }
    }

    function closeModal(modal) {
        modal.style.display = "none";
    }

    // Open modal when buttons are clicked
    document.getElementById("openAddGrave").addEventListener("click", function () {
        openModal("addGraveModal");
    });

    document.getElementById("openAddDeceased").addEventListener("click", function () {
        openModal("addDeceasedModal");
    });

    // Close modal when clicking the close button
    document.querySelectorAll(".close").forEach(button => {
        button.addEventListener("click", function () {
            closeModal(this.closest(".modal"));
        });
    });

    // Close modal when clicking outside modal content
    window.addEventListener("click", function (event) {
        document.querySelectorAll(".modal").forEach(modal => {
            if (event.target === modal) {
                closeModal(modal);
            }
        });
    });
});


document.addEventListener("DOMContentLoaded", function () {
    var editButtons = document.querySelectorAll(".editDeceasedBtn");
    var modal = document.getElementById("editDeceasedModal");

    editButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            // Get data attributes from the clicked button
            var deceasedId = this.getAttribute("data-id");
            var firstName = this.getAttribute("data-firstname");
            var lastName = this.getAttribute("data-lastname");
            var birthDate = this.getAttribute("data-birthdate");
            var deathDate = this.getAttribute("data-deathdate");
            var obituary = this.getAttribute("data-obituary");

            // Populate the modal form fields
            document.getElementById("editDeceasedId").value = deceasedId;
            document.getElementById("editFirstName").value = firstName;
            document.getElementById("editLastName").value = lastName;
            document.getElementById("editBirthDate").value = birthDate;
            document.getElementById("editDeathDate").value = deathDate;
            document.getElementById("editObituary").value = obituary;

            // Show the modal
            modal.style.display = "block";
        });
    });

    // Close modal when clicking the close button
    document.querySelector(".modal .close").addEventListener("click", function () {
        modal.style.display = "none";
    });

    // Close modal when clicking outside the modal content
    window.addEventListener("click", function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    });
});







if (window.location.href.indexOf("status=addGraveSuccess") !== -1) {
    document.getElementById("addGraveSuccess").style.display = "block";
} else if (window.location.href.indexOf("status=addGraveFailed") !== -1) {  
    document.getElementById("addGraveFailed").style.display = "block"; 
} else if (window.location.href.indexOf("status=addDeceasedSuccess") !== -1) {  
    document.getElementById("addDeceasedSuccess").style.display = "block";
} else if (window.location.href.indexOf("status=addDeceasedFailed") !== -1) {  
    document.getElementById("addDeceasedFailed").style.display = "block";
} else if (window.location.href.indexOf("status=graveNotExist") !== -1) {  
    document.getElementById("graveNotExist").style.display = "block";
}

setTimeout(() => {
    window.history.replaceState(null, null, window.location.pathname);
}, 3000);






// Show the selected section
function showSection(sectionId) {
    // Hide all sections
    document.querySelectorAll('.home-section, .grave-section, .deceased-section, .modal').forEach(section => {
        section.style.display = 'none';
    });

    // Show the selected section
    document.getElementById(sectionId).style.display = 'block';
}
</script>

<script>
    function searchTable() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toLowerCase().split(" "); // Split the input by space
        table = document.getElementById("deceasedTable");
        tr = table.getElementsByTagName("tr");

        for (i = 1; i < tr.length; i++) { // Start at 1 to skip table header
            td = tr[i].getElementsByTagName("td");
            let found = false;

            // Combine first and last name and check if the search input matches the full name
            let fullName = (td[1].textContent || td[1].innerText) + " " + (td[2].textContent || td[2].innerText); // Combine first and last name
            
            // Check if every part of the input matches the full name
            let matches = filter.every(part => fullName.toLowerCase().includes(part));
            if (matches) {
                found = true;
            }

            tr[i].style.display = found ? "" : "none"; // Show or hide row
        }
    }
</script>