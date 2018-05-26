let ingredients = {};

ingredients.init = function () {
    $('#choose_ingredient_modal').on('hidden.bs.modal', function (e) {
        let modal = $(this);

        modal.find('.choose-data').val('');
        modal.find('input[name=ingredient]').val('');
    })
};

module.exports = ingredients;
