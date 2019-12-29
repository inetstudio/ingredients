window.tinymce.PluginManager.add('ingredients', function(editor) {
  let widgetData = {
    widget: {
      events: {
        widgetSaved: function(model) {
          editor.execCommand(
              'mceReplaceContent',
              false,
              '<img class="content-widget" data-type="ingredient" data-id="' + model.id + '" alt="Виджет-ингредиент: '+model.additional_info.title+'" />',
          );
        },
      },
    },
  };

  function initFormsComponents() {
    if (typeof window.Admin.vue.modulesComponents.$refs['ingredients-package_IngredientWidget'] == 'undefined') {
      window.Admin.vue.modulesComponents.modules['ingredients-package'].components = _.union(
          window.Admin.vue.modulesComponents.modules['ingredients-package'].components, [
            {
              name: 'IngredientWidget',
              data: widgetData,
            },
          ]);
    } else {
      let component = window.Admin.vue.modulesComponents.$refs['ingredients-package_IngredientWidget'][0];

      component.$data.model.id = widgetData.model.id;
    }
  }
  
  editor.addButton('add_ingredient_widget', {
    title: 'Ингредиенты',
    icon: 'fa fa-tint',
    onclick: function() {
      editor.focus();

      let content = editor.selection.getContent();
      let isIngredient = /<img class="content-widget".+data-type="ingredient".+>/g.test(content);

      if (content === '' || isIngredient) {
        widgetData.model = {
          id: parseInt($(content).attr('data-id')) || 0,
        };

        initFormsComponents();

        window.waitForElement('#add_ingredient_widget_modal', function() {
          $('#add_ingredient_widget_modal').modal();
        });
      } else {
        swal({
          title: 'Ошибка',
          text: 'Необходимо выбрать виджет-ингредиент',
          type: 'error',
        });

        return false;
      }
    }
  });
});
