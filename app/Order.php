<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    protected $table = 'order';
    protected $primaryKey = 'id_order';
    public $timestamps = true;

    protected $fillable = [
        'id_reservasi'
    ];

    public function getCreatedAtAttribute(){
        if(!is_null($this->attributes['tgl_order'])){
            return Carbon::parse($this->attributes['tgl_order'])->format('Y-m-d');
        }
    }
}
