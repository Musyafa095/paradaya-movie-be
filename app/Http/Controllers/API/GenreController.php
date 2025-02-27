<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Genre;

class GenreController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'admin'])->except(['index', 'show']);
    } 
    public function index()
    {
        $genres = Genre::all();
        return response()->json([
            'message' => 'Berhasil menampilkan data genre',
            'data' => $genres
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
             'name' => 'required|min:2',
        ],[
            'name.required' => 'kolom name harus diisi',
            'name.min' => 'kolom name minimal 2 karakter'
        ]);
        Genre::create([
             'name' => $request->input('name'),
        ]);
        return response()->json(['message' => 'Berhasil menambahkan data genre'], 200);
    }

    public function show($id)

    {
       
        $genre = Genre::with('movie')->find($id);
        
        return response()->json([
            'message' => 'Detail untuk data genre',
            'data' => $genre
        ]);
    }

    public function update(Request $request, $id)
    {
        $genre = Genre::find($id);
        $request->validate([
            'name' => 'required|string',
        ]);
            if(!$genre) {
                return response ()-> json([
                      'message' => 'Data genre tidak di temukan',
                ], 404);

            }
        
        $genre->update($request->all());
        return response()->json([
            'message' => 'Update data genre telah berhasil',
            'data' => $genre
        ]);
    }

    public function destroy($id)
    {
        $genre = Genre::find($id);
        $genre -> delete();
       return response()->json([
        'message' => 'Berhasil menghapus data genre'
       ]);
       
    }
}
