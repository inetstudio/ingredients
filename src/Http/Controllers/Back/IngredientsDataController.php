<?php

namespace InetStudio\Ingredients\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use InetStudio\Ingredients\Contracts\Http\Controllers\Back\IngredientsDataControllerContract;

/**
 * Class IngredientsDataController.
 */
class IngredientsDataController extends Controller implements IngredientsDataControllerContract
{
    /**
     * Используемые сервисы.
     *
     * @var array
     */
    private $services;

    /**
     * IngredientsController constructor.
     */
    public function __construct()
    {
        $this->services['dataTables'] = app()->make('InetStudio\Ingredients\Contracts\Services\Back\IngredientsDataTableServiceContract');
    }

    /**
     * Получаем данные для отображения в таблице.
     *
     * @return mixed
     */
    public function data()
    {
        return $this->services['dataTables']->ajax();
    }
}
