<?php

namespace InetStudio\Ingredients\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use InetStudio\Ingredients\Contracts\Http\Requests\Back\SaveIngredientRequestContract;
use InetStudio\Ingredients\Contracts\Http\Controllers\Back\IngredientsControllerContract;
use InetStudio\Ingredients\Contracts\Http\Responses\Back\Ingredients\FormResponseContract;
use InetStudio\Ingredients\Contracts\Http\Responses\Back\Ingredients\SaveResponseContract;
use InetStudio\Ingredients\Contracts\Http\Responses\Back\Ingredients\IndexResponseContract;
use InetStudio\Ingredients\Contracts\Http\Responses\Back\Ingredients\DestroyResponseContract;

/**
 * Class IngredientsController.
 */
class IngredientsController extends Controller implements IngredientsControllerContract
{
    /**
     * Используемые сервисы.
     *
     * @var array
     */
    public $services;

    /**
     * IngredientsController constructor.
     */
    public function __construct()
    {
        $this->services['ingredients'] = app()->make('InetStudio\Ingredients\Contracts\Services\Back\IngredientsServiceContract');
        $this->services['dataTables'] = app()->make('InetStudio\Ingredients\Contracts\Services\Back\IngredientsDataTableServiceContract');
    }

    /**
     * Список объектов.
     *
     * @return IndexResponseContract
     */
    public function index(): IndexResponseContract
    {
        $table = $this->services['dataTables']->html();

        return app()->makeWith(IndexResponseContract::class, [
            'data' => compact('table'),
        ]);
    }

    /**
     * Добавление объекта.
     *
     * @return FormResponseContract
     */
    public function create(): FormResponseContract
    {
        $item = $this->services['ingredients']->getIngredientObject();

        return app()->makeWith(FormResponseContract::class, [
            'data' => compact('item'),
        ]);
    }

    /**
     * Создание объекта.
     *
     * @param SaveIngredientRequestContract $request
     *
     * @return SaveResponseContract
     */
    public function store(SaveIngredientRequestContract $request): SaveResponseContract
    {
        return $this->save($request);
    }

    /**
     * Редактирование объекта.
     *
     * @param int $id
     *
     * @return FormResponseContract
     */
    public function edit($id = 0): FormResponseContract
    {
        $item = $this->services['ingredients']->getIngredientObject($id);

        return app()->makeWith(FormResponseContract::class, [
            'data' => compact('item'),
        ]);
    }

    /**
     * Обновление объекта.
     *
     * @param SaveIngredientRequestContract $request
     * @param int $id
     *
     * @return SaveResponseContract
     */
    public function update(SaveIngredientRequestContract $request, int $id = 0): SaveResponseContract
    {
        return $this->save($request, $id);
    }

    /**
     * Сохранение объекта.
     *
     * @param SaveIngredientRequestContract $request
     * @param int $id
     *
     * @return SaveResponseContract
     */
    private function save(SaveIngredientRequestContract $request, int $id = 0): SaveResponseContract
    {
        $item = $this->services['ingredients']->save($request, $id);

        return app()->makeWith(SaveResponseContract::class, [
            'item' => $item,
        ]);
    }

    /**
     * Удаление объекта.
     *
     * @param int $id
     *
     * @return DestroyResponseContract
     */
    public function destroy(int $id = 0): DestroyResponseContract
    {
        $result = $this->services['ingredients']->destroy($id);

        return app()->makeWith(DestroyResponseContract::class, [
            'result' => ($result === null) ? false : $result,
        ]);
    }
}
