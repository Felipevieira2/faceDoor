<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool create(array $data, mixed $model_selected = null)
 * @method static bool process(int $jobId)
 * @method static \Illuminate\Database\Eloquent\Collection getPending()
 * 
 * @see \App\Services\ControlIdJobService
 */
class ControlIdJobFacada extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'controlid.job';
    }
} 