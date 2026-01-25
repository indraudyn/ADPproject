// memastikan hanya satu jawaban dipilih
document.querySelectorAll("input[type=radio]").forEach((radio) => {
    radio.addEventListener("change", () => {
        document
            .querySelectorAll("input[type=radio]")
            .forEach((r) => (r.checked = false));
        radio.checked = true;
    });
});
