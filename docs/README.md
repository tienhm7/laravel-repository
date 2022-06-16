# Document
This package supports using the repository design pattern and quickly creating repositories for Laravel 8 and above
# Usage
## Methods
### tienhm7\Repository\Contracts\RepositoryInterface
- scopeQuery(Closure $scope)
- resetScope()
- select(array $columns = ['*'])
- count()
- countWhere(array $where = [], $columns = '*')
- all(array $columns = ['*'])
- get($columns = ['*'])
- pluck(string $column, string $key = null)
- find($id, $columns = ['*'])
- findByField($field, $operator = '=', $value = null, $columns = ['*'])
- findTrash($id)
- findWhere(array $where, $columns = ['*'])
- findWhereIn($field, array $values, $columns = ['*'])
- findWhereNotIn($field, array $values, $columns = ['*'])
- findWhereBetween($field, array $values, $columns = ['*'])
- create(array $attributes)
- createMany(array $attributes)
- update($id, array $attributes)
- destroy($id)
- destroyWhere(array $where)
- delete($id)
- deleteWhere(array $where)
- trashed($count = false)
- restore($id)
- updateOrCreate(array $attributes, array $values = [])
- first($columns = ['*'])
- firstOrNew(array $attributes = [])
- firstOrCreate(array $attributes = [])
- has($relation)
- with($relations)
- withCount(mixed $relations)
- sync($id, $relation, $attributes, $detaching = true)
- syncWithoutDetaching($id, $relation, $attributes)
- whereHas($relation, $closure)
- orderBy($column, $direction = 'asc')
- limit($limit, $columns = ['*'])
- take($limit)
- hidden(array $fields)
- visible(array $fields)
- paginate($limit = null, $columns = ['*'], $method = "paginate")
- simplePaginate($limit = null, $columns = ['*'])

## Use methods
Example:
```
namespace App\Http\Controllers;

use App\PostRepository;

class PostsController extends Controller {

    /**
     * @var PostRepository
     */
    protected PostRepository $postRepository;

    public function __construct(PostRepository $postRepository){
        $this->postRepository = $postRepository;
    }

    ....
}
```

### Examples use of some methods

Find all results in Repository

```
$posts = $this->postRepository->all();

// or specified columns
$posts = $this->postRepository->all(['title']);
```
Select by column
```
$posts = $this->postRepository->select();
// or specified columns
$posts = $this->postRepository->select(['name','description']);
```
Get count model
```
$posts = $this->postRepository->count();
```
Get count model combine where
```php
$posts = $this->postRepository->countWhere(
    [
        ['status','=', ENABLE]
    ]
);

// or more condition

$posts = $this->postRepository->countWhere(
    [
        ['id', '=', $postId],
        ['status', '=', ENABLE]
    ]
);
```
Find all results

```php
$posts = $this->postRepository->get();

// or specified columns
$posts = $this->postRepository->get(['title']);
```

Find all results in Repository with pagination

```php
$posts = $this->postRepository->paginate($limit = null, $columns = ['*']);
```

Find by result by id

```php
$post = $this->postRepository->find($id);

// or specified columns
$posts = $this->postRepository->find($id, ['title', 'description']);
```

Hiding attributes of the model

```php
$post = $this->postRepository->hidden(['country_id'])->find($id);
```

Showing only specific attributes of the model

```php
$post = $this->postRepository->visible(['id', 'state_id'])->find($id);
```

Loading the Model relationships

```php
$post = $this->postRepository->with(['state'])->find($id);
```

Find by result by field name

```php
$posts = $this->postRepository->findByField('country_id','15');
```

Find by result by multiple fields

```php
$posts = $this->postRepository->findWhere([
    ['id', '=', $postId],
    ['status', '=', ENABLE]
]);
```

Find by result by multiple values in one field

```php
$posts = $this->postRepository->findWhereIn('id', [1,2,3,4,5]);
```

Find by result by excluding multiple values in one field

```php
$posts = $this->postRepository->findWhereNotIn('id', [6,7,8,9,10]);
```

Find all using custom scope

```php
$posts = $this->postRepository->scopeQuery(function($query){
    return $query->orderBy('sort_order','asc');
})->all();
```

Create new entry in Repository

```php
$post = $this->postRepository->create($postData);
```

Update entry in Repository

```php
$post = $this->postRepository->update($id, $postData);
```

Delete entry in Repository

```php
$this->postRepository->delete($id)
```

Delete entry in Repository by multiple fields

```php
$this->postRepository->deleteWhere([
    ['id', '=', $postId],
    ['status', '=', ENABLE]
])
```
In addition, you can learn more in abstract class tienhm7\Repository\Repository