<?php

namespace SecretBox;

use Illuminate\Database\Eloquent\Model;

class File extends RandomIdModel
{
    // Using a non-incrementing and non-numeric primary key
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = [
        'name',  'type', 'extension', 'user_id', 'group_id', 'url'
    ];

    public function user()
    {
        return $this->belongsTo('SecretBox\User');
    }

    public function group()
    {
        return $this->belongsTo('SecretBox\Group');
    }
}
