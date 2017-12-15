@extends('admin::back.layouts.app')

@php
    $title = ($item->id) ? 'Редактирование ингредиента' : 'Добавление ингредиента';
@endphp

@section('title', $title)

@pushonce('styles:datatables')
    <!-- DATATABLES -->
    <link href="{!! asset('admin/css/plugins/datatables/datatables.min.css') !!}" rel="stylesheet">
@endpushonce

@pushonce('styles:products_custom')
    <!-- CUSTOM STYLE -->
    <link href="{!! asset('admin/css/modules/products/custom.css') !!}" rel="stylesheet">
@endpushonce

@section('content')

    @push('breadcrumbs')
        @include('admin.module.ingredients::back.partials.breadcrumbs')
        <li>
            <a href="{{ route('back.ingredients.index') }}">Ингредиенты</a>
        </li>
    @endpush

    <div class="row m-sm">
        <a class="btn btn-white" href="{{ route('back.ingredients.index') }}">
            <i class="fa fa-arrow-left"></i> Вернуться назад
        </a>
        @if ($item->id && $item->href)
            <a class="btn btn-white" href="{{ $item->href }}" target="_blank">
                <i class="fa fa-eye"></i> Посмотреть на сайте
            </a>
        @endif
        @php
            $status = (! $item->id or ! $item->status) ? \InetStudio\Statuses\Models\StatusModel::get()->first() : $item->status;
        @endphp
        <div class="bg-{{ $status->color_class }} p-xs b-r-sm pull-right">{{ $status->name }}</div>
    </div>

    <div class="wrapper wrapper-content">

        {!! Form::info() !!}

        {!! Form::open(['url' => (!$item->id) ? route('back.ingredients.store') : route('back.ingredients.update', [$item->id]), 'id' => 'mainForm', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}

            @if ($item->id)
                {{ method_field('PUT') }}
            @endif

            {!! Form::hidden('ingredient_id', (!$item->id) ? '' : $item->id) !!}

            {!! Form::buttons('', '', ['back' => 'back.ingredients.index']) !!}

            {!! Form::meta('', $item) !!}

            {!! Form::social_meta('', $item) !!}

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel-group float-e-margins" id="mainAccordion">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#mainAccordion" href="#collapseMain" aria-expanded="true">Основная информация</a>
                                </h5>
                            </div>
                            <div id="collapseMain" class="panel-collapse collapse in" aria-expanded="true">
                                <div class="panel-body">

                                    {!! Form::string('title', $item->title, [
                                        'label' => [
                                            'title' => 'Заголовок',
                                        ],
                                        'field' => [
                                            'class' => 'form-control slugify',
                                            'data-slug-url' => route('back.ingredients.getSlug'),
                                            'data-slug-target' => 'slug',
                                        ],
                                    ]) !!}

                                    {!! Form::string('slug', $item->slug, [
                                        'label' => [
                                            'title' => 'URL',
                                        ],
                                        'field' => [
                                            'class' => 'form-control slugify',
                                            'data-slug-url' => route('back.ingredients.getSlug'),
                                            'data-slug-target' => 'slug',
                                        ],
                                    ]) !!}

                                    @php
                                        $previewImageMedia = $item->getFirstMedia('preview');
                                    @endphp

                                    {!! Form::crop('preview', $previewImageMedia, [
                                        'label' => [
                                            'title' => 'Превью',
                                        ],
                                        'image' => [
                                            'src' => isset($previewImageMedia) ? url($previewImageMedia->getUrl()) : '',
                                        ],
                                        'crops' => [
                                            [
                                                'title' => 'Область по умолчанию',
                                                'name' => 'default',
                                                'ratio' => '380/360',
                                                'value' => isset($previewImageMedia) ? $previewImageMedia->getCustomProperty('crop.default') : '',
                                                'size' => [
                                                    'width' => 380,
                                                    'height' => 360,
                                                    'type' => 'min',
                                                    'description' => 'Минимальный размер области — 380x360 пикселей'
                                                ],
                                            ],
                                            [
                                                'title' => 'Размер 3х4',
                                                'name' => '3_4',
                                                'ratio' => '3/4',
                                                'value' => isset($previewImageMedia) ? $previewImageMedia->getCustomProperty('crop.3_4') : '',
                                                'size' => [
                                                    'width' => 384,
                                                    'height' => 512,
                                                    'type' => 'min',
                                                    'description' => 'Минимальный размер области 3x4 — 384x512 пикселей'
                                                ],
                                            ],
                                            [
                                                'title' => 'Размер 3х2',
                                                'name' => '3_2',
                                                'ratio' => '3/2',
                                                'value' => isset($previewImageMedia) ? $previewImageMedia->getCustomProperty('crop.3_2') : '',
                                                'size' => [
                                                    'width' => 768,
                                                    'height' => 512,
                                                    'type' => 'min',
                                                    'description' => 'Минимальный размер области 3x4 — 768x512 пикселей'
                                                ],
                                            ],
                                        ],
                                        'additional' => [
                                            [
                                                'title' => 'Описание',
                                                'name' => 'description',
                                                'value' => isset($previewImageMedia) ? $previewImageMedia->getCustomProperty('description') : '',
                                            ],
                                            [
                                                'title' => 'Copyright',
                                                'name' => 'copyright',
                                                'value' => isset($previewImageMedia) ? $previewImageMedia->getCustomProperty('copyright') : '',
                                            ],
                                            [
                                                'title' => 'Alt',
                                                'name' => 'alt',
                                                'value' => isset($previewImageMedia) ? $previewImageMedia->getCustomProperty('alt') : '',
                                            ],
                                        ],
                                    ]) !!}

                                    {!! Form::wysiwyg('description', $item->description, [
                                        'label' => [
                                            'title' => 'Лид',
                                        ],
                                        'field' => [
                                            'class' => 'tinymce-simple',
                                            'type' => 'simple',
                                            'id' => 'description',
                                        ],
                                    ]) !!}

                                    {!! Form::wysiwyg('content', $item->content, [
                                        'label' => [
                                            'title' => 'Содержимое',
                                        ],
                                        'field' => [
                                            'class' => 'tinymce',
                                            'id' => 'content',
                                            'hasImages' => true,
                                        ],
                                        'images' => [
                                            'media' => $item->getMedia('content'),
                                            'fields' => [
                                                [
                                                    'title' => 'Описание',
                                                    'name' => 'description',
                                                ],
                                                [
                                                    'title' => 'Copyright',
                                                    'name' => 'copyright',
                                                ],
                                                [
                                                    'title' => 'Alt',
                                                    'name' => 'alt',
                                                ],
                                            ],
                                        ],
                                    ]) !!}

                                    {!! Form::dropdown('tags[]', $item->tags()->pluck('id')->toArray(), [
                                        'label' => [
                                            'title' => 'Теги',
                                        ],
                                        'field' => [
                                            'class' => 'select2 form-control',
                                            'data-placeholder' => 'Выберите теги',
                                            'style' => 'width: 100%',
                                            'multiple' => 'multiple',
                                            'data-source' => route('back.tags.getSuggestions'),
                                        ],
                                        'options' => (old('tags')) ? \InetStudio\Tags\Models\TagModel::whereIn('id', old('tags'))->pluck('name', 'id')->toArray() : $item->tags()->pluck('name', 'id')->toArray(),
                                    ]) !!}

                                    {!! Form::dropdown('classifiers[]', $item->classifiers()->where('type', 'Тип кожи')->pluck('classifiers.id')->toArray(), [
                                        'label' => [
                                            'title' => 'Тип кожи',
                                        ],
                                        'field' => [
                                            'class' => 'select2 form-control',
                                            'data-placeholder' => 'Выберите типы кожи',
                                            'style' => 'width: 100%',
                                            'multiple' => 'multiple',
                                            'data-source' => route('back.classifiers.getSuggestions', ['type' => 'Тип кожи']),
                                        ],
                                        'options' => (old('classifiers')) ? \InetStudio\Classifiers\Models\ClassifierModel::whereIn('id', old('classifiers'))->where('type', 'Тип кожи')->pluck('classifiers.value', 'classifiers.id')->toArray() : $item->classifiers()->where('type', 'Тип кожи')->pluck('classifiers.value', 'classifiers.id')->toArray(),
                                    ]) !!}

                                    {!! Form::dropdown('status_id', $item->status_id, [
                                        'label' => [
                                            'title' => 'Статус материала',
                                        ],
                                        'field' => [
                                            'class' => 'select2 form-control',
                                            'data-placeholder' => 'Выберите статус',
                                            'style' => 'width: 100%',
                                        ],
                                        'options' => [null => ''] + \InetStudio\Statuses\Models\StatusModel::select('id', 'name')->pluck('name', 'id')->toArray(),
                                    ]) !!}

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {!! Form::products('products', $item->products, ['table' => $productsTable])!!}

            {!! Form::buttons('', '', ['back' => 'back.ingredients.index']) !!}

        {!! Form::close()!!}
    </div>

    @include('admin.module.articles::back.pages.modals.suggestion')
    @include('admin.module.experts::back.pages.modals.suggestion')
    @include('admin.module.ingredients::back.pages.modals.suggestion')
    @include('admin.module.polls::back.pages.modals.form')

@endsection

@pushonce('scripts:datatables')
    <!-- DATATABLES -->
    <script src="{!! asset('admin/js/plugins/datatables/datatables.min.js') !!}"></script>
@endpushonce

@pushonce('scripts:datatable_products_embedded')
    {!! $productsTable->scripts() !!}
@endpushonce

@pushonce('scripts:products_custom')
    <!-- CUSTOM SCRIPT -->
    <script src="{!! asset('admin/js/modules/products/custom.js') !!}"></script>
@endpushonce
