@pushonce('modals:choose_ingredient')
    <div id="choose_ingredient_modal" tabindex="-1" role="dialog" aria-hidden="true" class="modal inmodal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Закрыть</span></button>
                    <h1 class="modal-title">Выберите ингредиент</h1>
                </div>

                <div class="modal-body">
                    <div class="ibox-content form-horizontal">
                        <div class="row">

                            {!! Form::hidden('ingredient_data', '', [
                                'class' => 'choose-data',
                                'id' => 'ingredient_data',
                            ]) !!}

                            {!! Form::string('ingredient', '', [
                                'label' => [
                                    'title' => 'Ингредиент',
                                ],
                                'field' => [
                                    'class' => 'form-control autocomplete',
                                    'data-search' => route('back.ingredients.getSuggestions'),
                                    'data-target' => '#ingredient_data'
                                ],
                            ]) !!}

                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Закрыть</button>
                    <a href="#" class="btn btn-primary save">Сохранить</a>
                </div>

            </div>
        </div>
    </div>
@endpushonce

@pushonce('scripts:autocomplete')
    <!-- AUTOCOMPLETE -->
    <script src="{!! asset('admin/js/plugins/autocomplete/jquery.autocomplete.min.js') !!}"></script>
@endpushonce
