<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidade extends Model {
    use HasFactory;
    protected $fillable = ['unid_nome', 'unid_sigla'];
    protected $table = 'unidade';
    public $timestamps = false;
    protected $primaryKey = 'unid_id';

    public function enderecos() {
        return $this->belongsToMany(Endereco::class, 'unidade_endereco', 'unid_id', 'end_id');
    }

    public function lotacoes() {
        return $this->hasMany(Lotacao::class, 'unid_id');
    }
}
