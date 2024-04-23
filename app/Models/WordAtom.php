<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string word
 */

class WordAtom extends Model
{
    use HasFactory;

    protected $table = "words_atoms";

    public $timestamps = false;
}
