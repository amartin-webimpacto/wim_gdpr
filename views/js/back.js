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
    var container = $("<div>").addClass("bootstrap");
    var divMsg = $("<div>").addClass("alert alert-danger").html(mensaje);
    var buttonClose = $("<button>").attr({
        type: "button",
        "data-dismiss": "alert"
    }).addClass("close").text("×")
    divMsg.append(buttonClose);
    container.append(divMsg);

    $(container).insertAfter("#content>.bootstrap:eq(0)");

}

$(document).ready(function () {
    $(function () {

        $('#cms_form button[type="submit"]').on('click', function (e) {
            e.preventDefault();

            var pagContent = $('.mce-tinymce.mce-container.mce-panel iframe');
            pagContent.each(function (index) {
                var pagContent = $('textarea.rte');
                pagContent.each(function (index) {
                    var selectorLang = $(this).attr('id');
                    $(this).text(tinyMCE.get(selectorLang).getContent());

                });
            });
            var dataForm = $('#cms_form').serialize();

            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: url + 'modules/wim_gdpr/ajax.php' + '?rand=' + new Date().getTime(),
                data: {
                    action: "validationForm",
                    data: dataForm
                },
                dataType: 'json',
                async: true,
                success: function (jsonData) {
                    $('.alert.alert-danger').remove();
                    if (typeof jsonData !== 'undefined' && jsonData.length > 0) {
                        jsonData.forEach(function (error, index) {
                            showError(error);
                        });

                        $("html, body").animate({scrollTop: 0}, 500);
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log(data);
                }
            });
        });
    });
});