document.addEventListener("DOMContentLoaded", function () {
    const quill = new Quill("#editor", {
        theme: "snow",
        placeholder: "Tulis cerita di sini...",
        modules: {
            toolbar: [
                ["bold", "italic", "underline"],
                [{ header: [1, 2, false] }],
                [{ list: "ordered" }, { list: "bullet" }],
                ["link"],
                ["clean"],
            ],
        },
    });

    const form = document.getElementById("editForm");
    const ceritaInput = document.getElementById("ceritaInput");

    form.addEventListener("submit", function () {
        ceritaInput.value = quill.root.innerHTML;
    });
});
