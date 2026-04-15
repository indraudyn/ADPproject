const menuToggleMain = document.getElementById("menu-toggle");
if (menuToggleMain && typeof window.sidebarToggleBound === 'undefined') {
    window.sidebarToggleBound = true;
    menuToggleMain.addEventListener("click", function () {
        const sidebar = document.querySelector(".sidebar");
        if (sidebar) sidebar.classList.toggle("collapsed");
    });
}
