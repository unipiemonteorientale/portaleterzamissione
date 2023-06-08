<div class="field" style="margin: 2px;">
    {if $style == "horizontal"}
        <div class="ui horizontal basic primary label">{$schema.title|default:$schema.name}</div> {$list[$props.value]}
        
    {elseif $action_name == "readx"}
        <label class="field_read_label">{$schema.title|default:$schema.name}</label>
        <div class="field_read_value">{$props.value|default:"&nbsp;"}</div>
    {else}
    
<script>
$(document).ready(function() {
    
});
</script>

    <div id="div_uielement_{$schema.name}">
        {if !$schema.no_label|default:false}
        <label>{$schema.title|default:$schema.name} {if $schema.required|default:false}<a class="ui red empty circular label" title="Campo obbligatorio"></a>{/if}</label>
        {/if}
            
        {foreach name="sm" key=key item=item from=$risposte}
        <div id="div_selection_item_{$item.codice_risposta}{$schema.name}{$schema.post}">
            <input type="hidden" name="{$item.codice_risposta}_code{$schema.post}" value="{*$schema.pre*}{$item.code_risposta}" />
            
            <div class="two fields" style="border-bottom: 0px solid #AAA; border-radius: 0px; padding: 0px;">
                <div class="field">
                    {if $action_name == 'print'}
                    <div class="ui checkbox">
                        {if $item.risposta_selezionata == $item.codice_risposta}&#10004; <label>{$item.testo_risposta}</label>{*else&#8226;*}{/if}
                        
                    </div>
                    {else}
                    <div class="ui {if $item.risposta_selezionata == $item.codice_risposta}checked{/if} {if $action_name == 'read'}read-only{/if} checkbox">
                        <input type="checkbox" {if $item.risposta_selezionata == $item.codice_risposta}checked=""{/if} name="{$item.codice_risposta}_codice_risposta" value="{$item.codice_risposta}" onclick="$('#div_selection_item_{$item.codice_risposta}{$schema.name}{$schema.post} .field.campo_aggiuntivo').toggleClass('disabled');"/>
                        <label>{$item.testo_risposta}</label>
                    </div>
                    {/if}
                </div>
                
                {if $item.campo_aggiuntivo_tipo|count_characters}
                <div class="field {if $item.risposta_selezionata != $item.codice_risposta}disabled{/if} campo_aggiuntivo">
                
                    {if $action_name == 'print'}
                        {if $item.risposta_selezionata == $item.codice_risposta}
                            {if $item.campo_aggiuntivo_label|count_characters}<span>{$item.campo_aggiuntivo_label}</span>{/if}
                            {if $item.supplement1|count_characters}<p>{$item.supplement1}</p>{/if}
                        {/if}
                    {else}
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
                    {/if}
                </div>
                {/if}
            </div>
            {if !$smarty.foreach.sm.last}
            <div class="ui fitted divider"></div>
            {/if}
        </div>
        {/foreach}
    
    </div>
    {/if}
    
    
</div>