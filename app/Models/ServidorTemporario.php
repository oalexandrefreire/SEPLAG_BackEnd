<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServidorTemporario extends Model {
    use HasFactory;
    protected $fillable = ['pes_id', 'st_data_admissao', 'st_data_demissao'];
    protected $table = 'servidor_temporario';
    public $timestamps = false;

    public $incrementing = false;
    protected $primaryKey = 'pes_id';

    public function pessoa() {
        return $this->belongsTo(Pessoa::class, 'pes_id');
    }
}
