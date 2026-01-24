tinymce.init({
    selector: "#ceritaEditor",
    height: 300,
    menubar: "file edit insert format table",
    plugins: "lists link image preview code table",
    toolbar:
        "undo redo | bold italic underline strikethrough | " +
        "alignleft aligncenter alignright alignjustify | " +
        "bullist numlist | outdent indent | link image | preview",
    branding: false,
});
