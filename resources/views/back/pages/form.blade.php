@extends('admin::back.layouts.app')

@php
    $title = ($item->id) ? 'Редактирование ингредиента' : 'Создание ингредиента';
@endphp

@section('title', $title)

@section('content')

    @push('breadcrumbs')
        @include('admin.module.ingredients::back.partials.breadcrumbs.form')
    @endpush

    <div class="wrapper wrapper-content">
        <div class="ibox">
            <div class="ibox-title">
                <a class="btn btn-sm btn-white m-r-xs" href="{{ route('back.ingredients.index') }}">
                    <i class="fa fa-arrow-left"></i> Вернуться назад
                </a>
                @if ($item->id && $item->href)
                    <a class="btn btn-sm btn-white" href="{{ $item->href }}" target="_blank">
                        <i class="fa fa-eye"></i> Посмотреть на сайте
                    </a>
                @endif
                <div class="ibox-tools">
                    @php
                        $status = (! $item->id or ! $item->status) ? \InetStudio\Statuses\Models\StatusModel::get()->first() : $item->status;
                    @endphp
                    <button class="btn btn-sm btn-{{ $status->color_class }}">{{ $status->name }}</button>
                </div>
            </div>
        </div>

        {!! Form::info() !!}

        {!! Form::open(['url' => (! $item->id) ? route('back.ingredients.store') : route('back.ingredients.update', [$item->id]), 'id' => 'mainForm', 'enctype' => 'multipart/form-data']) !!}

            @if ($item->id)
                {{ method_field('PUT') }}
            @endif

            {!! Form::hidden('ingredient_id', (! $item->id) ? '' : $item->id, ['id' => 'object-id']) !!}

            {!! Form::hidden('ingredient_type', get_class($item), ['id' => 'object-type']) !!}

            <div class="ibox">
                <div class="ibox-title">
                    {!! Form::buttons('', '', ['back' => 'back.ingredients.index']) !!}
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel-group float-e-margins" id="mainAccordion">

                                {!! Form::meta('', $item) !!}

                                {!! Form::social_meta('', $item) !!}

                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h5 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#mainAccordion" href="#collapseMain" aria-expanded="true">Основная информация</a>
                                        </h5>
                                    </div>
                                    <div id="collapseMain" class="collapse show" aria-expanded="true">
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
                                                    'filepath' => isset($previewImageMedia) ? url($previewImageMedia->getUrl()) : '',
                                                    'filename' => isset($previewImageMedia) ? $previewImageMedia->file_name : '',
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

                                            {!! Form::widgets('', $item) !!}

                                            {!! Form::tags('', $item) !!}

                                            {!! Form::classifiers('', $item, [
                                                'label' => [
                                                    'title' => 'Тип кожи',
                                                ],
                                                'field' => [
                                                    'placeholder' => 'Выберите типы кожи',
                                                    'group' => 'Тип кожи',
                                                ],
                                            ]) !!}

                                            {!! Form::datepicker('publish_date', ($item->publish_date) ? $item->publish_date->format('d.m.Y H:i') : '', [
                                                'label' => [
                                                    'title' => 'Дата публикации',
                                                ],
                                                'field' => [
                                                    'class' => 'datetimepicker form-control',
                                                ],
                                            ]) !!}

                                            {!! Form::status('', $item) !!}

                                        </div>
                                    </div>
                                </div>

                                {!! Form::products('products', $item->products)!!}

                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox-footer">
                    {!! Form::buttons('', '', ['back' => 'back.ingredients.index']) !!}
                </div>
            </div>

        {!! Form::close()!!}
    </div>

    @include('admin.module.articles::back.pages.modals.suggestion')
    @include('admin.module.persons::back.pages.modals.suggestion')
    @include('admin.module.ingredients::back.pages.modals.suggestion')
    @include('admin.module.products::back.pages.modals.suggestion')

@endsection
