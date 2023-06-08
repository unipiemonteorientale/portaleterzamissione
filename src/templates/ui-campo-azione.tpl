<style>
.selectedx { border: 2px solid red !important; }
.notselectedx { border: 2px solid #CCC !important; }

.selectedx .image.center { background: #fcc !important; }
</style>


<div class="field {$schema.css_class}" style="margin: 2px;">
    {if $style == "horizontal"}
        <div class="ui horizontal basic primary label">{$schema.title|default:$schema.name}</div> {$list[$props.value]}
    {elseif $action_name == "print"}
        <div class="field_print_value">{$props.value}) {$list[$props.value]}</div>
        <span class="field_print_label">{$schema.title|default:$schema.name}</span>
    {elseif $action_name == "read"}
        <label class="field_read_label">{$schema.title|default:$schema.name}</label>
        {*<div class="field_read_value">{$props.value|default:"&nbsp;"}</div>*}
        <div class="ui three stackable cards">
            <div class="card campo_{$props.value} selectedx">
                <div class="image center" style="padding: 3px; ">
                    <a class="ui red circular massive label">{$props.value}</a>
                </div>
                <div class="content">
                    {$list[$props.value]} <span onclick="mostraHelp('x', '{$props.value}');"><i class='question circle icon pointer'></i></span>
                </div>
            </div>
        </div>
    {else}
    
<script>
$(document).ready(function() {
    {*$('.ui.search.selection.dropdown').dropdown({ 
        {if $action_name == 'search'}
        clearable: true,
        forceSelection: false,
        ignoreDiacritics: true
        {/if}
    });*}
});
function changeCampoAzione(ca) {
    {*/*ca = 'g';
    
    var msg = {
        result:true,
        level:'INFO', 
        description:"Attualmente è configurato solo il campo d'azione g) Attività di public engagement. Gli altri campi d'azione saranno disponibili prossimamente."
    };
    ShowMessage(msg, false, function() {
        
    });*/*}
    
	$(".card").removeClass('selectedx').addClass('notselectedx');
	$(".card.campo_"+ca).removeClass('notselectedx').addClass('selectedx');
	
	
	$(".circular.massive.label").removeClass('red').addClass('grey');
	$(".card.campo_"+ca+" .circular.massive.label").removeClass('grey').addClass('red');
    
    $(".card.campo_"+ca+" input[type=radio]").prop('checked',true);
}
</script>



<div class="field">
    {if !$schema.no_label|default:false}
    <label>{$schema.title|default:$schema.name} {if $schema.required|default:false}<a class="ui red empty circular label" title="Campo obbligatorio"></a>{/if}</label>
    {/if}
    
    <div class="ui three stackable cards">
    {foreach name="ca" key=key item=item from=$list}
    <div class="card campo_{$key} {if $key == $props.value|default:'g'}selectedx{else}notselectedx{/if}" onclick="changeCampoAzione('{$key}');">
        <div class="image center" style="padding: 3px; ">
            <a class="ui {if $key == $props.value|default:'g'}red{else}grey{/if} circular massive label" onclick="mostraHelp('x', '{$key}');">{$key}</a>
        </div>
        <div class="content">
            
            <div class="ui radio {*read-only*} checkbox">
                <input type="radio" name="{$schema.pre}{$schema.name}{$schema.post}" value="{$key}" {if $key == $props.value|default:'g'}checked="checked"{/if} onclick="changeCampoAzione('{$key}');" />
                <label><h3>{$item} <span onclick="mostraHelp('x', '{$key}');"><i class='question circle icon pointer'></i></span></h3></label>
            </div>
        </div>
    </div>
    
    
    {/foreach}
    </div>
</div>
    
    {/if}
    

</div>

