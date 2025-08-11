<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Events\IncidentCreated;
use App\Events\IncidentDeleted;
use App\Events\IncidentUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Incident extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Ini adalah daftar kolom yang boleh diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'site_id',
        'title',
        'location',
        'chronology',
        'status',
        'investigation_notes',
        // Kita tidak memasukkan 'asset_name' atau 'serial_number' karena sudah tidak ada di tabel.
    ];

    /**
     * Relasi ke User yang melaporkan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Site tempat insiden terjadi.
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Relasi ke Aset yang terlibat (bisa banyak).
     */
    public function assets()
    {
        return $this->belongsToMany(Asset::class, 'asset_incident');
    }

    /**
     * Relasi ke Lampiran (polimorfik).
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Relasi ke Komentar (polimorfik).
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Relasi ke Problem.
     */
    public function problems()
    {
        return $this->belongsToMany(Problem::class, 'incident_problem');
    }

    protected $dispatchesEvents = [
        'created' => IncidentCreated::class, // Ini sudah ada
    ];

        /**
     * Mendefinisikan relasi ke User yang ditugaskan.
     * Ini adalah method yang hilang.
     */
    public function assignedTo()
    {
        // Satu insiden ditugaskan ke satu user.
        // Kita secara spesifik memberitahu Laravel untuk menggunakan foreign key 'assigned_to_user_id'.
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }


    protected static function boot()
    {
        parent::boot();
        // Saat sebuah insiden akan dibuat, buatkan UUID untuknya
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}