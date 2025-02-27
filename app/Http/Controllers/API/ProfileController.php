<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use Illuminate\Support\Facades\Log;
class ProfileController extends Controller
{
    public function updateProfile(Request $request)
    {
        // Ambil user yang sedang login
        $user = auth()->user();

        // Validasi input
        $request->validate([
            'bio' => 'required|string',
            'age' => 'required|integer',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048', // Gambar opsional
        ], [
            'required' => 'Inputan :attribute wajib diisi',
            'integer' => 'Inputan :attribute harus berupa angka',
            'image' => 'File harus berupa gambar',
            'mimes' => 'File harus berformat: jpg, png, jpeg, gif, svg',
            'max' => 'Ukuran file tidak boleh lebih dari 2MB',
        ]);

        try {
            $user = auth()->user();
        
            // Pastikan user ada
            if (!$user) {
                return response()->json([
                    'message' => 'User tidak ditemukan',
                ], 404);
            }
        
            // Pastikan hanya nilai yang ada dalam request yang diperbarui
            $data = [
                'bio' => $request->input('bio', ''), // Default ke string kosong jika null
                'age' => $request->input('age', 0), // Default ke 0 jika null
            ];
        
            // Cek jika ada file yang diunggah
            if ($request->hasFile('image')) {
                $uploadedFileUrl = cloudinary()->upload($request->file('image')->getRealPath(), [
                    'folder' => 'image',
                ])->getSecurePath();
        
                $data['image'] = $uploadedFileUrl; // Simpan URL gambar
            }
        
            // Update atau buat profil
            $profile = Profile::updateOrCreate(['user_id' => $user->id], $data);
        
            return response()->json([
                'message' => 'Profil berhasil diperbarui',
                'data' => $profile,
            ], 200);
        } catch (\Throwable $e) { // Gunakan Throwable agar menangani semua jenis error
            Log::error('Error updating profile: ', ['error' => $e]);
        
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengupdate profil',
                'error' => $e->getMessage(), // Bisa dihapus jika tidak ingin menampilkan error ke client
            ], 500);
        }
    }
}
