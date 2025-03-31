<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cidade extends Model {
    use HasFactory;
    protected $fillable = ['cid_nome', 'cid_uf'];
    protected $table = 'cidade';
    public $timestamps = false;
    public $primaryKey = 'cid_id';

    public function enderecos() {
        return $this->hasMany(Endereco::class, 'cid_id');
    }
}
