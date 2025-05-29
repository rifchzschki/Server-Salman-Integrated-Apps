<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Cloudinary\Configuration\Configuration; // Import Configuration

class CloudinaryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Cara 1: Menggunakan CLOUDINARY_URL dari .env
        // Pastikan CLOUDINARY_URL di .env Anda sudah benar
        // contoh: cloudinary://API_KEY:API_SECRET@CLOUD_NAME?secure=true
        if (env('CLOUDINARY_URL')) {
            Configuration::instance(env('CLOUDINARY_URL'));
        } else {
            // Cara 2: Mengkonfigurasi secara individual jika CLOUDINARY_URL tidak ada
            // atau jika Anda lebih suka cara ini
            if (env('CLOUDINARY_CLOUD_NAME') && env('CLOUDINARY_API_KEY') && env('CLOUDINARY_API_SECRET')) {
                Configuration::instance([
                    'cloud' => [
                        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                        'api_key'    => env('CLOUDINARY_API_KEY'),
                        'api_secret' => env('CLOUDINARY_API_SECRET'),
                    ],
                    'url' => [
                        'secure' => true // Menggunakan HTTPS
                    ]
                ]);
            }
        }
    }
}
