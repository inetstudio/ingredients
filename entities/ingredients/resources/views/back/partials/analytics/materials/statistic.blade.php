@inject('ingredientsService', 'InetStudio\IngredientsPackage\Ingredients\Contracts\Services\Back\ItemsServiceContract')

@php
    $ingredients = $ingredientsService->getIngredientsStatisticByStatus();
@endphp

<li>
    <small class="label label-default">{{ $ingredients->sum('total') }}</small>
    <span class="m-l-xs">Ингредиенты</span>
    <ul class="todo-list m-t">
        @foreach ($ingredients as $ingredient)
            <li>
                <small class="label label-{{ $ingredient->status->color_class }}">{{ $ingredient->total }}</small>
                <span class="m-l-xs">{{ $ingredient->status->name }}</span>
            </li>
        @endforeach
    </ul>
</li>
