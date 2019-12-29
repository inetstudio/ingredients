<?php

namespace InetStudio\IngredientsPackage\Ingredients\Contracts\Http\Controllers\Back;

use InetStudio\IngredientsPackage\Ingredients\Contracts\Services\Back\ItemsServiceContract;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Services\Back\DataTableServiceContract;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Http\Requests\Back\SaveItemRequestContract;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Http\Responses\Back\Resource\FormResponseContract;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Http\Responses\Back\Resource\SaveResponseContract;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Http\Responses\Back\Resource\ShowResponseContract;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Http\Responses\Back\Resource\IndexResponseContract;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Http\Responses\Back\Resource\DestroyResponseContract;

/**
 * Interface ResourceControllerContract.
 */
interface ResourceControllerContract
{
    /**
     * Список объектов.
     *
     * @param  DataTableServiceContract  $dataTableService
     *
     * @return IndexResponseContract
     */
    public function index(DataTableServiceContract $dataTableService): IndexResponseContract;

    /**
     * Получение объекта.
     *
     * @param  ItemsServiceContract  $resourceService
     * @param  int  $id
     *
     * @return ShowResponseContract
     */
    public function show(ItemsServiceContract $resourceService, int $id = 0): ShowResponseContract;
    
    /**
     * Создание объекта.
     *
     * @param  ItemsServiceContract  $resourceService
     *
     * @return FormResponseContract
     */
    public function create(ItemsServiceContract $resourceService): FormResponseContract;

    /**
     * Создание объекта.
     *
     * @param  ItemsServiceContract  $resourceService
     * @param  SaveItemRequestContract  $request
     *
     * @return SaveResponseContract
     */
    public function store(ItemsServiceContract $resourceService, SaveItemRequestContract $request): SaveResponseContract;

    /**
     * Редактирование объекта.
     *
     * @param  ItemsServiceContract  $resourceService
     * @param  int  $id
     *
     * @return FormResponseContract
     */
    public function edit(ItemsServiceContract $resourceService, int $id = 0): FormResponseContract;

    /**
     * Обновление объекта.
     *
     * @param  ItemsServiceContract  $resourceService
     * @param  SaveItemRequestContract  $request
     * @param  int  $id
     *
     * @return SaveResponseContract
     */
    public function update(
        ItemsServiceContract $resourceService,
        SaveItemRequestContract $request,
        int $id = 0
    ): SaveResponseContract;

    /**
     * Удаление объекта.
     *
     * @param  ItemsServiceContract  $resourceService
     * @param  int  $id
     *
     * @return DestroyResponseContract
     */
    public function destroy(ItemsServiceContract $resourceService, int $id = 0): DestroyResponseContract;
}
