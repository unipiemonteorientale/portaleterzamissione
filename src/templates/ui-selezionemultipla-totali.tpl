<div class="field" style="margin: 2px;">
    {if $style == "horizontal"}
        <div class="ui horizontal basic primary label">{$schema.title|default:$schema.name}</div> {$list[$props.value]}
    {elseif $action_name == "print"}
        <div class="field_print_value">{$props.value|default:"&nbsp;"}</div>
        <span class="field_print_label">{$schema.title|default:$schema.name}</span>
    {elseif $action_name == "readx"}
        <label class="field_read_label">{$schema.title|default:$schema.name}</label>
        <div class="field_read_value">{$props.value|default:"&nbsp;"}</div>
    {else}
    
<script>
$(document).ready(function() {
    {$schema.codice_domanda}_func_totali();
    $( "#div_uielement_{$schema.codice_domanda} [type=number]" ).each(function( index ) {
        $( this ).change(function() {
            {$schema.codice_domanda}_func_totali();
        });
    });
});
function {$schema.codice_domanda}_func_totali() {
    console.log("TOTALI {$schema.codice_domanda}");
    var totale = 0;
    $( "#div_uielement_{$schema.codice_domanda} :input[type=number]" ).each(function( index ) {
        var valore = $( this ).val();
        console.log(valore);
        if (valore.length)
            totale = totale + parseInt(valore);
    });
    console.log(totale);
    $( "#div_uielement_{$schema.codice_domanda} .totale" ).html(totale);
}
function {$schema.codice_domanda}_func_disable(item) { 
    $('#div_selection_item_'+item+'{$schema.name}{$schema.post} .field.campo_aggiuntivo').toggleClass('disabled');
    $('#div_selection_item_'+item+'{$schema.name}{$schema.post} .field.campo_aggiuntivo :input').val('');
    {$schema.codice_domanda}_func_totali();
}
</script>

    <div id="div_uielement_{$schema.codice_domanda}">
        {if !$schema.no_label|default:false}
        <label>{$schema.title|default:$schema.name} {if $schema.required|default:false}<a class="ui red empty circular label" title="Campo obbligatorio"></a>{/if}</label>
        {/if}
            
        {foreach name="sm" key=key item=item from=$risposte}
        <div id="div_selection_item_{$item.codice_risposta}{$schema.name}{$schema.post}">
            <input type="hidden" name="{$item.codice_risposta}_code{$schema.post}" value="{*$schema.pre*}{$item.code_risposta}" />
            
            <div class="two fields" style="border-bottom: 0px solid #AAA; border-radius: 0px; padding: 5px;">
                <div class="field">
                    <div class="ui {if $item.risposta_selezionata == $item.codice_risposta}checked{/if} {if $action_name == 'read'}read-only{/if} checkbox">
                        <input type="checkbox" {if $item.risposta_selezionata == $item.codice_risposta}checked=""{/if} name="{$item.codice_risposta}_codice_risposta" value="{$item.codice_risposta}" onclick="{$schema.codice_domanda}_func_disable('{$item.codice_risposta}');"/>
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
                        <input type="number" step="0.01" name="{$item.codice_risposta}_supplement1{$item.post}" placeholder="{$item.placeholder|default:''}" {if $item.required|default:false}required{/if} value="{$item.supplement1}" {if $action_name == 'read'}readonly=''{/if} />
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
            
            {if $smarty.foreach.sm.last}
            <div class="ui bottom attached grey inverted fitted segment">
                <div class="two fields" style="border-bottom: 0px solid #AAA; border-radius: 0px; padding: 5px;">
                    <div class="field">
                        <h1>TOTALI</h1>
                    </div>
                    
                    <div class="field">
                        <h1 class="totale"></h1>
                    </div>
                </div>
            </div>
            {/if}
        </div>
        {/foreach}
    
    </div>
    {/if}
    
    
</div>