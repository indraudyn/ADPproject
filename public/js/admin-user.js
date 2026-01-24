document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("searchInput");
    const rows = document.querySelectorAll("#userTable tr");

    searchInput.addEventListener("keyup", () => {
        const value = searchInput.value.toLowerCase();

        rows.forEach((row) => {
            row.style.display = row.innerText.toLowerCase().includes(value)
                ? ""
                : "none";
        });
    });
});
