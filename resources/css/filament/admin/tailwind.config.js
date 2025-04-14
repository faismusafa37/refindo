import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    safelist: [
        'fi-simple-main-ctn',
    ],
    theme: {
        extend: {
            // Kalau mau custom warna, font, dsb bisa tambahin di sini
        },
    },
    plugins: [],
}
