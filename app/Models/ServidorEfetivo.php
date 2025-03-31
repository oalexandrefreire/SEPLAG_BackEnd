<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServidorEfetivo extends Model {
    use HasFactory;
    protected $fillable = ['pes_id', 'se_matricula'];
    protected $table = 'servidor_efetivo';
    public $timestamps = false;
    protected $primaryKey = 'pes_id';
    public $incrementing = false;

    public function pessoa() {
        return $this->belongsTo(Pessoa::class, 'pes_id');
    }
}
