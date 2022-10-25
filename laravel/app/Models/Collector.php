<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Collector extends User
{
    use HasFactory;

    public function zones()
    {
        return $this->belongsToMany(Zone::class)->withTimestamps();
    }

    public function nodes(){
        return $this->hasManyThrough(Node::class, Zone::class);
    }

    public function clientesArray()
    {
        return $this->zones()
        ->has('nodes.client_products.client')
        ->with('nodes.client_products.client.user')
        ->get()
        ->pluck('nodes')->collapse()
        ->pluck('client_products')->collapse()
        ->pluck('client.id')->unique()->values()->toArray();
    }

    public function clientProductsArray()
    {
        return $this->zones()
        ->has('nodes.client_products.client')
        ->with('nodes.client_products.client.user')
        ->get()
        ->pluck('nodes')->collapse()
        ->pluck('client_products')->collapse()
        ->pluck('id')->unique()->values()->toArray();
    }
}
