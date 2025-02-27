<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Review;


class ReviewController extends Controller
{
    public function updateReview(Request $request) {
        $request->validate([
            'critic' => 'required|min:5',
            'rating' => 'required|integer|min:1|max:5',
            'movie_id' => 'required|exists:movies,id'
        ], [
            'critic.required' => 'Kritik wajib diisi',
            'critic.min' => 'Komentar minimal 5 karakter',
            'movie_id.required' => 'ID movie wajib diisi',
            'rating.required' => 'Rating wajib diisi',
            'rating.integer' => 'Rating harus berupa angka',
            'movie_id.exists' => 'ID movie tidak ditemukan'
        ]);
    
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'message' => 'User tidak terautentikasi'
            ], 401);
        }
    
        $movie = Movie::find($request->input('movie_id'));
        if (!$movie) {
            return response()->json([
                'message' => 'Movie tidak ditemukan'
            ], 404);
        }
    
        try {
            $review = Review::updateOrCreate(
                ['user_id' => $user->id, 'movie_id' => $movie->id],
                ['critic' => $request->input('critic'), 'rating' => $request->input('rating')]
            );
    
            return response()->json([
                'message' => 'Kritik berhasil dibuat/diupdate',
                'data' => $review,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan kritik',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
}
