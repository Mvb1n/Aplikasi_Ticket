<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'root_cause_analysis',
        'permanent_solution',
        'status',
    ];

    /**
     * Mendapatkan insiden yang terkait dengan problem ini.
     */
    public function incidents()
    {
        return $this->belongsToMany(Incident::class, 'incident_problem');
    }
}