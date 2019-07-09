<?php

namespace SecretBox;

use Illuminate\Database\Eloquent\Model;
use SecretBox\Helpers\RandomIdGenerator;

class RandomIdModel extends Model
{
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
           $model->{$model->getKeyName()} = RandomIdGenerator::generate($model);
        });
    }
}
