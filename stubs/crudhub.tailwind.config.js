/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/views/crudhub.blade.php",
        "./resources/js/crudhub/**/*.{js,vue}",
        "./vendor/neurony/crudhub/resources/js/**/*.{js,vue}"
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require("@tailwindcss/forms"),
    ],
};
