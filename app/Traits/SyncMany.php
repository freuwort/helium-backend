<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait SyncMany
{
    /**
     * Sync a one-to-many relationship.
     * 
     * @param string $modelClass The class name of the model to sync.
     * @param iterable $data The data to sync.
     * @param string $relation The name of the relationship to sync.
     * @param string $primaryKey The name of the primary key of the model to sync.
     * 
     * @return Model
     */
    public function syncMany(string $modelClass, iterable $data, string $relation = null, string $primaryKey = 'id'): Model
    {
        // Get the relation name
        $relation = $relation ?? Str::plural(strtolower(class_basename($modelClass)));

        $newIds = [];

        foreach ($data as $item)
        {
            // Update or create the model
            $model = $this->$relation()->updateOrCreate([$primaryKey => $item[$primaryKey] ?? null], $item);

            $newIds[] = $model->$primaryKey;
        }

        // Detach models that were not present in the new data
        $this->$relation()->whereNotIn($primaryKey, $newIds)->delete();

        return $this;
    }
}
