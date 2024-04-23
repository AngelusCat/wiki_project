<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int article_id
 * @property int word_id
 * @property int number_of_occurrences
 */

class Communication extends Model
{
    use HasFactory;

    public $timestamps = false;
}
