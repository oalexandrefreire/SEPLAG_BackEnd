<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PessoaEndereco extends Model {
    use HasFactory;
    protected $fillable = ['pes_id', 'end_id'];
    protected $table = 'pessoa_endereco';
    public $timestamps = false;

    public $incrementing = false;
    protected $primaryKey = ['pes_id', 'end_id'];
}
