<?php

namespace SecretBox;

use Illuminate\Database\Eloquent\Model;

class EncFile extends Model
{
    // Using a non-incrementing and non-numeric primary key
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;


    protected $fillable = [
        'group_id', 'enc_metadata'
    ];

    public function group()
    {
        return $this->belongsTo('SecretBox\Group');
    }
}
