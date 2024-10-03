<?php

namespace App\Traits;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use InvalidArgumentException;
use App\Models\User;

trait CRUDTrait
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     *
     * @param Model $model
     * @param Request $request
     * @param string $with
     * @return JsonResponse
     */
    public function index_data(Model $model, Request $request, string $with = ''): JsonResponse
    {
        try {
            $rules = [
                'page' => 'integer|min:1',
                'per_page' => 'integer|min:1',
                'all_data' => 'integer|in:1,0'
            ];
            $data = [
                'page' => $request->get('page', 1),
                'per_page' => $request->get('per_page', 20),
                'all_data' => $request->get('all_data', 0)
            ];
            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }
            if (!$model instanceof DatabaseNotification) {
                $this->authorizeForModel($model, 'viewAny');
            }
            $relations = [];
            if ($with) {
                $relations = $this->parseRelations($with);
                $this->validateRelations($model, $relations);
            }
            $withCount = [];
            if ($request->get('withCount', false)) {
                $withCount = $this->parseRelations($request->get('withCount', ''));
                $this->validateRelations($model, $withCount);
            }
            $table = $model->getTable();
            [$keys, $customKeys] = $this->extractKeys($request);

            $orderBy = $request->get('orderBy');
            $orderDirection = $this->validateOrderDirection($request->get('dir', 'asc'));

            $page = $request->get('page', 1) ?: 1;
            $perPage = $request->get('per_page', 20) ?: 20;
            $allData = $request->get('all_data', 0);
            $search = $request->get('search');
            $query = $model->newQuery();

            return $this->filterAndOrder($query, $table, $keys, $orderBy, $orderDirection, $relations, $customKeys, $withCount, $page, $perPage, $allData, $search);
        } catch (AuthorizationException $e) {
            return $this->apiResponse(null, 403, 'Error: ' . $e->getMessage());
        } catch (InvalidArgumentException | Exception $e) {
            return $this->apiResponse(null, 400, 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Authorize the user for the given model and ability.
     *
     * @param Model $model
     * @param string $ability
     * @return void
     * @throws AuthorizationException
     */
    protected function authorizeForModel(Model $model, string $ability): void
    {
        $this->authorize($ability, $model);
    }

    /**
     * Parse the given "with" string into an array of relations.
     *
     * @param string $with
     * @return array
     */
    protected function parseRelations(string $with): array
    {
        return empty($with) ? [] : explode(',', $with);
    }

    /**
     * Validate the given relations against the model's available relations.
     *
     * @param Model $model
     * @param array $relations
     * @return void
     * @throws InvalidArgumentException
     */
    protected function validateRelations(Model $model, array $relations): void
    {
        $modelRelations = $model->getRelations();
        $invalidRelations = array_diff($relations, $modelRelations);

        if (!empty($invalidRelations)) {
            $invalidRelationsStr = implode(', ', $invalidRelations);
            throw new InvalidArgumentException("Invalid relations: $invalidRelationsStr");
        }
    }

    /**
     * Extract the keys and custom keys from the request.
     *
     * @param Request $request
     * @return array
     */
    protected function extractKeys(Request $request): array
    {
        $keys = $request->except(['orderBy', 'dir', 'with', 'withCount', 'page', 'per_page', 'all_data', 'search']);
        $customKeys = [];

        foreach ($keys as $key => $value) {
            if (str_contains($key, '*')) {
                $customKeys[str_replace('*', '.', $key)] = $value;
                unset($keys[$key]);
            }
        }

        return [$keys, $customKeys];
    }

    /**
     * Validate the order direction.
     *
     * @param string $orderDirection
     * @return string
     */
    protected function validateOrderDirection(string $orderDirection): string
    {
        return in_array($orderDirection, ['asc', 'desc']) ? $orderDirection : 'asc';
    }

    /**
     * Filter and order the query based on the given parameters.
     *
     * @param Builder $query
     * @param string $table
     * @param array $keys
     * @param string|null $orderBy
     * @param string $orderDirection
     * @param array $relations
     * @param array $customKeys
     * @param array $withCount
     * @param int $page
     * @param int $perPage
     * @param int $allData
     * @return JsonResponse
     */
    protected function filterAndOrder(Builder $query, string $table, array $keys, ?string $orderBy, string $orderDirection, array $relations, array $customKeys, array $withCount, int $page, int $perPage, int $allData, string|null $search): JsonResponse
    {
        $missingColumns = [];

        foreach ($keys as $key => $value) {
            $operator = Str::substr($key, -1);
            if ($operator === '!') {
                $key = Str::substr($key, 0, -1);
                if ($this->validateColumn($table, $key)) {
                    $query->where($key, '!=', $value);
                } else {
                    $missingColumns[] = $key;
                }
            } elseif ($operator === '>') {
                $key = Str::substr($key, 0, -1);
                if ($this->validateColumn($table, $key)) {
                    $query->where($key, 'LIKE', '%' . $value . '%');
                } else {
                    $missingColumns[] = $key;
                }
            } elseif ($this->validateColumn($table, $key)) {
                $query->where($key, $value);
            } else {
                $missingColumns[] = $key;
            }
        }

        if (!empty($missingColumns)) {
            return $this->apiResponse(null, 422, 'Missing columns: ' . implode(', ', $missingColumns));
        }

        return $this->handleOrdering($query, $table, $orderBy, $orderDirection, $relations, $customKeys, $withCount, $page, $perPage, $allData, $search);
    }

    /**
     *  Validate the given column against the table schema.
     *
     * @param string $table
     * @param string $column
     * @return bool
     */
    protected function validateColumn(string $table, string $column): bool
    {
        if (!Schema::hasTable($table)) {
            $table .= 's';
        }
        return Schema::hasColumn($table, $column);
    }

    /**
     * Handle the ordering of the query based on the given parameters.
     *
     * @param Builder $query
     * @param string $table
     * @param string|null $orderBy
     * @param string $orderDirection
     * @param array $relations
     * @param array $customKeys
     * @param array $withCount
     * @param int $page
     * @param int $perPage
     * @param int $allData
     * @return JsonResponse
     */
    protected function handleOrdering(Builder $query, string $table, ?string $orderBy, string $orderDirection, array $relations, array $customKeys, array $withCount, int $page, int $perPage, int $allData, ?string $search): JsonResponse
    {
        if ($orderBy && $this->validateColumn($table, $orderBy)) {
            $query->orderBy($orderBy, $orderDirection);
        } else {
            $query->orderBy('id', $orderDirection);
        }

        foreach ($customKeys as $key => $value) {
            [$relation, $column, $operator] = $this->parseCustomKey($key);
            if (empty($relation) || empty($column) || empty($operator)) {
                throw new InvalidArgumentException("Invalid custom key format");
            }
            if (!$this->validateColumn($relation, $column)) {
                throw new InvalidArgumentException('Invalid column: ' . $column);
            }
            $query->whereHas($relation, function ($q) use ($column, $value, $operator) {
                $q->where($column, $operator === '!' ? '!=' : '=', $value);
            });
        }

        $query->with($relations);
        if ($search) {
            $user = auth()->user();
            $role = $user->rule->name;
            if (method_exists($query->getModel(), 'getSearchAbleColumns')) {
                $searchColumns = $query->getModel()->getSearchAbleColumns();
                $query->where(function ($query) use ($search, $searchColumns, $role) {
                    foreach ($searchColumns as $searchColumn) {
                        if (str_contains($searchColumn, '.')) {
                            list ($relatedSearch, $relatedColumn) = explode('.', $searchColumn);
                            $query->orWhereHas($relatedSearch, function ($subQuery) use ($relatedColumn, $search, $role) {
                                $subQuery->where($relatedColumn, 'LIKE', '%' . $search . '%');
                            });
                        } else {
                            $query->orWhere($searchColumn, 'LIKE', '%' . $search . '%');
                        }
                    }
                });
            }
        }
        if ($allData == 1) {
            if ($query->getModel() instanceof User && in_array('devices', $withCount)) {
                $data = $query->withCount([
                    'devices' => function ($query) {
                        $query->where('deliver_to_client', false);
                    }
                ])->get();
            } else {
                $data = $query->withCount($withCount)->get();
            }
            return $this->apiResponse($data);
        }

        $data = $query->withCount($withCount)->paginate($perPage, page: $page);
        $pagination = [
            'current_page' => $data->currentPage(),
            'first_page_url' => $data->url(1),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
            'last_page' => $data->lastPage(),
            'next_page_url' => $data->nextPageUrl(),
            'per_page' => $data->perPage(),
            'prev_page_url' => $data->previousPageUrl(),
            'total' => $data->total()
        ];
        return $this->apiResponse(body: $data->items(), pagination: $pagination);
    }

    /**
     * Parse the custom keys.
     *
     * @param string $customKey
     * @return array|null
     */
    protected function parseCustomKey(string $customKey): array|null
    {
        $parts = explode('.', $customKey);
        if (count($parts) !== 2) {
            return null;
        }

        $column = array_pop($parts);
        $operator = Str::substr($column, -1) === '!' ? '!' : '=';
        $column = $operator === '!' ? Str::substr($column, 0, -1) : $column;

        return [$parts[0], $column, $operator];
    }

    /**
     * Show the specified resource.
     *
     * @param Model $model
     * @param int $id
     * @param string $with
     * @return JsonResponse
     */
    public function show_data(Model $model, $id, string $with = ''): JsonResponse
    {
        try {
            $object = $model->findOrFail($id);
            $this->authorizeForModel($model->find($id), 'view');
            $relations = $this->parseRelations($with);
            $this->validateRelations($object, $relations);

            $data = $object->load($relations);
            return $this->apiResponse($data);
        } catch (AuthorizationException $e) {
            return $this->apiResponse(null, 403, 'Error: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            $exceptionModel = explode('\\', $e->getModel());
            $exceptionModel = end($exceptionModel);
            return $this->apiResponse(null, 404, "Error: $exceptionModel with ID $id not found.");
        } catch (InvalidArgumentException | Exception $e) {
            return $this->apiResponse(null, 400, 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param Model $model
     * @return JsonResponse
     */
    public function store_data(Request $request, Model $model): JsonResponse
    {
        try {
            $this->authorizeForModel($model, 'create');
            return $this->apiResponse($model::create($request->all()), 201, 'Add successful');
        } catch (AuthorizationException $e) {
            return $this->apiResponse(null, 403, 'Error: ' . $e->getMessage());
        } catch (Exception $e) {
            return $this->apiResponse(null, 400, 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @param Model $model
     * @return JsonResponse
     */
    public function update_data(Request $request, $id, Model $model): JsonResponse
    {
        try {
            $object = $model->findOrFail($id);
            $this->authorizeForModel($model->find($id), 'update');
            $columns = $request->keys();
            foreach ($columns as $column) {
                if ($column !== 'password' && $column !== 'client_priority' && $this->validateColumn($model->getTable(), $column)) {
                    $object->$column = $request[$column];
                }
            }
            $object->save();
            return $this->apiResponse($object, 200, 'Update successful');
        } catch (AuthorizationException $e) {
            return $this->apiResponse(null, 403, 'Error: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            $exceptionModel = explode('\\', $e->getModel());
            $exceptionModel = end($exceptionModel);
            return $this->apiResponse(null, 404, "Error: $exceptionModel with ID $id not found.");
        } catch (InvalidArgumentException | Exception $e) {
            return $this->apiResponse(null, 400, 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @param Model $model
     * @return JsonResponse
     */
    public function destroy_data(int $id, Model $model): JsonResponse
    {
        try {
            $this->authorizeForModel($model, 'delete');
            $object = $model->findOrFail($id);
            $object->delete();
            return $this->apiResponse('Delete successful');
        } catch (AuthorizationException $e) {
            return $this->apiResponse(null, 403, 'Error: ' . $e->getMessage());
        } catch (ModelNotFoundException $e) {
            $exceptionModel = explode('\\', $e->getModel());
            $exceptionModel = end($exceptionModel);
            return $this->apiResponse(null, 404, "Error: $exceptionModel with ID $id not found.");
        }
    }
}
