<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Scout\Searchable;

class Simulation extends Model
{
    use HasFactory;
//    use Searchable;

    protected $fillable = [
        'user_id',
        'category_id',
        'slug',
        'access'
    ];

//    public function toSearchableArray(): array
//    {
//        return [
//            'sname' => $this->version->name,
//            'synopsis' => $this->version->synopsis
//        ];
//    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function version(): HasOne
    {
        return $this->hasOne(SimulationWithVersion::class, 'status_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(SimulationWithVersion::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function liked(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'likes', 'simulation_id', 'user_id');
    }
}
