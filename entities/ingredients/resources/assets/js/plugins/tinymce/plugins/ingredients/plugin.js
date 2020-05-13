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

  function loadWidget() {
    let component = window.Admin.vue.helpers.getVueComponent('ingredients-package', 'IngredientWidget');

    component.$data.model.id = widgetData.model.id;
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

        window.Admin.vue.helpers.initComponent('ingredients-package', 'IngredientWidget', widgetData);

        window.waitForElement('#add_ingredient_widget_modal', function() {
          loadWidget();

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
