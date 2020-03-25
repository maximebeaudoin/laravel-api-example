<?php

namespace App\Core\Repository\Contract;

use App\Core\Entity\Entity;

/**
 * Interface Repository
 * @package App\Core\Repository\Contract
 */
interface Repository
{
    /**
     * Make a new instance of the entity to query on
     *
     * @param array $with
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder|Entity
     */
    public function make(array $with = []);

    /**
     * Retrieve all entities
     *
     * @param array $with
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $with = []);

    /**
     * Find a single entity
     *
     * @param int $id
     * @param array $with
     * @return Entity
     */
    public function find($id, array $with = []);

    /**
     * Delete an existing entity
     *
     * @param Entity $entity
     * @return Entity
     */
    public function delete(Entity $entity);

    /**
     * Save the entity
     *
     * @param Entity $entity
     * @return Entity
     */
    public function save(Entity $entity);

    /**
     * Create a new entity
     *
     * @param array $input
     * @return Entity
     */
    public function create(array $input);

    /**
     * Update an existing entity
     *
     * @param Entity $entity
     * @param array $input
     * @return Entity
     */
    public function update(Entity $entity, array $input);
}
