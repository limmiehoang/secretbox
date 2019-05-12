<?php

namespace SecretBox;

class Group extends RandomIdModel
{
    // Using a non-incrementing and non-numeric primary key
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = [
        'name', 'initial_data', 'identity_key'
    ]; // @TODO: make identity_key not fillable

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
        return $this->hasOne('SecretBox\EncFile');
    }
}