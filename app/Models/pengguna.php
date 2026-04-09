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
        'id_divisi',
        'nama_pengguna',
        'email',
        'password',
        'Jenis_akun',
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
        'id_divisi'   => 'integer',
        'Jenis_akun'  => 'string',
    ];

    const JENIS_PERUSAHAAN = 'Perusahaan';
    const JENIS_PRIBADI    = 'Pribadi';

    // public function role()
    // {
    //     return $this->belongsTo(Role::class, 'id_role', 'id_role');
    // }

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

       public function scopePerusahaan($query)
    {
        return $query->where('Jenis_akun', self::JENIS_PERUSAHAAN);
    }

    /**
     * Filter hanya akun Pribadi
     */
    public function scopePribadi($query)
    {
        return $query->where('Jenis_akun', self::JENIS_PRIBADI);
    }

    /**
     * Filter berdasarkan role 
     */
    public function scopeByRole($query, int $idRole)
    {
        return $query->where('id_role', $idRole);
    }

    /**
     * Filter berdasarkan divisi 
     */
    public function scopeByDivisi($query, int $idDivisi)
    {
        return $query->where('id_divisi', $idDivisi);
    }
}
