<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Pegawai extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $table = 'pegawai';
    protected $primaryKey = 'id_pegawai';
    public $timestamps = true;

    protected $fillable = [
        'nama_pegawai','id_role','no_telp_pegawai','jenis_kelamin','tgl_gabung','tgl_keluar','status_pegawai','email','password'
    ];

    public function getCreatedAtAttribute(){
        if(!is_null($this->attributes['created_at'])){
            return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
        }
    }

    public function getUpdatedAtAttribute(){
        if(!is_null($this->attributes['updated_at'])){
            return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
        }
    }

    public function getTglGabungAttribute(){
        if(!is_null($this->attributes['tgl_gabung'])){
            return Carbon::parse($this->attributes['tgl_gabung'])->format('Y-m-d');
        }
    }

    public function getTglKeluarAttribute(){
        if(!is_null($this->attributes['tgl_keluar'])){
            return Carbon::parse($this->attributes['tgl_keluar'])->format('Y-m-d');
        }
    }

    protected $hidden = [
      'password','remember_token'
    ];
}
