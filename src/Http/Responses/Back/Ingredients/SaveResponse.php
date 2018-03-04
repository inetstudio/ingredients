<?php

namespace InetStudio\Ingredients\Http\Responses\Back\Ingredients;

use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Responsable;
use InetStudio\Ingredients\Contracts\Models\IngredientModelContract;
use InetStudio\Ingredients\Contracts\Http\Responses\Back\Ingredients\SaveResponseContract;

/**
 * Class SaveResponse.
 */
class SaveResponse implements SaveResponseContract, Responsable
{
    /**
     * @var IngredientModelContract
     */
    private $item;

    /**
     * SaveResponse constructor.
     *
     * @param IngredientModelContract $item
     */
    public function __construct(IngredientModelContract $item)
    {
        $this->item = $item;
    }

    /**
     * Возвращаем ответ при сохранении объекта.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return RedirectResponse
     */
    public function toResponse($request): RedirectResponse
    {
        return response()->redirectToRoute('back.ingredients.edit', [
            $this->item->fresh()->id,
        ]);
    }
}
