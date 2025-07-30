<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;

/**
 * Resolve models from id
 */
final class ModelResolver
{
    /**
     * Returns null if record not found
     * @param Model|int|string $id - any id
     * @param string $modelClass - the model that is supposed to be resolved to
     * @return Model|null
     */
    public static function resolve(Model|int|string $id, string $modelClass): Model|null
    {
        if($id instanceof $modelClass){
            return $id;
        }
        return $modelClass::find($id);
    }
}
