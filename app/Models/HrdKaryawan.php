<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrdKaryawan extends Model
{
    use HasFactory;

    protected $table = 'hrd_karyawan';

    protected $fillable = [
        'nik',
        'nama_lengkap',
        'alamat',
        'nomor_telepon',
        'email',
        'tanggal_lahir',
        'jenis_kelamin',
        'tgl_masuk',
        'status_karyawan',
        'nomor_ktp',
        'npwp',
        'foto_karyawan',
        'pendidikan_terakhir',
        'nomor_rekening_bank',
        'nama_bank',
        'id_kantor',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'nik', 'nik');
    }

}
