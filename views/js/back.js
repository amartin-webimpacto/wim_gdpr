/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2018 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */

function showError(mensaje) {
    if ($("#content>.bootstrap:eq(0)").length) {// Version >= 1.6
        var container = $("<div>").addClass("bootstrap");
        var divMsg = $("<div>").addClass("alert alert-danger").html(mensaje);
        var buttonClose = $("<button>").attr({
            type: "button",
            "data-dismiss": "alert"
        }).addClass("close").text("×")
        divMsg.append(buttonClose);
        container.append(divMsg);

        $(container).insertAfter("#content>.bootstrap:eq(0)");
    } else {// Version <= 1.5
        var container = $("<div>").addClass("error").text(mensaje);
        $(container).insertBefore("#cms_toolbar");
    }

    $("html, body").animate({scrollTop: 0}, 500);
}

$(document).ready(function () {
    // Prestashop >= 1.6
    $('#cms_form button[type="submit"]').on('click', function (e) {
        // content textarea
        var pagContent = $('.mce-tinymce.mce-container.mce-panel iframe');
        pagContent.each(function (index) {
            var pagContent = $('textarea.rte');
            pagContent.each(function (index) {
                var selectorLang = $(this).attr('id');
                $(this).text(tinyMCE.get(selectorLang).getContent());

            });
        });

        //shop tree
        var inputChecked = $('.panel .tree-folder input:checked');
        var itemShopSelected = '';
        if (inputChecked.length) {
            inputChecked.each(function (index) {
                var id_shop = $(this).val();
                itemShopSelected += '&itemShopSelected[' + index + ']=' + id_shop;
            });
        }

        var dataForm_serialize = $('#cms_form').serialize();
        var dataForm = dataForm_serialize + itemShopSelected + '&current_id_shop=' + current_id_shop;

        $.ajax({
            type: 'POST',
            headers: {"cache-control": "no-cache"},
            url: url + 'modules/wim_gdpr/ajax.php' + '?rand=' + new Date().getTime(),
            data: {
                action: "validationForm",
                data: dataForm
            },
            dataType: 'json',
            async: false,
            success: function (jsonData) {
                $('.alert.alert-danger, .alert.alert-success').remove();
                if (typeof jsonData !== 'undefined' && jsonData.length > 0) {
                    e.preventDefault();
                    jsonData.forEach(function (error, index) {
                        showError(error);
                    });

                    $("html, body").animate({scrollTop: 0}, 500);
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                // error en el servidor
                showError("Ha ocurrido un error inesperado en el servidor.");
            }
        });
    });


    // Prestashop <= 1.5
    setTimeout(function () {// Hay que hacerlo así porque se sobreescribe el evento click
        $('#desc-cms-save-and-preview, #desc-cms-save-and-stay, #desc-cms-save').off("click");

        $('#desc-cms-save-and-preview, #desc-cms-save-and-stay, #desc-cms-save').on('click', function (e) {
            // content textarea
            $('textarea.rte').each(function () {
                var selectorLang = $(this).attr('id');
                $(this).text(tinyMCE.get(selectorLang).getContent());
            });

            //shop tree
            var inputChecked = $('.input_shop:checked');
            var itemShopSelected = '';
            if (inputChecked.length) {
                inputChecked.each(function (index) {
                    var id_shop = $(this).attr("shop_id");
                    itemShopSelected += '&itemShopSelected[' + index + ']=' + id_shop;
                });
            }

            var dataForm_serialize = $('#cms_form').serialize();
            var dataForm = dataForm_serialize + itemShopSelected + '&current_id_shop=' + current_id_shop;

            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: url + 'modules/wim_gdpr/ajax.php' + '?rand=' + new Date().getTime(),
                data: {
                    action: "validationForm",
                    data: dataForm
                },
                dataType: 'json',
                success: function (jsonData) {
                    $('.alert.alert-danger, .alert.alert-success').remove();
                    if (typeof jsonData !== 'undefined' && jsonData.length > 0) {
                        e.preventDefault();

                        $('#content>.conf').remove();
                        $('#content>.error').remove();

                        jsonData.forEach(function (error, index) {
                            showError(error);
                        });

                        $("html, body").animate({scrollTop: 0}, 500);
                    } else {
                        $("#cms_form").submit();
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    // error en el servidor
                    showError("Ha ocurrido un error inesperado en el servidor.");
                }
            });
        });
    }, 50);

});
