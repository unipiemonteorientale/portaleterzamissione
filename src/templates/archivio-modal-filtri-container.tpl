{extends file="template-modal-page.tpl"}

{block name="modal_content"}

<style>
</style>


<script>
//var params = {$json_params|default:''}; //JSON.parse('{$json_params}');
$(document).ready(function() {
    
    //fields = ["name","description"]
    fields = [
        {* 'name': 'anno', 'context': 'default', 'object': 'm3_attivita', 'value': '{$filtri.anno}' *},
        {* 'name': 'tipo_compilazione', 'context': 'default', 'object': 'm3_attivita', 'value': '' *},
        {* 'name': 'AP_9', 'context': 'AP', 'object': 'M3_DOMANDE', 'value': '' *},
        {* 'name': 'AP_7', 'context': 'AP', 'object': 'M3_DOMANDE', 'value': '' *}
    ];
    
    fields.forEach(function(item, index) {
        
        console.log(item);
        drawElement(item.name, item.context, item.object, item.value);
    });

    $("#actions_div_filters .clear.button").click(function() {
        console.debug("#actions_div_filters .clear.button click()");
        $("#web3filters_modal")[0].reset();
        document.getElementById("web3filters_modal").reset(); 
        $(':input', "#web3formfields_modal")
              .not(':button, :submit, :reset, :hidden')
              .val('')
              .prop('checked', false)
              .prop('selected', false);
		$('#web3filters_modal .dropdown').dropdown('clear');
    });

    $('#web3filters_modal').form({
        fields: {
            {foreach item=rule from=$rules}{$rule}{/foreach}
        },
        onSuccess: function(event, fields) {
            event.preventDefault();
            $.ajax({
                type: 'post',
                url: '{$BASE_URL}/api/archivio/filter',
                data: $(this).serialize(),
                success: function () {
                    //"ok" label on success.
                    //$('#successLabel').css("display", "block");
                    console.debug("form #web3filters_modal success sent.");
                }
            })
            .done(function( data ) {
                if (data.trim() == 'OK') {
                    //window.location = "/";
                    //alert('OK');
                    $("#actions_div_filters .ok.button").click();
                }
                else if (data == 'KO') {
                    $('.ui.error.message').html("Errore");
                    $('.ui.form').addClass('error'); //response.msg
                }
                else {
                    /*var msg = JSON.parse(data);
                    
                    if (msg['description'].length > 0) {
                        ShowMessage(msg, false, function() {
                            if (msg['result'])
                                $("#actions_div_{$modal_id} .ok.button").click();
                        });
                    }
                    else {
                        if (msg['result'])
                            $("#actions_div_{$modal_id} .ok.button").click();
                        else 
                            ShowMessage("{ result:false,level:'INFO', description:'Errore non specificato.' }", false);
                    } */
                    console.log(data);
					
                    $("#actions_div_filters .ok.button").click();
                }
            });      
        }
    });
    
    $('.ui.dropdown.personale.uniupo.matricola').dropdown({
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
            $('.ui.dropdown.personale.uniupo.matricola').dropdown("set value", matricola);
            init_ui_field_matricola_uniupo(matricola);
        }
    });
    
    $('.campo_azione.dropdown').dropdown({ 
        onChange: function(value, text, $choice) {
            console.log(value);
            $(".field.campo_azione").addClass("invisible");
            $(".field.campo_azione .ui.dropdown").dropdown("clear");
            $(".field.campo_azione.campo_"+value).removeClass("invisible");
        }
    }).dropdown("set selected", "{$filtri.campo_azione}");
    
    $('.anno.dropdown').dropdown("set selected", [{$filtri.anno}]);
    $('.strutture.dropdown').dropdown("set selected", [{$filtro_strutture}]);
    $('.AP_7.dropdown').dropdown("set selected", [{$filtro_agenda2030}]);
    $('.ssd.dropdown').dropdown("set selected", [{$filtro_ssd}]);
    
    {*
    $('.B_1.dropdown').dropdown({ });
    $('.G_6.dropdown').dropdown("set selected", [{$filtro_categoria_pe}]);
    *}
    
    {foreach item="filtro_speciale_list" key=key from=$filtri_specifici}
            
    $('.{$key}.dropdown').dropdown({ });
    
    {/foreach}

    init_ui_field_matricola_uniupo('{$filtri.matricola}');
});

function init_ui_field_matricola_uniupo(matricola) {
    if (matricola == '')
        return;
    
    $.ajax({
        type: 'get',
        url: '{$BASE_URL}/api/wizard/personale/'+matricola.slice(-6),
        success: function () { }
    })
    .done(function( data ) {
        if (data.success) {
            persona = data.results[0];
            console.log(persona);
            $('.ui.dropdown.personale.uniupo.matricola').dropdown("set text", persona.name);
        }
    });   
}

function drawElement(name, context, object, value) {
    $.ajax({
        type: 'get',
        url: '{$BASE_URL}/api/form/'+context+'/'+object+'/'+name+'/search?record_code='+value,
        //data: {},
        success: function () {
            elem = $("#web3filters_modal #"+name);
            if (elem.length == 0) {
                $("#web3formfields_{$modal_id}").append('<div class="ui active inline loader '+name+'"></div>');
            }
            else {
                elem.append('<div class="ui active inline loader"></div>');
            }
        }
    })
    .done(function( data ) {
        elem = $("#web3filters_modal #"+name);
        data = data.replace("ui segment", "").replace("container_domanda", "");
        if (elem.length == 0) {
            $("#web3formfields_{$modal_id}").find(".active.loader."+name).removeClass("active");
            $("#web3formfields_{$modal_id}").append("<div id='"+name+"'>"+data+"</div>");
        }
        else {
            elem.find(".active.loader").removeClass("active");
            elem.html(data);
            if (params[name]) {
                elem.find("[name='"+name+"']").val(params[name]);
            }
        }
    });      
}
</script>



