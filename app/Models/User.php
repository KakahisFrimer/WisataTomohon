<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email_or_phone',
        'password',
        'role',
        'security_question_id', // tambahkan ini
        'security_answer', // tambahkan ini
        'password_reset_token'
    ];

    


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];



    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function findForPassport($identifier)
    {
        return $this->where('email_or_phone', $identifier)->first();
    }

public function getInitialsAttribute()
{
    $name = trim($this->name);
    
    // Jika nama kosong, kembalikan string kosong
    if (empty($name)) {
        return '';
    }
    
    // Pisahkan nama menjadi kata-kata, dengan penanganan spasi ganda
    $words = preg_split('/\s+/', $name);
    $wordCount = count($words);
    
    // Ambil inisial kata pertama
    $firstInitial = mb_substr($words[0], 0, 1, 'UTF-8');
    
    // Ambil inisial kata terakhir (jika ada lebih dari satu kata)
    $lastInitial = ($wordCount > 1) ? mb_substr($words[$wordCount - 1], 0, 1, 'UTF-8') : '';
    
    // Gabungkan dan jadikan huruf besar
    return mb_strtoupper($firstInitial . $lastInitial, 'UTF-8');
}

public function getAvatarColorAttribute()
{
    // Generate warna acak berdasarkan inisial
    $initials = $this->initials;
    $colors = [
        '#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6',
        '#1abc9c', '#d35400', '#c0392b', '#16a085', '#8e44ad'
    ];
    
    // Pilih warna berdasarkan jumlah karakter inisial
    $index = ord($initials[0]) % count($colors);
    return $colors[$index];
}

public function reviews()
{
    return $this->hasMany(\App\Models\Review::class);
}


}
