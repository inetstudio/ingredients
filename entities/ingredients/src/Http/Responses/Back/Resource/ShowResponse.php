<?php

namespace InetStudio\IngredientsPackage\Ingredients\Http\Responses\Back\Resource;

use Illuminate\Http\Request;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Models\IngredientModelContract;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Http\Responses\Back\Resource\ShowResponseContract;

/**
 * Class ShowResponse.
 */
class ShowResponse implements ShowResponseContract
{
    /**
     * @var IngredientModelContract
     */
    protected $item;

    /**
     * ShowResponse constructor.
     *
     * @param  IngredientModelContract  $item
     */
    public function __construct(IngredientModelContract $item)
    {
        $this->item = $item;
    }

    /**
     * Возвращаем ответ при получении объекта.
     *
     * @param  Request  $request
     *
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return response()->json($this->item);
    }
}
