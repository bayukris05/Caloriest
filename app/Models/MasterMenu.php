<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterMenu extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'id_menu';

    protected $fillable = [
        'nama_menu',
        'jumlah',
        'jumlah_kalori',
        'id_satuan'
    ];
}
