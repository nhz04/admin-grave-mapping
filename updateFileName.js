function updateFileName() {
    const input = document.getElementById("death_certificate");
    const fileNameLabel = document.querySelector(".custom-file-label");
    const fileNameSpan = document.getElementById("file-name");

    if (input.files.length > 0) {
        const fileName = input.files[0].name;
        fileNameSpan.textContent = fileName; 
    } else {
        fileNameSpan.textContent = "No file chosen";
    }
}