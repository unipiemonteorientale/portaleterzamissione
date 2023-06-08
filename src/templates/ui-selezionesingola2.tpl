<div class="field" style="margin: 2px;">
    {if $style == "horizontal"}
        <div class="ui horizontal basic primary label">{$schema.title|default:$schema.name}</div> {$list[$props.value]}
    {elseif $action_name == "print"}
        <div class="field_print_value">{$props.value|default:"&nbsp;"}</div>
        <span class="field_print_label">{$schema.title|default:$schema.name}</span>
    {elseif $action_name == "readx"}
        <label class="field_read_label">{$schema.title|default:$schema.name}</label>
        {if !$schema.no_label|default:false}<div class="field_read_value">{$props.value|default:"&nbsp;"}</div>{/if}
    {else}
    
<script>
$(document).ready(function() {
    $('.ui.search.selection.dropdown.{$schema.codice_domanda}').dropdown({ 
        {if $action_name == 'search'}
        clearable: true,
        forceSelection: false,
        ignoreDiacritics: true
        {/if}
        
        onChange: function(value, text, $selectedItem) {
            console.log('{$schema.codice_domanda}', 'onchange', value);
            var myevent = new CustomEvent(
                '{$schema.codice_domanda}', { 
                    detail: {
                        domanda: '{$schema.codice_domanda}', risposta: value
                    },
                    bubbles: true,
                    cancelable: true
                }
            );
            document.dispatchEvent(myevent);
        }
    });
    
    
});
</script>
    
    <div class="two fields">
        <div class="field">
            {if !$schema.no_label|default:false}
            <label>{$schema.title|default:$schema.name} {if $schema.required|default:false}<a class="ui red empty circular label" title="Campo obbligatorio"></a>{/if}</label>
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
            
            {else}
                {*<div class="ui search {if $action_name == 'read'}read-only{else}clearable{/if} selection dropdown {$schema.codice_domanda}">
                    <input type="hidden" name="{$schema.pre}{$schema.name}{$schema.post}" value="{$props[$schema.name]}" {if $schema.required|default:false}required{/if}>
                    <i class="dropdown icon"></i>
                    <div class="default text">{$list[$props.value]|default:$schema.placeholder}</div>
                    <div class="scrollhint menu">
                        {if !$schema.required|default:false}<div class="item" data-value=""></div>{/if}
                        {foreach key=key item=item from=$list}
                        <div class="item" data-value="{$item.code}">{$item.label}</div>
                        {/foreach}
                    </div>
                </div>*}
            {foreach name="sm" key=key item=item from=$risposte}
            <div id="div_selection_item_{$item.codice_risposta}{$schema.name}{$schema.post}">
                <input type="hidden" name="{$item.codice_risposta}_code{$schema.post}" value="{*$schema.pre*}{$item.code_risposta}" />
                
                <div class="two fields" style="border-bottom: 0px solid #AAA; border-radius: 0px; padding: 5px;">
                    <div class="field">
                        <div class="ui {if $item.risposta_selezionata == $item.codice_risposta}checked{/if} {if $action_name == 'read'}read-only{/if} checkbox">
                            <input type="checkbox" {if $item.risposta_selezionata == $item.codice_risposta}checked=""{/if} name="{$item.codice_risposta}_codice_risposta" value="{$item.codice_risposta}" onclick="$('#div_selection_item_{$item.codice_risposta}{$schema.name}{$schema.post} .field.campo_aggiuntivo').toggleClass('disabled');"/>
                            <label>{$item.testo_risposta}</label>
                        </div>
                    </div>
                    
                    {if $item.campo_aggiuntivo_tipo|count_characters}
                    <div class="field {if $item.risposta_selezionata != $item.codice_risposta}disabled{/if} campo_aggiuntivo">
                        {if $item.campo_aggiuntivo_label|count_characters}<span>{$item.campo_aggiuntivo_label}</span>{/if}
                            {if $item.campo_aggiuntivo_tipo == 'INT'}
                        <div class="ui labeled input">
                            <div class="ui label"><i class="icon calculator"></i></div>
                            <input type="number" oninput="this.value = Math.round(this.value);" name="{$item.codice_risposta}_supplement1{$item.post}" placeholder="{$item.placeholder|default:''}" {if $item.required|default:false}required{/if} value="{$item.supplement1}" {if $action_name == 'read'}readonly=''{/if} />
                        </div>
                            {elseif $item.campo_aggiuntivo_tipo == 'DECIMAL'}
                        <div class="ui labeled input">
                            <div class="ui label"><i class="ui icon calculator"></i></div>
                            <input type="number" step="0.01" name="{$item.codice_risposta}_supplement1{$item.post}" placeholder="{$item.placeholder|default:''}" {if $item.required|default:false}required{/if} value="{$item.supplement1}" {if $action_name == 'read'}readonly=''{/if} />
                        </div>
                            {elseif $item.campo_aggiuntivo_tipo == 'PERC'}
                        <div class="ui labeled input">
                            <div class="ui label"><i class="icon percentage"></i></div>
                            <input type="number" step="0.01" max="100" name="{$item.codice_risposta}_supplement1{$item.post}" placeholder="{$item.placeholder|default:''}" {if $item.required|default:false}required{/if} value="{$item.supplement1}" {if $action_name == 'read'}readonly=''{/if} />
                        </div>
                            {else}
                        <div class="ui labeled input">
                            <div class="ui label"><i class="icon pen"></i></div>
                            <input type="text" name="{$item.codice_risposta}_supplement1{$item.post}" placeholder="{$item.placeholder|default:''}" {if $item.required|default:false}required{/if} value="{$item.supplement1}" {if $action_name == 'read'}readonly=''{/if} />
                        </div>
                            {/if}
                    </div>
                    {/if}
                </div>
                {if !$smarty.foreach.sm.last}
                <div class="ui fitted divider"></div>
                {/if}
            </div>
            {/foreach}
            {/if}
        </div>
        
        {if $schema2}
        <div class="field">
            <label>{$schema2.title|default:$schema2.name} {if $schema2.required|default:false}<a class="ui red empty circular label" title="Campo obbligatorio"></a>{/if}</label>
            <input type="text" name="{$schema2.pre}{$schema2.name}{$schema2.post}" placeholder="{$schema2.placeholder|default:''}" {if $schema2.required|default:false}required{/if} value="{$props[$schema2.name]}" />
        </div>
        {/if}
    </div>
    
    
    {/if}
    
    
</div>