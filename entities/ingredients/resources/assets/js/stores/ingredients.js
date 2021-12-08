import hash from 'object-hash';
import { v4 as uuidv4 } from 'uuid';

window.Admin.vue.stores['ingredients-package_ingredients'] = new window.Vuex.Store({
  state: {
    emptyIngredient: {
      model: {
        title: '',
        slug: '',
        description: '',
        content: '',
        publish_date: null,
        webmaster_id: '',
        status_id: 0,
        created_at: null,
        updated_at: null,
        deleted_at: null,
      },
      isModified: false,
      hash: '',
    },
    ingredient: {},
    mode: '',
  },
  mutations: {
    setIngredient(state, ingredient) {
      let emptyIngredient = JSON.parse(JSON.stringify(state.emptyIngredient));
      emptyIngredient.model.id = uuidv4();

      let resultIngredient = _.merge(emptyIngredient, ingredient);
      resultIngredient.hash = hash(resultIngredient.model);

      state.ingredient = resultIngredient;
    },
    setMode(state, mode) {
      state.mode = mode;
    },
  },
});
