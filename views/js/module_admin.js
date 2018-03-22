/**
 * Created by YV-01-030 on 14/03/2018.
 */
function generateJSON() {
    var myarray = [];
    $("#module_form").find(":checkbox:checked").each(function (index, elem) {
        var shop_id = $(elem).attr("parent");
        var cms_id = $(elem).val();

        if (typeof myarray[shop_id] === 'undefined') { // Si no existe
            myarray[shop_id] = [];
        }
        myarray[shop_id].push(cms_id);
    });

    var shop = {};
    myarray.forEach(function (cmsList, shop_id) {
        shop[shop_id] = {cms: cmsList};
    });

    var json = JSON.stringify({shop});

    return json;
}

/**
 * Hace que se seleccione el checkbox al hacer clic en cualquier elemento del tr
 */
function trSelectCheckbox() {
    $('#module_form tr').click(function (event) {
        if (event.target.type !== 'checkbox') {
            $(':checkbox', this).trigger('click');
        }
    });
}

/**
 * Envía el formulario de configuracion del modulo.
 */
function submitForm() {
    var json = generateJSON();
    var form = $("#module_form");
    var hiddenInput = $("<input>").attr({
        id: "WIM_GDPR_CMS_LIST",
        name: "WIM_GDPR_CMS_LIST",
        type: "hidden"
    }).val(json);
    form.append(hiddenInput);
}