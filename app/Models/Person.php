<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'persons';

    /**
     * Get the phone associated with the person.
     */
    public function creditcard()
    {
        return $this->hasOne(Creditcard::class);
    }
}
