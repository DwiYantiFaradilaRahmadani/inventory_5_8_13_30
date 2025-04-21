<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'category_id',
        'Nama_Barang',
        'Stock',
        'Satuan'

];

    public function category()
{
    return $this->belongsTo(Category::class, 'category_id');
}
}
