const mix = require('laravel-mix');

mix.browserSync({
    proxy: 'your-local-dev-url.test', // Replace with your local development URL
    files: [
        'resources/views/**/*.blade.php',
        'resources/js/**/*.js',
        'resources/css/**/*.css',
        'app/**/*.php',
        'routes/**/*.php'
    ]
});

mix.js('resources/js/app.js', 'public/js')
   .postCss('resources/css/app.css', 'public/css', [
       require('tailwindcss'),
   ]);
