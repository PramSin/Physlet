<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SimulationWithVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'simulation_id',
        'status_id',
        'name',
        'slug',
        'root_path',
        'synopsis',
    ];

    protected $hidden = [
        'root_path'
    ];

    public function simulation(): BelongsTo
    {
        return $this->belongsTo(Simulation::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Simulation::class, 'status_id');
    }

    /*    public function projects()
        {

        }*/

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