<form id="web3filters_modal" class="ui form">
    
    <div id="web3formfields_modal" class="web3formfields">
        <div class="four fields">
            <div class="field" id="anno">
                <label>Anno</label>
                <select name="anno" class="ui fluid anno dropdown">
                    <option value=""></option>
                    {foreach item=item from=$anni}<option value="{$item.anno}" {if $filtri.anno == '{$item.anno}'}selected{/if}>{$item.anno}</option>{/foreach}
                </select>
            </div>
            <div class="field" id="campo_azione">
                <label>Campo d'azione</label>
                <select name="campo_azione" class="ui fluid campo_azione dropdown">
                    <option value=""></option>
                    {foreach item=item from=$campi_azione}<option value="{$item.codice}" {if $filtri.campo_azione == '{$item.codice}'}selected{/if}>{$item.etichetta}</option>{/foreach}
                </select>
            </div>
            <div class="field" id="tipo_compilazione">
                <label>Tipologia di compilazione</label>
                <select name="tipo_compilazione" class="ui fluid tipo_compilazione dropdown">
                    <option value=""></option>
                    <option value="B" {if $filtri.tipo_compilazione == 'B'}selected{/if}>Base</option>
                    <option value="V" {if $filtri.tipo_compilazione == 'V'}selected{/if}>Valutazione</option>
                </select>
            </div>
            <div class="field" id="stato">
                <label>Stato attività</label>
                <select name="stato" class="ui fluid stato dropdown">
                    <option value=""></option>
                    <option value="V" {if $filtri.stato == 'V'}selected{/if}>Validata</option>
                    <option value="B" {if $filtri.stato == 'B'}selected{/if}>Bozza</option>
                </select>
            </div>
        </div>
        
        {if !$operatore->has("DOCENTE")}
        <div class="three fields">
            <div class="field" id="strutture">
                <label>Struttura di appartenenza</label>
                <select name="struttura[]" multiple="" class="ui fluid strutture dropdown">
                    <option value=""></option>
                    {foreach item=item from=$strutture}<option value="{$item.codice}">{$item.etichetta}</option>{/foreach}
                </select>
            </div>
            
            <div class="field">
                <label>SSD</label>
                <select name="ssd[]" multiple="" class="ui fluid ssd dropdown">
                    <option value=""></option>
                    {foreach item=item from=$SSD}<option value="{$item.codice}">{$item.etichetta}</option>{/foreach}
                </select>
            </div>
            
            <div class="field">
                <label>Docente UNIUPO</label>
                <div class="ui fluid personale uniupo matricola search selection dropdown">
                    <input type="hidden" name="matricola" id="matricola" value="">
                    <div class="default text">Selezionare tra il personale UNIUPO</div>
                    <div class="menu"></div>
                </div>
            </div>
        </div>
        {/if}
            
        <div class="field">
            <label>Parole chiave</label>
            <div id="AP_9" class="five fields">
                <div class="field">
                    <label>Parola #1</label>
                    <input name="parola1" value="{$filtri.parola1}" />
                </div>
                <div class="field">
                    <label>Parola #2</label>
                    <input name="parola2" value="{$filtri.parola2}" />
                </div>
                <div class="field">
                    <label>Parola #3</label>
                    <input name="parola3" value="{$filtri.parola3}" />
                </div>
                <div class="field">
                    <label>Parola #4</label>
                    <input name="parola4" value="{$filtri.parola4}" />
                </div>
                <div class="field">
                    <label>Parola #5</label>
                    <input name="parola5" value="{$filtri.parola5}" />
                </div>
            </div>
        </div>
    
        <div class="two fields">
            <div class="field">
                <label>{$AP_7.0.testo_domanda}</label>
                <select name="agenda2030[]" multiple="" class="ui fluid AP_7 dropdown">
                    <option value=""></option>
                    {foreach item=item from=$AP_7}<option value="{$item.codice_risposta}">{$item.testo_risposta}</option>{/foreach}
                </select>
            </div>
        </div>
        
            
        <div style="background: #DEDEDE; padding: 2px 10px;">
            <h4>Filtri specifici del campo di azione selezionato</h4>
            
            {foreach item="filtro_speciale_list" key=key from=$filtri_specifici}
            
            <div class="field campo_azione campo_{$filtro_speciale_list.0.sezione|lower} invisible">
                <label>{$filtro_speciale_list.0.testo_domanda}</label>
                <select name="filtro_speciale_{$filtro_speciale_list.0.sezione|lower}[]" multiple="" class="ui fluid {$key} dropdown">
                    <option value=""></option>
                    {foreach item=item from=$filtro_speciale_list}<option value="{$item.codice_risposta}">{$item.testo_risposta}</option>{/foreach}
                </select>
            </div>
            
            {/foreach}
            
            
        </div>
    </div>
    
    
    <button class="ui button invisible" type="submit" id="btn_web3filters_modal_submit">Submit</button>
</form>




{/block}





{block name="modal_actions_footer"}
<div id="actions_div_filters">

	<div class="ui ok button invisible">Hidden</div>
    <div class="ui gray search button" onclick="$('#btn_web3filters_modal_submit').click();">Ricerca</div>
	
    <div class="ui gray clear button">Azzera filtri</div>
    <div class="ui cancel button">Chiudi</div>

</div>
{/block}