<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Simulation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'slug',
        'access'
    ];

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
}
