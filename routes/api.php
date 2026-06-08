<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\TransactionController; 
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes - Amanjiwo Reservation
|--------------------------------------------------------------------------
*/

// 1. Mengambil semua tipe kamar (WITH CATEGORY, SEARCH & FILTER HARGA)
// Endpoint: GET http://127.0.0.1:8000/api/rooms
// Endpoint: GET http://127.0.0.1:8000/api/type-room
// Response: { status, message, data: [{ id, name, description, price_per_night, image, ... }] }
Route::get('/rooms', [HotelController::class, 'getRooms']);
Route::get('/type-room', [HotelController::class, 'getRooms']);

// 2. Mengambil seluruh data reservasi
// Endpoint: GET http://127.0.0.1:8000/api/reservations
// Response: { status, message, data: [{ id, user_id, room_type_id, ..., user: {}, room_type: {} }] }
Route::get('/reservations', [HotelController::class, 'getAllReservations']);

// 3. Mengambil booking/reservasi terbaru (Top 5)
// Endpoint: GET http://127.0.0.1:8000/api/reservations/latest
// Response: { status, message, data: [5 reservasi terbaru] }
Route::get('/reservations/latest', [HotelController::class, 'getLatestReservations']);

// 4. Mengambil reservasi berdasarkan ID
// Endpoint: GET http://127.0.0.1:8000/api/reservations/{id}
Route::get('/reservations/{id}', [HotelController::class, 'getReservationById']);

// 5. Cek ketersediaan kamar
// Endpoint: GET http://127.0.0.1:8000/api/rooms/availability
Route::get('/rooms/availability', [HotelController::class, 'checkAvailability']);
Route::post('/rooms/check-availability', [HotelController::class, 'checkAvailability']);

// 6. Mengambil seluruh data reservasi milik user tertentu
// Endpoint: GET http://127.0.0.1:8000/api/users/{user_id}/reservations
Route::get('/users/{user_id}/reservations', [HotelController::class, 'getUserReservations']);

// 7. Membuat reservasi baru
// Endpoint: POST http://127.0.0.1:8000/api/reservations
// Body: { user_id, room_type_id, customer_name, customer_email, check_in, check_out }
Route::post('/reservations', [HotelController::class, 'storeReservation']);

// 8. Registrasi user
// Endpoint: POST http://127.0.0.1:8000/api/signup
Route::post('/signup', [AuthController::class, 'signup']);

// 9. Login user
// Endpoint: POST http://127.0.0.1:8000/api/auth/login
Route::post('/auth/login', [AuthController::class, 'login']);

// 10. Menghapus reservasi (Cancel Booking)
// Endpoint: DELETE http://127.0.0.1:8000/api/reservations/{id}
Route::delete('/reservations/{id}', [HotelController::class, 'deleteReservation']);

// 11. Menghapus user
// Endpoint: DELETE http://127.0.0.1:8000/api/users/{user_id}
Route::delete('/users/{user_id}', [AuthController::class, 'deleteUser']);

// 12. Update reservasi (PUT & PATCH)
// Endpoint: PUT http://127.0.0.1:8000/api/reservations/{id}
Route::put('/reservations/{id}', [HotelController::class, 'updateReservation']);
Route::patch('/reservations/{id}', [HotelController::class, 'updateReservation']);


// ==========================================
// TRANSACTION ROUTES (Midtrans/Manual)
// ==========================================
// POST    http://127.0.0.1:8000/api/transactions
// GET     http://127.0.0.1:8000/api/transactions
// GET     http://127.0.0.1:8000/api/transactions/status/pending
// POST    http://127.0.0.1:8000/api/transactions/{id}/pay
Route::post('/transactions', [TransactionController::class, 'store']);
Route::get('/transactions', [TransactionController::class, 'index']);
Route::get('/transactions/status/{status}', [TransactionController::class, 'filterByStatus']);
Route::get('/transactions/{id}', [TransactionController::class, 'show']);
Route::post('/transactions/{id}/pay', [TransactionController::class, 'pay']);
Route::put('/transactions/{id}/cancel', [TransactionController::class, 'cancel']);
Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);