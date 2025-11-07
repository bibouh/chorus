# Creation des resources
- pour creer les migration ou model
```bash 
php artisan make:NomDuModel -ms
```
- les contontroller creer le dans Http/controller/Api/

- les Api sont gerer avec Laravel Json APi 5 Allez sur [Laravel Json Api](https://laraveljsonapi.io/5.x/)

Permet de creer le premier serveur de la version 1 de l'api


```bash
php artisan jsonapi:server v1
```

```bash
php artisan install api
```
pour sanctum les api
```php
<?php

namespace App\JsonApi\V1;

use Illuminate\Support\Facades\Auth;
use LaravelJsonApi\Core\Server\Server as BaseServer;

class Server extends BaseServer
{

    /**
     * The base URI namespace for this server.
     *
     * @var string
     */
    protected string $baseUri = '/api/v1';

    /**
     * Bootstrap the server when it is handling an HTTP request.
     *
     * @return void
     */
    public function serving(): void
    {
        // no-op
        Auth::shouldUse('sanctum');
    }

```

cela crée le schema de la resource exemple post

```bash
php artisan jsonapi:schema posts

```
et on mets données

```php
class PostSchema extends Schema
{

    /**
     * The model the schema corresponds to.
     *
     * @var string
     */
    public static string $model = Post::class;

    /**
     * Get the resource fields.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
               ID::make(),
            Str::make('content'),
             DateTime::make('createdAt')->sortable()->readOnly(),
            DateTime::make('publishedAt')->sortable(),
            Str::make('slug'),
            Str::make('title')->sortable(),
             DateTime::make('updatedAt')->sortable()->readOnly(),
        ];
    }

    /**
     * Get the resource filters.
     *
     * @return array
     */
    public function filters(): array
    {
        return [
            WhereIdIn::make($this),
        ];
    }

    /**
     * Get the resource paginator.
     *
     * @return Paginator|null
     */
    public function pagination(): ?Paginator
    {
        return PagePagination::make();
    }

}
```


apres vient les request des resources

```bash
php artisan jsonapi:request posts
```

```php
class PostRequest extends ResourceRequest
{

    /**
     * Get the validation rules for the resource.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
        // @TODO
        'content' => ['required', 'string'],        'publishedAt' => ['nullable', JsonApiRule::dateTime()],
        'slug' => ['required', 'string', Rule::unique('posts', 'slug')],
        'tags' => JsonApiRule::toMany(),
        'title' => ['required', 'string'],
        ];
    }

}
```

```bash
php artisan jsonapi:controller Api/V1/PostController
```

```php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use LaravelJsonApi\Laravel\Http\Controllers\Actions;

class PostController extends Controller
{

    use Actions\FetchMany;
    use Actions\FetchOne;
    use Actions\Store;
    use Actions\Update;
    use Actions\Destroy;
    use Actions\FetchRelated;
    use Actions\FetchRelationship;
    use Actions\UpdateRelationship;
    use Actions\AttachRelationship;
    use Actions\DetachRelationship;

}
```
pour les controller custom 
```php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use LaravelJsonApi\Laravel\Http\Controllers\Actions;

class PostController extends Controller
{

    // ...

    /**
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function purge(): \Illuminate\Http\Response
    {
        $this->authorize('deleteAll', Post::class);

        Post::query()->delete();

        return response('', 204);
    }

}
```

dans la route 

```php
JsonApiRoute::server('v1')
    ->prefix('v1')
    ->resources(function (ResourceRegistrar $server) {
        $server->resource('posts', PostController::class)
            ->relationships(function (Relationships $relationships) {
                $relationships->hasOne('author')->readOnly();
                $relationships->hasMany('tags');
            })->actions('-actions', function (ActionRegistrar $actions) {
                $actions->delete('purge');
            });

        // ...other resources
    });
```
pour les filtres

```bash
php artisan jsonapi:query posts --collection
```

```php
 namespace App\JsonApi\V1\Posts;

 use LaravelJsonApi\Laravel\Http\Requests\ResourceQuery;
 use LaravelJsonApi\Validation\Rule as JsonApiRule;

 class PostCollectionQuery extends ResourceQuery
 {

     /**
      * Get the validation rules that apply to the request query parameters.
      *
      * @return array
      */
     public function rules(): array
     {
         return [
             'fields' => [
                 'nullable',
                 'array',
                 JsonApiRule::fieldSets(),
             ],
             'filter' => [
                 'nullable',
                 'array',
                 JsonApiRule::filter(),
             ],
+            'filter.author' => 'array',
+            'filter.author.*' => 'integer',
+            'filter.id' => 'array',
+            'filter.id.*' => 'integer',
             'include' => [
                 'nullable',
                 'string',
                 JsonApiRule::includePaths(),
             ],
             'page' => [
                 'nullable',
                 'array',
                 JsonApiRule::page(),
             ],
+            'page.number' => ['integer', 'min:1'],
+            'page.size' => ['integer', 'between:1,100'],
             'sort' => [
                 'nullable',
                 'string',
                 JsonApiRule::sort(),
             ],
             'withCount' => [
                 'nullable',
                 'string',
                 JsonApiRule::countable(),
             ],
         ];
     }
 }
```

```bash
php artisan jsonapi:resource posts --server=v1

```

```php
namespace App\JsonApi\V1\Posts;

use LaravelJsonApi\Core\Resources\JsonApiResource;

class PostResource extends JsonApiResource
{

    /**
     * Get the resource's attributes.
     *
     * @param \Illuminate\Http\Request|null $request
     * @return iterable
     */
    public function attributes($request): iterable
    {
        return [
            'content' => $this->content,
            'createdAt' => $this->created_at,
            'slug' => $this->slug,
            'synopsis' => $this->synopsis,
            'title' => $this->title,
            'updatedAt' => $this->updated_at,
        ];
    }

    /**
     * Get the resource's relationships.
     *
     * @param \Illuminate\Http\Request|null $request
     * @return iterable
     */
    public function relationships($request): iterable
    {
        return [
            $this->relation('author'),
            $this->relation('comments'),
            $this->relation('tags'),
        ];
    }

}
```
- les services dans le app/services comme notification push etc

- les images gerer avec spatie media library

## images et medias
- les permissions avec SPatie role permissions


