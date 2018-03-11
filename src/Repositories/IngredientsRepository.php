<?php

namespace InetStudio\Ingredients\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use InetStudio\Ingredients\Contracts\Models\IngredientModelContract;
use InetStudio\Ingredients\Contracts\Repositories\IngredientsRepositoryContract;
use InetStudio\Ingredients\Contracts\Http\Requests\Back\SaveIngredientRequestContract;

/**
 * Class IngredientsRepository.
 */
class IngredientsRepository implements IngredientsRepositoryContract
{
    /**
     * @var IngredientModelContract
     */
    private $model;

    /**
     * IngredientsRepository constructor.
     *
     * @param IngredientModelContract $model
     */
    public function __construct(IngredientModelContract $model)
    {
        $this->model = $model;
    }

    /**
     * Возвращаем объект по id, либо создаем новый.
     *
     * @param int $id
     *
     * @return IngredientModelContract
     */
    public function getItemByID(int $id): IngredientModelContract
    {
        return $this->model::find($id) ?? new $this->model;
    }

    /**
     * Возвращаем объекты по списку id.
     *
     * @param $ids
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getItemsByIDs($ids, bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery()->whereIn('id', (array) $ids);

        if ($returnBuilder) {
            return $builder;
        }

        return $builder->get();
    }

    /**
     * Сохраняем объект.
     *
     * @param SaveIngredientRequestContract $request
     * @param int $id
     *
     * @return IngredientModelContract
     */
    public function save(SaveIngredientRequestContract $request, int $id): IngredientModelContract
    {
        $item = $this->getItemByID($id);

        $item->title = strip_tags($request->get('title'));
        $item->slug = strip_tags($request->get('slug'));
        $item->description = $request->input('description.text');
        $item->content = $request->input('content.text');
        $item->webmaster_id = '';
        $item->status_id = ($request->filled('status_id')) ? $request->get('status_id') : 1;
        $item->publish_date = ($request->filled('publish_date')) ? date('Y-m-d H:i', \DateTime::createFromFormat('!d.m.Y H:i', $request->get('publish_date'))->getTimestamp()) : null;
        $item->save();

        return $item;
    }

    /**
     * Удаляем объект.
     *
     * @param int $id
     *
     * @return bool
     */
    public function destroy($id): ?bool
    {
        return $this->getItemByID($id)->delete();
    }

    /**
     * Ищем объекты.
     *
     * @param string $field
     * @param $value
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function searchItemsByField(string $field, string $value, bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery(['title'])->where($field, 'LIKE', '%'.$value.'%');

        if ($returnBuilder) {
            return $builder;
        }

        return $builder->get();
    }

    /**
     * Получаем все объекты.
     *
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getAllItems(bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery(['created_at', 'updated_at']);

        if ($returnBuilder) {
            return $builder;
        }

        return $builder->get();
    }

    /**
     * Возвращаем объекты, привязанные к материалам.
     *
     * @param Collection $materials
     *
     * @return Collection
     */
    public function getItemsByMaterials(Collection $materials): Collection
    {
        return $materials->map(function ($item) {
            return (method_exists($item, 'ingredients')) ? $item->ingredients : [];
        })->filter()->collapse()->unique('id');
    }

    /**
     * Получаем объекты по slug.
     *
     * @param string $slug
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getItemBySlug(string $slug, bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery([
            'description', 'content', 'status_id', 'publish_date',
        ], [
            'meta', 'media', 'tags', 'products',
        ])->whereSlug($slug);

        if ($returnBuilder) {
            return $builder;
        }

        $item = $builder->first();

        return $item;
    }

    /**
     * Получаем сохраненные объекты пользователя.
     *
     * @param int $userID
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getItemsFavoritedByUser(int $userID, bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery(['publish_date'], ['media', 'tags', 'products', 'counters'])
            ->orderBy('publish_date', 'DESC')
            ->whereFavoritedBy('ingredient', $userID);

        if ($returnBuilder) {
            return $builder;
        }

        $items = $builder->get();

        return $items;
    }

    /**
     * Возвращаем запрос на получение объектов.
     *
     * @param array $extColumns
     * @param array $with
     *
     * @return Builder
     */
    protected function getItemsQuery($extColumns = [], $with = []): Builder
    {
        $defaultColumns = ['id', 'title', 'slug'];

        $relations = [
            'meta' => function ($query) {
                $query->select(['metable_id', 'metable_type', 'key', 'value']);
            },

            'media' => function ($query) {
                $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk']);
            },

            'tags' => function ($query) {
                $query->select(['id', 'name', 'slug']);
            },

            'products' => function ($query) {
                $query->select(['id', 'title', 'brand'])
                    ->with(['media' => function ($query) {
                        $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk']);
                    }, 'links' => function ($linksQuery) {
                        $linksQuery->select(['id', 'product_id', 'link']);
                    }]);
            },

            'status' => function ($query) {
                $query->select(['id', 'name', 'alias']);
            },
        ];

        return $this->model::select(array_merge($defaultColumns, $extColumns))
            ->with(array_intersect_key($relations, array_flip($with)));
    }
}
