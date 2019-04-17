<?php

namespace SecretBox;

use Illuminate\Database\Eloquent\Model;

class Group extends RandomIdModel
{
    // Using a non-incrementing and non-numeric primary key
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = [
        'name', 'initial_data'
    ];

    public function files()
    {
        return $this->hasMany('SecretBox\File');
    }

    public function encFiles()
    {
        return $this->hasMany('SecretBox\EncFile');
    }

    public function users()
    {
        return $this->belongsToMany('SecretBox\User');
    }

    public function initialUser()
    {
        return $this->belongsTo('SecretBox\User');
    }

    public function initialData()
    {
        return $this->belongsTo('SecretBox\EncFile');
    }
}