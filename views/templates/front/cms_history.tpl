{if $cmsVersionHistory|@count > 0}
    <ul class="list-group">
        <li class="list-group-item active">{l s='Historial de cambios'}:</li>
        {foreach $cmsVersionHistory as $history_item}
            <li class="list-group-item">{$history_item.modification_reason_for_a_new}</li>
        {/foreach}
    </ul>
{/if}