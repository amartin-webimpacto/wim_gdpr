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


function submitForm() {
    var json = generateJSON();
    var form = $("#module_form");
    var hiddenInput = $("input").attr({
        id: "WIM_GDPR_CMS_LIST",
        name: "WIM_GDPR_CMS_LIST",
        type: "hidden"
    }).val(json);
    form.append(hiddenInput);

    //form.submit()
}