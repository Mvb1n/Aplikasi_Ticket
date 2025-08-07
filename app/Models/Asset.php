<?php

namespace App\Models;

use App\Events\AssetDeletedInApp1;
use App\Events\AssetUpdatedInApp1;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asset extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'serial_number',
        'description',
        'purchase_date',
        'status',
        'site_id',
        'category',
    ];

    public function incidents() 
    {
        return $this->belongsToMany(Incident::class, 'asset_incident');
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    protected $dispatchesEvents = [
    'updated' => AssetUpdatedInApp1::class,
    'deleted' => AssetDeletedInApp1::class,
];
}
