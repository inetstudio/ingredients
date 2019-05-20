<?php

namespace InetStudio\IngredientsPackage\Ingredients\Http\Responses\Back\Resource;

use Illuminate\Http\Request;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Models\IngredientModelContract;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Http\Responses\Back\Resource\SaveResponseContract;

/**
 * Class SaveResponse.
 */
class SaveResponse implements SaveResponseContract
{
    /**
     * @var IngredientModelContract
     */
    protected $item;

    /**
     * SaveResponse constructor.
     *
     * @param  IngredientModelContract  $item
     */
    public function __construct(IngredientModelContract $item)
    {
        $this->item = $item;
    }

    /**
     * Возвращаем ответ при сохранении объекта.
     *
     * @param  Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function toResponse($request)
    {
        $item = $this->item->fresh();

        return response()->redirectToRoute(
            'back.ingredients.edit',
            [
               $item['id'],
            ]
        );
    }
}
