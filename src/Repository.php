<?php
/**
 * Project laravel-repository.
 * Created by PhpStorm
 * User: tienhm <tiencntt2@gmail.com.vn>
 * Date: 6/16/22
 * Time: 9:40 AM
 */

namespace tienhm7\Repository;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class Repository
{

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Closure
     */
    protected $scopeQuery = null;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->makeModel();
        $this->boot();
    }

    /**
     * Run after make Model
     */
    public function boot()
    {
        //
    }

    /**
     * Specify model class name
     *
     * @return string
     */
    abstract public function model();

    /**
     * Function make Model
     * @return Model
     * @throws Exception
     */
    public function makeModel()
    {
        $model = app($this->model());

        if (!$model instanceof Model) {
            throw new Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * Returns the current Model instance
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * function reset model
     * @return void
     * @throws Exception
     */
    public function resetModel()
    {
        $this->makeModel();
    }

    /**
     * Query Scope
     *
     * @param Closure $scope
     *
     * @return $this
     */
    public function scopeQuery(\Closure $scope)
    {
        $this->scopeQuery = $scope;

        return $this;
    }

    /**
     * Reset Query Scope
     *
     * @return $this
     */
    public function resetScope()
    {
        $this->scopeQuery = null;

        return $this;
    }

    /**
     * Select by column
     * @param string[] $columns
     *
     * @return mixed
     */
    public function select(array $columns = ['*'])
    {
        return $this->model->select($columns);
    }

    /**
     * Get count model
     *
     * @return mixed
     */
    public function count()
    {
        return $this->select()->count();
    }

    /**
     * Count results of repository with conditions
     *
     * @param array $where
     * @param string $columns
     *
     * @return int
     * @throws Exception
     */
    public function countWhere(array $where = [], $columns = '*')
    {
        $this->applyScope();

        if ($where) {
            $this->applyConditions($where);
        }

        $result = $this->model->count($columns);

        $this->resetModel();

        $this->resetScope();

        return $result;
    }

    /**
     * Get all the results in the model's table
     * @param array $columns
     *
     * @return mixed
     * @throws Exception
     */
    public function all(array $columns = ['*'])
    {
        $this->applyScope();

        if ($this->model instanceof Builder) {
            $results = $this->model->get($columns);
        } else {
            $results = $this->model->all($columns);
        }

        $this->resetModel();

        $this->resetScope();

        return $results;
    }

    /**
     * Alias of All method
     *
     * @param array $columns
     *
     * @return mixed
     * @throws Exception
     */
    public function get($columns = ['*'])
    {
        return $this->all($columns);
    }

    /**
     * Retrieves all the values for a given key
     * @param string $column
     * @param string|null $key
     *
     * @return \Illuminate\Support\Collection|array
     * @throws Exception
     */
    public function pluck(string $column, string $key = null)
    {
        return $this->model->pluck($column, $key);
    }

    /**
     * Find model by id
     * @param $id
     * @param string[] $columns
     * @return mixed
     * @throws Exception
     */
    public function find($id, $columns = ['*'])
    {
        try {
            $this->applyScope();

            $model = $this->model->findOrFail($id, $columns);

            $this->resetModel();

            return $model;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    /**
     * Find data by field and value
     * @param $field
     * @param string $operator
     * @param $value
     * @param array $columns
     * @return mixed
     * @throws Exception
     */
    public function findByField($field, $operator = '=', $value = null, $columns = ['*'])
    {
        $this->applyScope();

        $result = $this->model->where($field, $operator, $value)->get($columns);

        $this->resetModel();

        return $result;
    }

    /**
     * Find model by id include soft deleted model
     * @param $id
     *
     * @return mixed
     * @throws Exception
     */
    public function findTrash($id)
    {
        $result = $this->model->withTrashed()->find($id);

        $this->resetModel();

        return $result;
    }

    /**
     * Find data by multiple fields
     *
     * @param array $where
     * @param array $columns
     * @return mixed
     * @throws Exception
     */
    public function findWhere(array $where, $columns = ['*'])
    {
        $this->applyScope();

        $this->applyConditions($where);

        $result = $this->model->get($columns);

        $this->resetModel();

        return $result;
    }

    /**
     * Find data by multiple values in one field
     *
     * @param $field
     * @param array $values
     * @param array $columns
     * @return mixed
     * @throws Exception
     */
    public function findWhereIn($field, array $values, $columns = ['*'])
    {
        $this->applyScope();

        $result = $this->model->whereIn($field, $values)->get($columns);

        $this->resetModel();

        return $result;
    }

    /**
     * Find data by excluding multiple values in one field
     *
     * @param $field
     * @param array $values
     * @param array $columns
     * @return mixed
     * @throws Exception
     */
    public function findWhereNotIn($field, array $values, $columns = ['*'])
    {
        $this->applyScope();

        $result = $this->model->whereNotIn($field, $values)->get($columns);

        $this->resetModel();

        return $result;
    }

    /**
     * Find data by between values in one field
     *
     * @param $field
     * @param array $values
     * @param array $columns
     *
     * @return mixed
     * @throws Exception
     */
    public function findWhereBetween($field, array $values, $columns = ['*'])
    {
        $this->applyScope();

        $result = $this->model->whereBetween($field, $values)->get($columns);

        $this->resetModel();

        return $result;
    }

    /**
     * Save a new entity in repository
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes)
    {
        DB::beginTransaction();

        try {
            $obj = $this->model->create($attributes);

            $this->resetModel();

            DB::commit();

            return $obj;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return false;
        }
    }

    /**
     * Save many entity in repository
     * @param array $attributes
     * @return mixed
     */
    public function createMany(array $attributes)
    {
        DB::beginTransaction();

        try {
            $obj = $this->model->insert($attributes);

            $this->resetModel();

            DB::commit();

            return $obj;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return false;
        }
    }

    /**
     * Update an entity in repository by id
     *
     * @param $id
     * @param array $attributes
     * @return mixed
     */
    public function update($id, array $attributes)
    {
        DB::beginTransaction();

        try {
            $this->applyScope();

            $obj = $this->model->findOrFail($id);

            $obj->fill($attributes);

            $obj->save();

            $this->resetModel();

            DB::commit();

            return $obj;
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e);

            return false;
        }
    }

    /**
     * Force deletes an entity in repository by id
     *
     * @param $id
     * @return bool|null
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $this->applyScope();

            if ($id instanceof $this->model) {
                $obj = $id;
            } else {
                $obj = $this->find($id);
            }

            if (!$obj) {
                return false;
            }

            $response = $obj->forceDelete();

            $this->resetModel();

            DB::commit();

            return $response;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return false;
        }
    }

    /**
     * Force deletes multiple entities.
     *
     * @param array $where
     *
     * @return int
     * @throws Exception
     */
    public function destroyWhere(array $where)
    {
        DB::beginTransaction();

        try {
            $this->applyScope();

            $this->applyConditions($where);

            $results = $this->model->forceDelete();

            $this->resetModel();

            DB::commit();

            return $results;
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e);

            return false;
        }
    }

    /**
     * Delete an entity in repository by id
     *
     * @param $id
     * @return bool|null
     */
    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $this->applyScope();

            if ($id instanceof $this->model) {
                $obj = $id;
            } else {
                $obj = $this->find($id);
            }

            if (!$obj) {
                return false;
            }

            $response = $obj->delete();

            $this->resetModel();

            DB::commit();

            return $response;
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e);

            return false;
        }
    }

    /**
     * Delete multiple entities.
     *
     * @param array $where
     *
     * @return int
     * @throws Exception
     */
    public function deleteWhere(array $where)
    {
        DB::beginTransaction();

        try {
            $this->applyScope();

            $this->applyConditions($where);

            $results = $this->model->delete();

            $this->resetModel();

            DB::commit();

            return $results;
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e);

            return false;
        }
    }

    /**
     * Get count model or all model instance has been soft deleted
     *
     * @param $count
     * @return mixed
     * @throws Exception
     */
    public function trashed($count = false)
    {
        $query = $this->model->onlyTrashed();

        $result = $count ? $query->count() : $query->get();

        $this->resetModel();

        return $result;
    }

    /**
     * Restore Soft Deleted Models
     *
     * @param $id
     * @return bool
     */
    public function restore($id)
    {
        DB::beginTransaction();

        try {
            if ($id instanceof $this->model) {
                $obj = $id;
            } else {
                $obj = $this->findTrash($id);
            }

            $obj->restore();

            $this->resetModel();

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e);

            return false;
        }
    }

    /**
     * Update or Create an entity in repository
     *
     * @param array $attributes
     * @param array $values
     * @return mixed
     * @throws Exception
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        $result = $this->model->updateOrCreate($attributes, $values);

        $this->resetModel();

        return $result;
    }

    /**
     * Retrieve first data of repository
     *
     * @param array $columns
     *
     * @return mixed
     * @throws Exception
     */
    public function first($columns = ['*'])
    {
        $this->applyScope();

        $results = $this->model->first($columns);

        $this->resetModel();

        return $results;
    }

    /**
     * Retrieve first data of repository, or return new Entity
     *
     * @param array $attributes
     *
     * @return mixed
     * @throws Exception
     */
    public function firstOrNew(array $attributes = [])
    {
        $this->applyScope();

        $result = $this->model->firstOrNew($attributes);

        $this->resetModel();

        return $result;
    }

    /**
     * Retrieve first data of repository, or create new Entity
     *
     * @param array $attributes
     *
     * @return mixed
     * @throws Exception
     */
    public function firstOrCreate(array $attributes = [])
    {
        $this->applyScope();

        $result = $this->model->firstOrCreate($attributes);

        $this->resetModel();

        return $result;
    }

    /**
     * Check if entity has relation
     *
     * @param string $relation
     *
     * @return $this
     */
    public function has($relation)
    {
        $this->model = $this->model->has($relation);

        return $this;
    }

    /**
     * Load relations
     *
     * @param $relations
     * @return $this
     */
    public function with($relations)
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    /**
     * Add sub-select queries to count the relations.
     *
     * @param mixed $relations
     *
     * @return $this
     */
    public function withCount(mixed $relations)
    {
        $this->model = $this->model->withCount($relations);
        return $this;
    }

    /**
     * Sync relations
     *
     * @param      $id
     * @param      $relation
     * @param      $attributes
     * @param bool $detaching
     *
     * @return mixed
     * @throws Exception
     */
    public function sync($id, $relation, $attributes, $detaching = true)
    {
        return $this->find($id)->{$relation}()->sync($attributes, $detaching);
    }

    /**
     * SyncWithoutDetaching
     *
     * @param $id
     * @param $relation
     * @param $attributes
     *
     * @return mixed
     * @throws Exception
     */
    public function syncWithoutDetaching($id, $relation, $attributes)
    {
        return $this->sync($id, $relation, $attributes, false);
    }

    /**
     * Load relation with closure
     *
     * @param string $relation
     * @param Closure $closure
     * @return $this
     */
    public function whereHas($relation, $closure)
    {
        $this->model = $this->model->whereHas($relation, $closure);

        return $this;
    }

    /**
     * Set the "orderBy" value of the query.
     *
     * @param mixed $column
     * @param string $direction
     *
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->model = $this->model->orderBy($column, $direction);

        return $this;
    }

    /**
     * Retrieve data of repository with limit applied
     *
     * @param int $limit
     * @param array $columns
     *
     * @return mixed
     * @throws Exception
     */
    public function limit($limit, $columns = ['*'])
    {
        // Shortcut to all with `limit` applied on query via `take`
        $this->take($limit);

        return $this->all($columns);
    }

    /**
     * Set the "limit" value of the query.
     *
     * @param int $limit
     * @return $this
     */
    public function take($limit)
    {
        // Internally `take` is an alias to `limit`
        $this->model = $this->model->limit($limit);

        return $this;
    }

    /**
     * Set hidden fields
     *
     * @param array $fields
     * @return $this
     */
    public function hidden(array $fields)
    {
        $this->model->setHidden($fields);

        return $this;
    }

    /**
     * Set visible fields
     *
     * @param array $fields
     * @return $this
     */
    public function visible(array $fields)
    {
        $this->model->setVisible($fields);

        return $this;
    }

    /**
     * Retrieve all data of repository, paginated
     * @param null|int $limit
     * @param array $columns
     * @param string $method
     *
     * @return mixed
     */
    public function paginate($limit = null, $columns = ['*'], $method = "paginate")
    {
        $limit = is_null($limit) ? 10 : $limit;
        return $this->model->{$method}($limit, $columns);
    }

    /**
     * Retrieve all data of repository, simple paginated
     *
     * @param null|int $limit
     * @param array $columns
     *
     * @return mixed
     */
    public function simplePaginate($limit = null, $columns = ['*'])
    {
        return $this->paginate($limit, $columns, "simplePaginate");
    }

    /**
     * Trigger static method calls to the model
     *
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($method, $arguments)
    {
        return call_user_func_array([new static(), $method], $arguments);
    }

    /**
     * Trigger method calls to the model
     *
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $this->applyScope();

        return call_user_func_array([$this->model, $method], $arguments);
    }

    /**
     * Applies the given where conditions to the model.
     *
     * @param array $where
     * @return void
     */
    protected function applyConditions(array $where)
    {
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                [$field, $condition, $val] = $value;
                $this->model = $this->model->where($field, $condition, $val);
            } else {
                $this->model = $this->model->where($field, '=', $value);
            }
        }
    }

    /**
     * Apply scope in current Query
     *
     * @return $this
     */
    protected function applyScope()
    {
        if (isset($this->scopeQuery) && is_callable($this->scopeQuery)) {
            $callback = $this->scopeQuery;
            $this->model = $callback($this->model);
        }

        return $this;
    }
}