<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(public Category $model) {}
    public function find(?int $id = null): Collection|Model|null
    {
        if ($id) {
            return $this->model->findOrFail($id);
        }
        return $this->model->all();
    }

    public function store(array $data): Category
    {
        $category = $this->model->create($data);
        $category->setPath();
        $category->save();
        return $category;
    }

    public function update(int $id, array $data): Category
    {
        $category = $this->model->findOrFail($id);
        $category->update($data);
        $oldPath = $category->path;
        $category->setPath();
        $category->updateDescendantPaths($oldPath);
        return $category;
    }

    public function delete(int $id): bool
    {
        $record = $this->model->findOrFail($id);
        return $record->delete();
    }

    public function softdelete(int $id): bool
    {
        $record = $this->model->findOrFail($id);
        return $record->delete();
    }

    public function restore(int $id): Category
    {
        $record = $this->model->withTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
