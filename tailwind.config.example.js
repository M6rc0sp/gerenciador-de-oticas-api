/**
 * Example Tailwind config to prevent utility class name collisions with Bootstrap.
 * This file is only an example; using `prefix: 'tw-'` requires updating markup classes.
 * Alternatives: disable specific utilities or remove plugins generating `.collapse`.
 */
module.exports = {
    prefix: 'tw-',
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {},
    },
    plugins: [],
}
