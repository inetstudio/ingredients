import {ingredients} from './package/ingredients';

require('./plugins/tinymce/plugins/ingredients');

require('../../../../../../widgets/entities/widgets/resources/assets/js/mixins/widget');

require('./stores/ingredients');

window.Vue.component(
    'IngredientWidget',
    () => import('./components/partials/IngredientWidget/IngredientWidget.vue'),
);

ingredients.init();
