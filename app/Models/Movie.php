<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Movie extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'movies';
    protected $fillable = ['title', 'summary', 'poster' , 'genre_id', 'date'];

   public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id');
    }
    public function review()
    {
        return $this->hasMany(Review::class, 'movie_id');
    }

}
