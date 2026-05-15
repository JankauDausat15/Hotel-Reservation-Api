<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\AuthController; // ← TAMBAH INI

// 1. Mengambil semua tipe kamar
// Endpoint: GET http://127.0.0.1:8000/api/type-room
// Response: { status, message, data: [{ id, name, description, price_per_night, image, reservations_count }] }
Route::get('/type-room', [HotelController::class, 'getRooms']);

// 2. Mengambil seluruh data reservasi
// Endpoint: GET http://127.0.0.1:8000/api/reservations
// Response: { status, message, data: [{ id, user_id, room_type_id, ..., user: {}, room_type: {} }] }
Route::get('/reservations', [HotelController::class, 'getAllReservations']);

// 12. Mengambil booking/reservasi terbaru ← HARUS DI ATAS /{id}
// Endpoint: GET http://127.0.0.1:8000/api/reservations/latest
// Response: { status, message, data: [5 reservasi terbaru dengan relasi user & room_type] }
Route::get('/reservations/latest', [HotelController::class, 'getLatestReservations']);

// 3. Mengambil reservasi berdasarkan ID
// Endpoint: GET http://127.0.0.1:8000/api/reservations/{id}
// Contoh:   GET http://127.0.0.1:8000/api/reservations/1
// Response: { status, message, data: { id, ..., user: {}, room_type: {} } }
Route::get('/reservations/{id}', [HotelController::class, 'getReservationById']);

// 4. Cek ketersediaan kamar
// Endpoint: GET http://127.0.0.1:8000/api/rooms/availability
Route::get('/rooms/availability', [HotelController::class, 'checkAvailability']);

// 5. Mengambil seluruh data reservasi milik user tertentu
// Endpoint: GET http://127.0.0.1:8000/api/users/{user_id}/reservations
// Contoh:   GET http://127.0.0.1:8000/api/users/1/reservations
// Response: { status, message, data: [{ id, ..., room_type: {} }] }
Route::get('/users/{user_id}/reservations', [HotelController::class, 'getUserReservations']);

// 6. Membuat reservasi baru
// Endpoint: POST http://127.0.0.1:8000/api/reservations
// Body:     { user_id, room_type_id, customer_name, customer_email, check_in, check_out }
// Response: { status, message, data: { id, ..., user: {}, room_type: {} } }
Route::post('/reservations', [HotelController::class, 'storeReservation']);

// 7. Registrasi user
// Endpoint: POST http://127.0.0.1:8000/api/signup
// Body:     { name, email, password, password_confirmation }
// Response: { status, message, data: { id, name, email } }
Route::post('/signup', [AuthController::class, 'signup']);

// 8. Login user
// Endpoint: POST http://127.0.0.1:8000/api/auth/login
// Body:     { email, password }
// Response: { status, message, data: { id, name, email } }
Route::post('/auth/login', [AuthController::class, 'login']);

// 9. Menghapus reservasi (Cancel Booking)
// Endpoint: DELETE http://127.0.0.1:8000/api/reservations/{id}
// Contoh:   DELETE http://127.0.0.1:8000/api/reservations/1
// Response: { status, message }
Route::delete('/reservations/{id}', [HotelController::class, 'deleteReservation']);

// 10. Menghapus user
// Endpoint: DELETE http://127.0.0.1:8000/api/users/{user_id}
// Contoh:   DELETE http://127.0.0.1:8000/api/users/1
// Response: { status, message }
Route::delete('/users/{user_id}', [AuthController::class, 'deleteUser']);

// 11. Update reservasi (PUT - update semua field)
// Endpoint: PUT http://127.0.0.1:8000/api/reservations/{id}
// Contoh:   PUT http://127.0.0.1:8000/api/reservations/1
// Body:     { user_id, room_type_id, customer_name, customer_email, check_in, check_out }
// Response: { status, message, data: { id, ..., user: {}, room_type: {} } }
Route::put('/reservations/{id}', [HotelController::class, 'updateReservation']);

// Update reservasi (PATCH - update sebagian field)
// Endpoint: PATCH http://127.0.0.1:8000/api/reservations/{id}
// Contoh:   PATCH http://127.0.0.1:8000/api/reservations/1
// Body:     { field_yang_mau_diupdate }
// Response: { status, message, data: { id, ..., user: {}, room_type: {} } }
Route::patch('/reservations/{id}', [HotelController::class, 'patchReservation']);