<?php

namespace App\Core\Repository;

use App\Core\Entity\Entity;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Repository
 * @package App\Core\Repository
 */
abstract class Repository
{
    /**
     * @var Entity
     */
    protected $entity;

    /**
     * @var \Illuminate\Database\DatabaseManager
     */
    protected $db;

    /**
     * @param Entity $entity
     * @param DatabaseManager $db
     */
    public function __construct(Entity $entity, DatabaseManager $db)
    {
        $this->entity = $entity;
        $this->db = $db;
    }

    /**
     * Make a new instance of the entity to query on
     *
     * @param array $with
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder|Entity
     */
    public function make(array $with = [])
    {
        return $this->entity->with($with);
    }

    /**
     * Retrieve all entities
     *
     * @param array $with
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $with = [])
    {
        return $this->make($with)
            ->get();
    }

    /**
     * Find a single entity
     *
     * @param int $id
     * @param array $with
     * @return Entity
     */
    public function find($id, array $with = [])
    {
        return $this->make($with)->findOrFail($id);
    }

    /**
     * Delete an existing entity
     *
     * @param Entity $entity
     * @return Entity
     */
    public function delete(Entity $entity)
    {
        $entity->delete();

        return $entity;
    }

    /**
     * Save the entity
     *
     * @param Entity $entity
     * @return Entity
     */
    public function save(Entity $entity)
    {
        $entity->save();

        return $entity;
    }

    /**
     * Create a new entity
     *
     * @param array $input
     * @return Entity
     */
    public function create(array $input)
    {
        return $this->entity->create($input);
    }

    /**
     * Update an existing entity
     *
     * @param Entity $entity
     * @param array $input
     * @return Entity
     */
    public function update(Entity $entity, array $input)
    {
        // Sync original to prevent modified attribute, from the model, to be saved,
        // we only want to update the value of input
        $entity->syncOriginal();

        $entity->update($input);

        return $entity;
    }
}
