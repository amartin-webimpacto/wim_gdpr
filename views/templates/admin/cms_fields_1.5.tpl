<!-- MOTIVO -->
<label>Motivo de modificación </label>
<div class="margin-form">
    <div class="translatable">
        {foreach from=$languageList item=language name=language}
            <div class="lang_{$language.id_lang}"
                 style="display:block; float: left;{if !$smarty.foreach.language.first}display: none;{/if}">
                <input type="text" name="modification_reason_for_a_new_{$language.id_lang}" id="modification_reason_for_a_new_{$language.id_lang}"
                       class="modification_reason_for_a_new" required="required">
            <span class="hint" name="help_box" style="display: none;">Este CMS está protegido por WebImpacto GDPR y es obligatorio indicar el motivo de su modificación
                <span class="hint-pointer">&nbsp;</span>
            </span>
            </div>
        {/foreach}

        <div class="displayed_flag"><img class="language_current pointer" src="../img/l/{$languageList[0].id_lang}.jpg"
                                         alt="{$languageList[0].name}" onclick="toggleLanguageFlags(this)">
        </div>

        <div class="language_flags">Elige idioma:<br><br>
            {foreach from=$languageList item=languageB}
                <img class="pointer" src="../img/l/{$languageB.id_lang}.jpg" alt="{$languageB.name}"
                     style="margin: 2px;" onclick="changeFormLanguage('{$languageB.id_lang}', '{$languageB.iso_code}','0');">
            {/foreach}
        </div>

    </div>
    <sup>*</sup>

    <div class="clear"></div>
</div>

<!-- FIN MOTIVO -->

<!-- SHOW TO USERS -->

<div class="form-group">
    <label class="control-label col-lg-3">
        Mostrar a usuarios
    </label>

    <div class="col-lg-9">
        <select name="show_to_users" id="show_to_users">
            <option value="0" {if $show_to_users == 0}selected="selected"{/if}>Ni se muestra en el CMS en el front la
                razón de cambio, ni se pedirá aceptación por parte de los usuarios si es la última versión
            </option>
            <option value="1" {if $show_to_users == 1}selected="selected"{/if}>Se muestra en el CMS en el front la razón
                de cambio y se pedirá aceptación por parte de los usuarios si es la última versión
            </option>
            <option value="2" {if $show_to_users == 2}selected="selected"{/if}>Se muestra en el CMS en el front la razón
                de cambio pero NO pedirá aceptación por parte de los usuarios si es la última versión
            </option>
        </select>
    </div>
</div>
{*

<h1>VERSION ANTIGUA</h1>
<div class="form-group">
    <label class="control-label col-lg-3 required">
        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
              data-original-title="Este CMS está protegido por WebImpacto GDPR y es obligatorio indicar el motivo de su modificación">
        Motivo de modificación
        </span>
    </label>

    <div class="col-lg-9">
        <div class="form-group">
            {foreach from=$languageList item=language name=language}
                <div class="translatable-field lang-{$language.id_lang}"
                     {if !$smarty.foreach.language.first}style="display: none;"{/if}>
                    <div class="col-lg-9">
                        <input type="text" id="modification_reason_for_a_new_{$language.id_lang}"
                               name="modification_reason_for_a_new_{$language.id_lang}"
                               class="modification_reason_for_a_new" required="required">
                    </div>
                    <div class="col-lg-2">
                        <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1"
                                data-toggle="dropdown">
                            {$language.iso_code}
                            <i class="icon-caret-down"></i>
                        </button>
                        <ul class="dropdown-menu">
                            {foreach from=$languageList item=languageB}
                                <li><a href="javascript:hideOtherLanguage({$languageB.id_lang});"
                                       tabindex="-1">{$languageB.name}</a></li>
                            {/foreach}
                        </ul>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
</div>

<!-- FIN MOTIVO -->

<!-- SHOW TO USERS -->
<div class="form-group">
    <label class="control-label col-lg-3">
        Mostrar a usuarios
    </label>

    <div class="col-lg-9">
        <select name="show_to_users" id="show_to_users">
            <option value="0" {if $show_to_users == 0}selected="selected"{/if}>Ni se muestra en el CMS en el front la
                razón de cambio, ni se pedirá aceptación por parte de los usuarios si es la última versión
            </option>
            <option value="1" {if $show_to_users == 1}selected="selected"{/if}>Se muestra en el CMS en el front la razón
                de cambio y se pedirá aceptación por parte de los usuarios si es la última versión
            </option>
            <option value="2" {if $show_to_users == 2}selected="selected"{/if}>Se muestra en el CMS en el front la razón
                de cambio pero NO pedirá aceptación por parte de los usuarios si es la última versión
            </option>
        </select>
    </div>
</div>
<!-- FIN SHOW TO USERS -->
<script type="text/javascript">
    var url = '{$url}';
    var current_id_shop = '{$current_id_shop}'
</script>*}
<script type="text/javascript">
    var url = '{$url}';
    var current_id_shop = '{$current_id_shop}'
</script>
