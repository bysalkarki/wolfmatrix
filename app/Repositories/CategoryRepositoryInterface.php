<?php

namespace App\Repositories;

interface CategoryRepositoryInterface
{
    public function find(?int $id = null): mixed;
    public function store(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function delete(int $id): bool;
    public function softdelete(int $id): bool;
    public function restore(int $id): mixed;
}
