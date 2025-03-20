<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        static::creating(function ($model) {
            if (!$model->tenant_id && session()->has('tenant_id')) {
                $model->tenant_id = session('tenant_id');
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder) {
            if (session()->has('tenant_id')) {
                $builder->where($builder->getModel()->getTable() . '.tenant_id', session('tenant_id'));
            }
        });
    }
}