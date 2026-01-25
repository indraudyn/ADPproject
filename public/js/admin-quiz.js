document.getElementById("searchInput").addEventListener("keyup", function () {
    let value = this.value.toLowerCase();
    document.querySelectorAll("tbody tr").forEach((row) => {
        row.style.display = row.textContent.toLowerCase().includes(value)
            ? ""
            : "none";
    });
});
