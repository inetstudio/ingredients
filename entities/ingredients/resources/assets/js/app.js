require('./plugins/tinymce/plugins/ingredients');

require('../../../../../../widgets/resources/assets/js/mixins/widget');

require('./stores/ingredients');

Vue.component(
    'IngredientWidget',
    require('./components/partials/IngredientWidget/IngredientWidget.vue').default,
);

let ingredients = require('./package/ingredients');
ingredients.init();
