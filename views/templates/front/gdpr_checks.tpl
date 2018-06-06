<ul class="gdpr_check_list">
    <input type="hidden" name="check_cms_list_count[]" id="check_cms_list_count" value="{$gdprListCount}"/>
    {foreach $gdprList as $gdpr}
        <li>
            <input type="checkbox" name="check_cms_list[]" id="check_cms_{$gdpr.id_cms}" value="{$gdpr.id_cms}"/>
            <span>{l s='Entiendo que he leído y acepto'} <a target="_blank" href="{$gdpr.link}">{$gdpr.meta_title}</a></span>
        </li>
    {/foreach}
</ul>