<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class pengguna extends Authenticatable
{
    use Notifiable;

    protected $table      = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    public    $timestamps = true;

    protected $fillable = [
        'id_role',
        'id_kategori',
        'nama_pengguna',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    protected $casts = [
        'id_pengguna' => 'integer',
        'id_role'     => 'integer',
        'id_kategori'   => 'integer',
    ];



    // public function role()
    // {
    //     return $this->belongsTo(Role::class, 'id_role', 'id_role');
    // }

    public function kategori()
    {
        return $this->belongsTo(KategoriUsaha::class, 'id_kategori', 'id_kategori');
    }

    // public function divisi()
    // {
    //     return $this->belongsTo(Divisi::class, 'id_divisi', 'id_divisi');
    // }

    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function getNamaPenggunaAttribute(string $value): string
    {
        return ucwords(strtolower($value));
    }

    /**
     * Filter berdasarkan role 
     */
    public function scopeByRole($query, int $idRole)
    {
        return $query->where('id_role', $idRole);
    }

    /**
     * Filter berdasarkan kategori 
     */
    public function scopeByKategori($query, int $idKategori)
    {
        return $query->where('id_kategori', $idKategori);
    }
}
