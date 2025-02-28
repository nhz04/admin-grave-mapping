function openViewDeceasedModal(id) {
    console.log("Function is working! ID:", id); // Debugging

    let modal = document.getElementById("viewDeceasedModal");
    if (!modal) {
        console.error("Modal not found!");
        return;
    }

    // Fetch deceased data from PHP
    fetch(`view_deceased.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            console.log(data); // Debugging

            if (data.error) {
                alert(data.error);
                return;
            }

            // Populate modal fields
            document.getElementById("viewDeceasedId").textContent = data.deceased_id || "N/A";
            document.getElementById("viewFirstName").textContent = data.first_name || "N/A";
            document.getElementById("viewLastName").textContent = data.last_name || "N/A";
            document.getElementById("viewBirthDate").textContent = data.birth_date || "N/A";
            document.getElementById("viewDeathDate").textContent = data.death_date || "N/A";
            document.getElementById("viewObituary").textContent = data.obituary || "N/A";
            document.getElementById("viewGraveLocation").textContent = data.grave_location || "N/A";

            let certImg = document.getElementById("viewDeathCertificate");
            if (data.death_certificate) {
                certImg.src = data.death_certificate; 
                certImg.style.display = "block";
            } else {
                certImg.style.display = "none";
            }

            // Show modal
            modal.style.display = "block";
        })
        .catch(error => console.error("Error fetching deceased data:", error));
}

// Close modal when clicking the close button
document.querySelector(".close").addEventListener("click", function() {
    document.getElementById("viewDeceasedModal").style.display = "none";
});

// Close modal when clicking outside it
window.onclick = function(event) {
    let modal = document.getElementById("viewDeceasedModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
};


