<?php

namespace App\Repositories;

interface RepositoryInterface
{
    /**
     * Get all records
     */
    public function all(array $columns = ['*']);

    /**
     * Find a record by its ID
     */
    public function find(int $id, array $columns = ['*']);

    /**
     * Create a new record
     */
    public function create(array $data);

    /**
     * Update a record
     */
    public function update(int $id, array $data);

    /**
     * Delete a record
     */
    public function delete(int $id);
}
