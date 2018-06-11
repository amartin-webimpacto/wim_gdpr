<div class="gdpr_check_list">
    {$cont=0}
    {$id_cms_list = ""}
    {$span = ""}

    {foreach $gdprList as $gdpr}
        {assign var=cont value=$cont+1}
        {if $cont > 1}
            {$id_cms_list="`$id_cms_list`,`$gdpr.id_cms`"}
            {if $cont == $gdprListCount}
                {$span="`$span` y <a target='_blank' href='`$gdpr.link`'>`$gdpr.meta_title`</a>"}
            {else}
                {$span="`$span`, <a target='_blank' href='`$gdpr.link`'>`$gdpr.meta_title`</a>"}
            {/if}
        {else}
            {assign var=id_cms_list value=$gdpr.id_cms}
            {$span="`$span` <a target='_blank' href='`$gdpr.link`'>`$gdpr.meta_title`</a>"}
        {/if}
    {/foreach}
    <input type="hidden" name="check_cms_list_count[]" id="check_cms_list_count" value="1"/>
    <input type="checkbox" name="check_cms_list[]" value="{$id_cms_list}"/>
    <span>{l mod='wim_gdpr' s='Entiendo que he leído y acepto'} {$span}</span>
</div>
