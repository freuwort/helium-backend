<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Builder::macro('wherePolymorphic', function ($relation, Model|null $model = null) {
            if (is_null($model)) {
                return $this
                ->whereNull("{$relation}_id")
                ->whereNull("{$relation}_type");
            }

            return $this
            ->where("{$relation}_id", $model->getKey())
            ->where("{$relation}_type", get_class($model));
        });



        Builder::macro('wherePolymorphicNull', function ($relation) {
            return $this->wherePolymorphic($relation, null);
        });

        Builder::macro('orWherePolymorphic', function ($relation, Model $model) {
            return $this->orWhere(function ($query) use ($relation, $model) {
                $query->wherePolymorphic($relation, $model);
            });
        });

        Builder::macro('orWherePolymorphicNull', function ($relation) {
            return $this->orWhere(function ($query) use ($relation) {
                $query->wherePolymorphicNull($relation);
            });
        });

        Builder::macro('wherePolymorphicIn', function ($relation, $models) {
            return $this->where(function ($query) use ($relation, $models) {
                foreach ($models as $model) {
                    $query->orWherePolymorphic($relation, $model);
                }
            });
        });

        Builder::macro('orWherePolymorphicIn', function ($relation, $models) {
            return $this->orWhere(function ($query) use ($relation, $models) {
                foreach ($models as $model) {
                    $query->orWherePolymorphic($relation, $model);
                }
            });
        });
    }
}
