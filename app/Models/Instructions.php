<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructions extends Model
{
    use HasFactory;
    protected $fillable = [
        'added_by',
        'instruction',
    ];

    public function added_by()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function appointments()
{
    return $this->belongsToMany(Appoinment::class, 'appoinment_instruction');
}
}