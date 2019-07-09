<?php
/**
 * Created by PhpStorm.
 * User: limmie
 * Date: 17/04/2019
 * Time: 09:58
 */

namespace SecretBox\Helpers;


class RandomIdGenerator
{
    const NUM_OF_BYTES = 8;

    public static function generate($model = null)
    {
        do {
            $id = static::randomBytes(static::NUM_OF_BYTES);
            $id = bin2hex($id);

            if (!$model)
                return $id;
        } while (!static::isValidId($id, $model));

        return $id;
    }

    /**
     * Randomness is returned as a string of bytes
     *
     * @param $bytes
     * @return string
     */
    public static function randomBytes($bytes)
    {
        return random_bytes($bytes);
    }

    /**
     * Find id in database and return true if there is no duplicate id
     *
     * @param $id
     * @param $model
     * @return bool
     */
    private static function isValidId($id, $model)
    {
        $modelClass = get_class($model);
        $find = call_user_func("\\$modelClass::find", $id);
        return is_null($find);
    }
}
