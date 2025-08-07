<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Import Str helper

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'slug', 'content', 'status'];

    /**
     * Boot the model.
     * Ini akan berjalan secara otomatis saat model digunakan.
     */
    protected static function boot()
    {
        parent::boot();

        // Saat sebuah artikel akan dibuat atau diperbarui, buat slug-nya secara otomatis
        static::saving(function ($article) {
            $article->slug = Str::slug($article->title);
        });
    }

    /**
     * Mendefinisikan relasi bahwa satu artikel dimiliki oleh satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
