<?php

namespace App\Traits;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

trait Loggable
{
    protected static function bootLoggable()
    {
        static::created(function ($model) {
            $model->logChange('CREATE');
        });

        static::updated(function ($model) {
            $model->logChange('UPDATE');
        });

        static::deleted(function ($model) {
            $model->logChange('DELETE');
        });
    }

    protected function logChange($action)
    {
        $model = class_basename($this);

        if (app()->runningInConsole() && !app()->runningUnitTests()) {
            return;
        }

        if ($model === 'User' && $action === 'CREATE' && $this->id === 1) {
            return;
        }

        Log::create([
            'user_id' => Auth::id(),
            'model_id' => $this->id,
            'action' => $action,
            'model' => $model,
            'changes' => $action === 'UPDATE' ? json_encode($this->getChanges()) : null,
        ]);
    }
}
