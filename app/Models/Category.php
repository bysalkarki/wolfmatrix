<?php

namespace App\Models;

use App\Observers\EventObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([EventObserver::class])]
class Category extends Model
{
    protected $fillable = ["name", "parent_id", "path"];

    public function setPath(): void
    {
        if ($this->parent_id) {
            $parent = self::query()->find($this->parent_id);
                $this->path = $parent->path . $this->id . '/';
                return;
        }

        $this->path = '/' . $this->id . '/';
    }

    public function updateDescendantPaths(string $oldPath): void
    {
        $descendants = self::query()
            ->where("path", "like", $oldPath . "%")
            ->get();

        foreach ($descendants as $descendant) {
            $descendant->path = preg_replace(
                "#^" . preg_quote($oldPath, "#") . "#",
                $this->path,
                $descendant->path
            );
            $descendant->save();
        }
    }
}
