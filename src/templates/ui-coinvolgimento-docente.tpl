<style>
</style>

<script>

var codice_attivita = '{$options.code_attivita|default:$options.record_code}';

$(document).ready(function() {  

    /*$('.ui.dropdown.personale.uniupo.{$schema.name}').dropdown({
        clearable: true,
        ignoreCase: true,
        ignoreDiacritics: true,
        fullTextSearch: 'exact',
        preserveHTML: false,
        minCharacters: 2,
        apiSettings: {
            url: "{$BASE_URL}/api/wizard/personale/{literal}{query}{/literal}"
        },
        saveRemoteData: false,
        onChange: function(value, text, $selectedItem) {
            //$("#matricola").val(value);
            console.log(value);
            $('.ui.dropdown.personale.uniupo.{$schema.name}').dropdown("set value", ('000000'+value).slice(-6));
        }
});*/
    $('.ui.dropdown.coinvolgimento.uniupo.{$schema.name}').dropdown({ 
        onChange: function(value, text, $choice) {
            console.log(value, text, $choice);
            var code = parseInt(value.slice(0, 3));
            console.log(value, value.slice(0, 3), code);
            if (code >=200)
                $(".specificare.altro.coinvolgimento").removeClass("invisible");
            else {
                $(".specificare.altro.coinvolgimento").addClass("invisible");
                $(".specificare.altro.coinvolgimento input").val('');
            }
                
        }
    });

    init_coinvolgimento_docente();
    
    
});
function init_coinvolgimento_docente() {
    
    $.ajax({
        type: 'get',
        url: '{$BASE_URL}/api/wizard/coinvolgimento-docente/'+codice_attivita,
        success: function () { }
    })
    .done(function( data ) {
        if (data.success) {
            values = data.results;
            //$('.ui.dropdown.coinvolgimento.uniupo.{$schema.name}').dropdown("set menu", data.results);
            $('.ui.dropdown.coinvolgimento.uniupo.{$schema.name}').dropdown("change values", values);
            
            {if $props.value|count_characters}
            $('.ui.dropdown.coinvolgimento.uniupo.{$schema.name}').dropdown("set selected", '{$props.value}');
            {/if}
        }
    });   
}
</script>

{*foreach item=item key=key from=$props}
    {$key} | {$item}<br>
{/foreach}
{foreach item=item key=key from=$schema}
    {$key} | {$item}<br>
{/foreach*}


<div class="field {$schema.css_class}">
    <label>{$schema.label1_code} {if $schema.required|default:false}<a class="ui red empty circular label" title="Campo obbligatorio"></a>{/if}</label>
    <div class="ui fluid coinvolgimento uniupo {$schema.name} selection dropdown">
    
        <input type="hidden" name="{$schema.name}" value="{$props.value}">
        
        <!--i class="dropdown icon"></i-->
        <div class="default text">Indicare il coinvolgimento del docente nell'attività</div>
        <div class="menu">
        </div>
    </div>
</div>
<div class="field {$schema.css_class} specificare altro coinvolgimento invisible">
    <label>{$schema.label2_code}</label>
    <div class="ui input">
        <input type="text" placeholder="" name="{$schema.dbfield2}" value="{$props.value2}" />
    </div>
</div>