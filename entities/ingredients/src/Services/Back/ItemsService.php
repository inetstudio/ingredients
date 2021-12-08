<?php

namespace InetStudio\IngredientsPackage\Ingredients\Services\Back;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use InetStudio\AdminPanel\Base\Services\BaseService;
use Illuminate\Contracts\Container\BindingResolutionException;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Models\IngredientModelContract;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Services\Back\ItemsServiceContract;

/**
 * Class ItemsService.
 */
class ItemsService extends BaseService implements ItemsServiceContract
{
    /**
     * ItemsService constructor.
     *
     * @param  IngredientModelContract  $model
     */
    public function __construct(IngredientModelContract $model)
    {
        parent::__construct($model);
    }

    /**
     * Сохраняем модель.
     *
     * @param  array  $data
     * @param  int  $id
     *
     * @return IngredientModelContract
     *
     * @throws BindingResolutionException
     */
    public function save(array $data, int $id): IngredientModelContract
    {
        $action = ($id) ? 'отредактирован' : 'создан';

        $itemData = Arr::only($data, $this->model->getFillable());
        $item = $this->saveModel($itemData, $id);

        $metaData = Arr::get($data, 'meta', []);
        app()->make('InetStudio\MetaPackage\Meta\Contracts\Services\Back\ItemsServiceContract')
            ->attachToObject($metaData, $item);

        $images = (config('ingredients.images.conversions.ingredient')) ? array_keys(
            config('ingredients.images.conversions.ingredient')
        ) : [];
        app()->make('InetStudio\Uploads\Contracts\Services\Back\ImagesServiceContract')
            ->attachToObject(request(), $item, $images, 'ingredients', 'ingredient');

        $tagsData = Arr::get($data, 'tags', []);
        app()->make('InetStudio\TagsPackage\Tags\Contracts\Services\Back\ItemsServiceContract')
            ->attachToObject($tagsData, $item);

        $classifiersData = Arr::get($data, 'classifiers', []);
        app()->make('InetStudio\Classifiers\Entries\Contracts\Services\Back\ItemsServiceContract')
            ->attachToObject($classifiersData, $item);

        resolve('InetStudio\WidgetsPackage\Widgets\Contracts\Actions\Back\AttachWidgetsToObjectActionContract')
            ->execute(
                resolve(
                    'InetStudio\WidgetsPackage\Widgets\Contracts\DTO\Actions\Back\AttachWidgetsToObjectDataContract',
                    [
                        'args' => [
                            'item' => $item,
                            'widgets' => explode(',', request()->get('widgets'))
                        ],
                    ]
                )
            );

        $item->searchable();

        event(
            app()->makeWith(
                'InetStudio\IngredientsPackage\Ingredients\Contracts\Events\Back\ModifyItemEventContract',
                compact('item')
            )
        );

        Session::flash('success', 'Ингредиент «'.$item->title.'» успешно '.$action);

        return $item;
    }

    /**
     * Возвращаем статистику объектов по статусу.
     *
     * @return mixed
     */
    public function getIngredientsStatisticByStatus()
    {
        $ingredients = $this->model::buildQuery(
                [
                    'relations' => ['status'],
                ]
            )
            ->select(['status_id', DB::raw('count(*) as total')])
            ->groupBy('status_id')
            ->get();

        return $ingredients;
    }

    /**
     * Присваиваем ингредиенты объекту.
     *
     * @param $ingredients
     * @param $item
     */
    public function attachToObject($ingredients, $item): void
    {
        if ($ingredients instanceof Request) {
            $ingredients = $ingredients->get('ingredients', []);
        } else {
            $ingredients = (array) $ingredients;
        }

        if (! empty($ingredients)) {
            $item->syncIngredients($this->model::whereIn('id', $ingredients)->get());
        } else {
            $item->detachIngredients($item->ingredients);
        }
    }
}
