<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class HotelController extends Controller
{
    // ==========================================
    // GET ALL ROOM TYPES (FITUR SEARCH & FILTER)
    // ==========================================
    public function getRooms(Request $request)
    {
        $query = RoomType::withCount('reservations');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $search = $request->search ?? $request->name;
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($request->filled('min_price') && $request->filled('max_price') && $request->min_price == $request->max_price) {
            $query->where('price_per_night', '<=', $request->min_price);
        } else {
            if ($request->filled('min_price')) {
                $query->where('price_per_night', '>=', $request->min_price);
            }
            if ($request->filled('max_price')) {
                $query->where('price_per_night', '<=', $request->max_price);
            }
        }

        $rooms = $query->latest()->get();

        return response()->json([
            'status'  => true,
            'message' => 'List tipe kamar berhasil diambil',
            'count'   => $rooms->count(),
            'data'    => $rooms
        ], 200);
    }

    // ==========================================
    // CEK KETERSEDIAAN KAMAR ✅ BARU
    // ==========================================
    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_type_id' => 'required|exists:room_types,id',
            'check_in'     => 'required|date',
            'check_out'    => 'required|date|after_or_equal:check_in',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'available' => false,
                'message'   => $validator->errors()->first()
            ], 422);
        }

        $room     = RoomType::find($request->room_type_id);
        $checkIn  = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $nights   = $checkIn->diffInDays($checkOut);
        $nights   = ($nights <= 0) ? 1 : $nights;

        
        $conflict = Reservation::where('room_type_id', $request->room_type_id)
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut->copy()->subDay()])
                      ->orWhereBetween('check_out', [$checkIn->copy()->addDay(), $checkOut])
                      ->orWhere(function ($q) use ($checkIn, $checkOut) {
                          $q->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                      });
            })
            ->exists();

        if ($conflict) {
            return response()->json([
                'status'    => true,
                'available' => false,
                'message'   => 'Kamar tidak tersedia pada tanggal yang dipilih'
            ], 200);
        }

        $totalPrice = $nights * $room->price_per_night;

        return response()->json([
            'status'      => true,
            'available'   => true,
            'nights'      => $nights,
            'total_price' => $totalPrice,
            'message'     => 'Kamar tersedia'
        ], 200);
    }

    // ==========================================
    // CREATE RESERVATION
    // ==========================================
    public function storeReservation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'        => 'required|exists:users,id',
            'room_type_id'   => 'required|exists:room_types,id',
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'check_in'       => 'required|date',
            'check_out'      => 'required|date|after_or_equal:check_in',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $room = RoomType::find($request->room_type_id);

        if (!$room) {
            return response()->json([
                'status'  => false,
                'message' => 'Tipe kamar tidak ditemukan'
            ], 404);
        }

        $checkIn    = Carbon::parse($request->check_in);
        $checkOut   = Carbon::parse($request->check_out);
        $nights     = $checkIn->diffInDays($checkOut);
        $nights     = ($nights <= 0) ? 1 : $nights;
        $totalPrice = $nights * $room->price_per_night;

        $reservation = Reservation::create([
            'user_id'        => $request->user_id,
            'room_type_id'   => $request->room_type_id,
            'customer_name'  => $request->customer_name,
            'customer_email' => $request->customer_email,
            'check_in'       => $request->check_in,
            'check_out'      => $request->check_out,
            'total_price'    => $totalPrice,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Reservasi berhasil dibuat',
            'data'    => $reservation->load(['user', 'roomType'])
        ], 201);
    }

    // ==========================================
    // GET USER RESERVATIONS
    // ==========================================
    public function getUserReservations($user_id)
    {
        $reservations = Reservation::with('roomType')
            ->where('user_id', $user_id)
            ->latest()
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Riwayat reservasi user berhasil diambil',
            'data'    => $reservations
        ], 200);
    }

    // ==========================================
    // FUNGSI PENDUKUNG LAINNYA
    // ==========================================
    public function getAllReservations()
    {
        return response()->json([
            'status'  => true,
            'message' => 'List semua reservasi berhasil diambil',
            'data'    => Reservation::with(['user', 'roomType'])->latest()->get()
        ], 200);
    }

    public function getReservationById($id)
    {
        $reservation = Reservation::with(['user', 'roomType'])->find($id);
        if (!$reservation) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
        return response()->json(['status' => true, 'data' => $reservation], 200);
    }

    public function deleteReservation($id)
    {
        $reservation = Reservation::find($id);
        if (!$reservation) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
        $reservation->delete();
        return response()->json(['status' => true, 'message' => 'Berhasil dihapus'], 200);
    }

    public function getLatestReservations()
    {
        $reservations = Reservation::with(['user', 'roomType'])->latest()->take(5)->get();
        return response()->json(['status' => true, 'data' => $reservations], 200);
    }
}