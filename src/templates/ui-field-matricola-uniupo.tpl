<style>
</style>

<script>

$(document).ready(function() {  

    $('.ui.dropdown.personale.uniupo.{$schema.name}').dropdown({
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
            var matricola = ('000000'+value).slice(-6);
            $('.ui.dropdown.personale.uniupo.{$schema.name}').dropdown("set value", matricola);
            init_ui_field_matricola_uniupo(matricola);
        }
    });

    {if $props.value|count_characters}init_ui_field_matricola_uniupo('000000{$props.value}');{/if}
});
function init_ui_field_matricola_uniupo(matricola) {
    
    $.ajax({
        type: 'get',
        url: '{$BASE_URL}/api/wizard/personale/'+matricola.slice(-6),
        success: function () { }
    })
    .done(function( data ) {
        if (data.success) {
            persona = data.results[0];
            console.log(persona);
            $('.ui.dropdown.personale.uniupo.{$schema.name}').dropdown("set text", persona.name);
            $("input[name='codice_ssd']").val(persona.ssd);
            $("input[name='codice_struttura']").val(persona.struttura);
        }
    });   
}
</script>




<div class="field {$schema.css_class}">
    <label>{$schema.label1_code} {if $schema.required|default:false}<a class="ui red empty circular label" title="Campo obbligatorio"></a>{/if}</label>
    <div class="ui fluid personale uniupo {$schema.name} search selection dropdown">
    
        <input type="hidden" name="{$schema.name}" id="{$schema.name}_matricola" value="{$props.value}">
        
        <!--i class="dropdown icon"></i-->
        <div class="default text">Selezionare tra il personale UNIUPO</div>
        <div class="menu">
        </div>
    </div>
</div>