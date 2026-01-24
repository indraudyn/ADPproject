document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const rows = document.querySelectorAll("#ceritaTable tr");

    searchInput.addEventListener("keyup", function () {
        const value = this.value.toLowerCase();

        rows.forEach((row) => {
            row.style.display = row.innerText.toLowerCase().includes(value)
                ? ""
                : "none";
        });
    });
});
