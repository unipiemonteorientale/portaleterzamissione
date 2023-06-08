<div class="field" style="margin: 2px;">
    {if $style == "horizontal"}
        <div class="ui horizontal basic primary label">{$schema.title|default:$schema.name}</div> {$list[$props.value]}
    {elseif $action_name == "printx"}
        <div class="field_print_value">{$props.value|default:"&nbsp;"}</div>
        <span class="field_print_label">{$schema.title|default:$schema.name}</span>
    {elseif $action_name == "readx"}
        <label class="field_read_label">{$schema.title|default:$schema.name}</label>
        {if !$schema.no_label|default:false}<div class="field_read_value">{$props.value|default:"&nbsp;"}</div>{/if}
    {else}
    
<script>
var {$schema.codice_domanda} = '{$props[$schema.name]}';
$(document).ready(function() {
    $('.ui.search.selection.dropdown.{$schema.codice_domanda}').dropdown({ 
        {if $action_name == 'search'}
        clearable: true,
        forceSelection: false,
        ignoreDiacritics: true
        {/if}
        
        onChange: function(value, text, $selectedItem) {
            console.log('{$schema.codice_domanda}', 'onchange', value);
            sendTrigger('{$schema.codice_domanda}', value);
        }
    });
	{if $props[$schema.name]|count_characters}sendTrigger('{$schema.codice_domanda}', '{$props[$schema.name]}');{/if}
});
</script>
    
    <div class="two fields">
        <div class="field">
            {if !$schema.no_label|default:false}
            <label>{$schema.title|default:$schema.name} {if $schema.required|default:false}<a class="ui red empty circular label" title="Campo obbligatorio"></a>{/if}</label>
            {elseif $schema2.title|count_characters}<label>&nbsp;</label>
            {/if}
            
            {if $schema.format_code == 'RADIO'}
            
                {foreach key=key item=item from=$list}
                <div class="field">
                    <div class="ui radio checkbox">
                        <input type="radio" name="{$schema.pre}{$schema.name}{$schema.post}" value="{$item.code}" {if $item.code == $props[$schema.name]}checked="checked"{/if}>
                        <label>{$item.label}</label>
                    </div>
                </div>
                {/foreach}
            
            {elseif $action_name == "print"}
            
                {foreach key=key item=item from=$list}
                <div class="field">
                    <div class="ui checkbox">
                        {if $schema.codice_risposta == $item.code}&#10004;{else}&#8226;{/if}
                        <label>{$item.label}</label>
                    </div>
                </div>
                {/foreach}
            
            {else}
            <div class="ui search {if $action_name == 'read'}read-only{else}clearable{/if} selection dropdown {$schema.codice_domanda}">
                <input type="hidden" name="{$schema.pre}{$schema.name}{$schema.post}" value="{$props[$schema.name]}" {if $schema.required|default:false}required{/if}>
                <i class="dropdown icon"></i>
                <div class="default text">{$list[$props.value]|default:$schema.placeholder}</div>
                <div class="scrollhint menu">
                    {if !$schema.required|default:false}<div class="item" data-value=""></div>{/if}
                    {foreach key=key item=item from=$list}
                    <div class="item" data-value="{$item.code}">{$item.label}</div>
                    {/foreach}
                </div>
            </div>
            {/if}
        </div>
        
        {if $schema2}
            {if $action_name == "print"}
        <div class="field">
            <label>{$schema2.title|default:$schema2.name}</label>
            {$props[$schema2.name]}
        </div>
            {else}
        <div class="field">
            <label>{$schema2.title|default:$schema2.name} {if $schema2.required|default:false}<a class="ui red empty circular label" title="Campo obbligatorio"></a>{/if}</label>
            <input type="text" name="{$schema2.pre}{$schema2.name}{$schema2.post}" placeholder="{$schema2.placeholder|default:''}" {if $schema2.required|default:false}required{/if} value="{$props[$schema2.name]}" />
        </div>
            {/if}
        {/if}
    </div>
    
    
    {/if}
    
    
</div>