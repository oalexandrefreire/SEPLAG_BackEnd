<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadeEndereco extends Model {
    use HasFactory;
    protected $fillable = ['unid_id', 'end_id'];
    protected $table = 'unidade_endereco';
    public $timestamps = false;

    public $incrementing = false;
    protected $primaryKey = ['unid_id', 'end_id'];
}
