{*
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
*}

<div class="panel">
    <h3>{l s='WebImpacto GDPR' mod='wim_gdpr'} General Data Protection Regulation</h3>
    <form id="module_form" class="defaultForm form-horizontal"
          action="index.php?controller=AdminModules&amp;configure=wim_gdpr&amp;tab_module=others&amp;module_name=wim_gdpr&amp;token={$token}"
          method="post" enctype="multipart/form-data" novalidate>

        {foreach $gdpr_list as $shop}
            <table class="table table-striped table-nonfluid">
                <thead>
                <tr>
                    <th scope="col"><b>#</b></th>
                    <th scope="col"><b>{$shop.name}</b></th>
                    <th scope="col"><b>Protegido</b></th>
                </tr>
                </thead>
                <tbody>
                {foreach $shop.cms as $cms}
                    <tr>
                        <td>{$cms.id}</td>
                        <td>{$cms.meta_title}</td>
                        <td><input value="{$cms.id}" parent="{$shop.id}" type="checkbox"
                                   {if $cms.checked==1}checked="checked"{/if} /></td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        {/foreach}

        <input type="hidden" name="submitWim_gdprModule" value="1">

        <div class="panel-footer">
            <button onclick="submitForm()" value="1" id="module_form_submit_btn" name="submitWim_gdprModule"
                    class="btn btn-default pull-right">
                <i class="process-icon-save"></i> Guardar
            </button>
        </div>
    </form>
</div>
<script>
    trSelectCheckbox();
</script>