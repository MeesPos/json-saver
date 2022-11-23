<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creditcard extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the person that owns the creditcard.
     */
    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
