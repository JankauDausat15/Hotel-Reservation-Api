<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomType;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            // =========================================================
            // KATEGORI: JUNIOR SUITE (10 Items - Format Rapi)
            // =========================================================
            ['category' => 'Junior Suite', 'name' => 'Junior Pool Suite', 'description' => '2 Guests · King Bed · Pool View', 'price_per_night' => 2500000, 'image' => 'junior_pool'],
            ['category' => 'Junior Suite', 'name' => 'Junior Garden Suite', 'description' => '2 Guests · Queen Bed · Garden View', 'price_per_night' => 2200000, 'image' => 'junior_garden'],
            ['category' => 'Junior Suite', 'name' => 'Junior Deluxe Suite', 'description' => '2 Guests · King Bed · City View', 'price_per_night' => 2700000, 'image' => 'junior_deluxe'],
            ['category' => 'Junior Suite', 'name' => 'Junior Corner Suite', 'description' => '2 Guests · Twin Bed · Corner View', 'price_per_night' => 2400000, 'image' => 'junior_corner'],
            ['category' => 'Junior Suite', 'name' => 'Junior Terrace Suite', 'description' => '2 Guests · King Bed · Terrace', 'price_per_night' => 2600000, 'image' => 'junior_terrace'],
            ['category' => 'Junior Suite', 'name' => 'Junior Courtyard Suite', 'description' => '2 Guests · Queen Bed · Courtyard', 'price_per_night' => 2100000, 'image' => 'junior_courtyard'],
            ['category' => 'Junior Suite', 'name' => 'Junior Premium Suite', 'description' => '2 Guests · King Bed · Premium View', 'price_per_night' => 2900000, 'image' => 'junior_premium'],
            ['category' => 'Junior Suite', 'name' => 'Junior Balcony Suite', 'description' => '2 Guests · Queen Bed · Balcony', 'price_per_night' => 2350000, 'image' => 'junior_balcony'],
            ['category' => 'Junior Suite', 'name' => 'Junior Heritage Suite', 'description' => '2 Guests · King Bed · Heritage Wing', 'price_per_night' => 2450000, 'image' => 'junior_heritage'],
            ['category' => 'Junior Suite', 'name' => 'Junior Signature Suite', 'description' => '2 Guests · King Bed · Signature', 'price_per_night' => 2800000, 'image' => 'junior_signature'],

            // =========================================================
            // KATEGORI: EXECUTIVE SUITE (10 Items)
            // =========================================================
            
             ['category' => 'Executive Suite', 'name' => 'Executive King Suite', 'description' => '2 Guests · King Bed · Ocean View', 'price_per_night' => 4500000, 'image' => 'executive_king_suite'],
             ['category' => 'Executive Suite', 'name' => 'Executive Twin Suite', 'description' => '2 Guests · Twin Bed · Garden View', 'price_per_night' => 4200000, 'image' => 'executive_twin_suite'],
             ['category' => 'Executive Suite', 'name' => 'Executive Lounge Suite', 'description' => '2 Guests · King Bed · Lounge Access', 'price_per_night' => 4800000, 'image' => 'executive_lounge_suite'],
             ['category' => 'Executive Suite', 'name' => 'Executive Corner Suite', 'description' => '3 Guests · King Bed · Corner View', 'price_per_night' => 4300000, 'image' => 'executive_corner_suite'],
             ['category' => 'Executive Suite', 'name' => 'Executive Terrace Suite', 'description' => '2 Guests · King Bed · Private Deck', 'price_per_night' => 5000000, 'image' => 'executive_terrace_suite'],
             ['category' => 'Executive Suite', 'name' => 'Executive Business Suite', 'description' => '2 Guests · King Bed · Work Desk', 'price_per_night' => 4600000, 'image' => 'executive_business_suite'],
             ['category' => 'Executive Suite', 'name' => 'Executive Premier Suite', 'description' => '2 Guests · King Bed · Premier View', 'price_per_night' => 5200000, 'image' => 'executive_premier_suite'],
             ['category' => 'Executive Suite', 'name' => 'Executive Heritage Suite', 'description' => '2 Guests · Queen Bed · Heritage', 'price_per_night' => 4400000, 'image' => 'executive_heritage_suite'],
             ['category' => 'Executive Suite', 'name' => 'Executive Spa Suite', 'description' => '2 Guests · King Bed · In-Room Spa', 'price_per_night' => 5500000, 'image' => 'executive_spa_suite'],
             ['category' => 'Executive Suite', 'name' => 'Executive Signature Suite', 'description' => '2 Guests · King Bed · Signature', 'price_per_night' => 5800000, 'image' => 'executive_signature_suite'],


            // =========================================================
            // KATEGORI: PRESIDENTIAL SUITE (10 Items)
            // =========================================================
            
             ['category' => 'Presidential Suite', 'name' => 'Royal Presidential Suite', 'description' => '4 Guests · 2 King Beds · Royal View', 'price_per_night' => 15000000, 'image' => 'royal_presidential_suite'],
             ['category' => 'Presidential Suite', 'name' => 'Grand Presidential Suite', 'description' => '4 Guests · Master Bed · Panoramic View', 'price_per_night' => 18000000, 'image' => 'grand_presidential_suite'],
             ['category' => 'Presidential Suite', 'name' => 'Imperial Presidential Suite', 'description' => '6 Guests · 3 Beds · Full Floor', 'price_per_night' => 20000000, 'image' => 'imperial_presidential_suite'],
             ['category' => 'Presidential Suite', 'name' => 'Heritage Presidential Suite', 'description' => '4 Guests · King Bed · Heritage Wing', 'price_per_night' => 14000000, 'image' => 'heritage_presidential_suite'],
             ['category' => 'Presidential Suite', 'name' => 'Garden Presidential Suite', 'description' => '4 Guests · 2 King Beds · Private Garden', 'price_per_night' => 16000000, 'image' => 'garden_presidential_suite'],
             ['category' => 'Presidential Suite', 'name' => 'Ocean Presidential Suite', 'description' => '4 Guests · King Bed · Ocean View', 'price_per_night' => 22000000, 'image' => 'ocean_presidential_suite'],
             ['category' => 'Presidential Suite', 'name' => 'Sky Presidential Suite', 'description' => '4 Guests · 2 King Beds · Skyline View', 'price_per_night' => 25000000, 'image' => 'sky_presidential_suite'],
             ['category' => 'Presidential Suite', 'name' => 'Palace Presidential Suite', 'description' => '6 Guests · 3 King Beds · Palace Wing', 'price_per_night' => 30000000, 'image' => 'palace_presidential_suite'],
             ['category' => 'Presidential Suite', 'name' => 'Signature Presidential Suite', 'description' => '4 Guests · Master Bed · Signature', 'price_per_night' => 28000000, 'image' => 'signature_presidential_suite'],
             ['category' => 'Presidential Suite', 'name' => 'Ultra Presidential Suite', 'description' => '8 Guests · 4 Beds · Penthouse', 'price_per_night' => 35000000, 'image' => 'ultra_presidential_suite'],

        ];

        foreach ($rooms as $room) {
            // Menggunakan updateOrCreate agar tidak duplikat jika seeder dijalankan ulang
            RoomType::updateOrCreate(['name' => $room['name']], $room);
        }
    }
}