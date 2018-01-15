@php
    $item = $value;
@endphp

{!! Form::dropdown('ingredients[]', $item->ingredients()->pluck('id')->toArray(), [
    'label' => [
        'title' => 'Ингредиенты',
    ],
    'field' => [
        'class' => 'select2 form-control',
        'data-placeholder' => 'Выберите ингредиенты',
        'style' => 'width: 100%',
        'multiple' => 'multiple',
        'data-source' => route('back.ingredients.getSuggestions'),
    ],
    'options' => (old('ingredients')) ? \InetStudio\Ingredients\Models\IngredientModel::whereIn('id', old('ingredients'))->pluck('title', 'id')->toArray() : $item->ingredients()->pluck('title', 'id')->toArray(),
]) !!}
