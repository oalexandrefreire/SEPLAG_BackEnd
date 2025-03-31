<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pessoa extends Model {
    use HasFactory;
    protected $fillable = ['pes_nome', 'pes_data_nascimento', 'pes_sexo', 'pes_mae', 'pes_pai'];
    protected $table = 'pessoa';
    protected $primaryKey = 'pes_id';
    public $timestamps = false;

    public function enderecos() {
        return $this->belongsToMany(Endereco::class, 'pessoa_endereco', 'pes_id', 'end_id');
    }

    public function fotos() {
        return $this->hasMany(FotoPessoa::class, 'pes_id');
    }

    public function servidorTemporario() {
        return $this->hasOne(ServidorTemporario::class, 'pes_id');
    }

    public function servidorEfetivo() {
        return $this->hasOne(ServidorEfetivo::class, 'pes_id');
    }

    public function lotacoes() {
        return $this->hasMany(Lotacao::class, 'pes_id');
    }
}
