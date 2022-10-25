<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'ip',
        'clave_wifi',
        'reference',
        'node_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
        ->using(ClientProduct::class)
        ->withTimestamps()
        ->withPivot('id','date_instalation','address','reference','amount_installation','advance','status','node_id');
    }

    public function client_products()
    {
        return $this->hasMany(ClientProduct::class);
    }

    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, ClientProduct::class);
    }

    public function invoicesVencidas()
    {
        return Invoice::select('invoices.id')->Where('invoices.status','-1')->WhereRaw('client_product_id IN (SELECT id FROM client_product WHERE client_id = ?)',[$this->id])->count();
    }

    public function HtmlEstatus($activo, $style='text')
    {
        switch ($activo) {
            
            case '1':
                $color = 'success';
                $text = 'Activo';
                break;
                
            case '0':
                $color = 'danger';
                $text = 'Moroso';
                break;

            default:
                $color = 'default';
                $text = 'Indefinido';
                break;
        }
        if ($style=='html') {
            return '<span class="badge bg-'.$color.' rounded">'.$text.'</span>';
        } else {
            return $text;
        }
    }
}
