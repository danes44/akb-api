<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailOrder extends Model
{
    protected $table = 'detail_order';
    protected $primaryKey = 'id_detail';
    public $timestamps = false;

    protected $fillable = [
        'jumlah_order','harga_jumlah','status_order','id_order', 'id_menu', 'waktu_order'
    ];

    public function getCreatedAtAttribute(){
        if(!is_null($this->attributes['waktu_order'])){
            return Carbon::parse($this->attributes['waktu_order'])->format('H:i:s');
        }
    }
}
