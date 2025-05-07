<?php

namespace App\Observers;

use App\Actions\AduitLogAction;
use Illuminate\Database\Eloquent\Model;

class EventObserver
{
    public function created(Model $model): void
    {
        $this->logEvent($model, "created");
    }

    public function updated(Model $model): void
    {
        $this->logEvent($model, "updated");
    }

    public function deleted(Model $model): void
    {
        $this->logEvent($model, "deleted");
    }

    public function restored(Model $model): void
    {
        $this->logEvent($model, "restored");
    }

    private function logEvent(Model $model, string $action): void
    {
        $details =
            class_basename($model) . " (ID: {$model->id}) was {$action}.\n";
        $changes = !empty($model->getChanges())
            ? json_encode($model->getChanges())
            : json_encode($model->getAttributes());

        $details .= $changes;

        AduitLogAction::logEvent($details);
    }
}
