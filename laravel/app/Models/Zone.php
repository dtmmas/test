<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zone extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
    ];

    public function collectors()
    {
        return $this->belongsToMany(Collector::class)->withTimestamps();
    }

    public function nodes()
    {
        return $this->hasMany(Node::class);
    }
}
