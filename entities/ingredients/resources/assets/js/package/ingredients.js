let ingredients = {};

ingredients.init = function() {
  if (!window.Admin.vue.modulesComponents.modules.hasOwnProperty('ingredients-package')) {
    window.Admin.vue.modulesComponents.modules = Object.assign(
        {}, window.Admin.vue.modulesComponents.modules, {
          'ingredients-package': {
            components: [],
          },
        });
  }
};

module.exports = ingredients;
