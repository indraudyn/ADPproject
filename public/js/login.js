document.addEventListener("DOMContentLoaded", function () {
    console.log("Login page loaded (Laravel)");
});

function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);

    if (input.type === "password") {
        input.type = "text";
        icon.classList.add("active");
    } else {
        input.type = "password";
        icon.classList.remove("active");
    }
}
