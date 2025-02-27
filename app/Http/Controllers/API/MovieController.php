<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movie;

class MovieController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'admin'])->except(['index', 'show']);
    }
    public function index(Request $request)
    {
        $query = Movie::query();

        if ($request->has('search')) {
            $searching = $request->input('search');
            $query->where('name', "LIKE", "%$searching%");
        }

        $per_page = $request->input('per_page', 8);

        $movies = $query->paginate($per_page);

        return response()->json([
            'message' => 'Data movie berhasil ditampilkan semua.',
            'data' => $movies
        ], 200);
        
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'summary' => 'required',
            'poster' => "required|image|mimes:jpg,png,jpeg,gif,svg|max:2048",
            'date' => 'required',
            'genre_id' => 'required|exists:genres,id'
        ]);
        $uploadedFileUrl = cloudinary()->upload($request->file('poster')->getRealPath(), [
            'folder' => 'poster',
        ])->getSecurePath();
            $movie = new Movie;
            $movie->title = $request->input('title');
            $movie ->summary = $request->input('summary');
            $movie -> date = $request->input('date');
            $movie -> genre_id = $request->input('genre_id');
            $movie -> poster = $uploadedFileUrl;
    
            $movie->save();
            return response()->json([
                'message' => 'Berhasil menambahkan data movie',
            ], 200);   
    }

    public function show($id)
    {
        $movie = Movie::with('genre', 'review.user.profile')->find($id);
        if (!$movie) {
            return response()->json([
                'message' => 'Data movie tidak ditemukan',
            ], 404);    
        }
        return response()->json([
            'message' => 'Detail movie berhasil ditampilkan',
            'data' => $movie
        ], 200);
    }

    public function update(Request $request,  $id)
    {
        $request->validate([
            'poster' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'title' => 'required',
            'summary' => 'required',
            'date' => 'required',
            'genre_id' => 'required|exists:genres,id'
        ]);

        $movie = Movie::find($id);
        if ($request->hasFile('poster')) {
            $uploadedFileUrl = cloudinary()->upload($request->file('poster')->getRealPath(), [
                'folder' => 'poster',
            ])->getSecurePath();
            $movie -> poster = $uploadedFileUrl;
        }
       
        if (!$movie) {
            return response()->json([
                'message' => 'Data movie tidak ditemukan',
            ], 404);
        }
            $movie->title = $request->input('title');
            $movie -> summary = $request->input('summary');
            $movie -> date = $request->input('date');
            $movie -> genre_id = $request->input('genre_id');
      
    
            $movie->save();
            return response()->json([
                'message' => 'Berhasil mengupdate data movie',
            ], 200);
        
    }

    public function destroy($id)
    {
        $movie = Movie::find($id);
        $movie->delete();
        return response()->json([
            'message' => 'Berhasil menghapus data movie'
        ], 200);
    }
}
