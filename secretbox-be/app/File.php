<?php

namespace SecretBox;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'name',  'type', 'extension', 'user_id', 'group_id', 'url'
    ];


    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
