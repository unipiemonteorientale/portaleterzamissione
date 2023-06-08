{extends file="template-modal-page.tpl"}

{block name="modal_content"}

<style>
.container_domanda {
    background: #dedede !important; 
    margin: 3px 0px;
}
th { 
    color: white !important;
    background: var(--colorPrimary) !important; 
}
</style>


<script>
var step = 1;
var attivita_code = '{$record_code}';
var form_changed = false;

function loadWizardPage(_step, _scrolltop, _scrollto) {
    
    if (typeof _step === 'undefined') 
        _step = step;
    
    if (typeof _scrolltop === 'undefined') 
        _scrolltop = false;
    
    if (typeof _scrollto === 'undefined') 
        _scrollto = false;
    
    if (_step != 99)
        step = _step;
    
    var res = true;
    if (form_changed == true) {
        res = confirm("Ci sono dati inseriti o modificati non salvati che verranno persi, vuoi proseguire?");
    }
    
    if (res) {
        elem = $("#wizard_div_{$modal_id}");
        $.ajax({
            type: 'get',
            url: '{$APP.url}/wizard/{$action_name}/step-'+_step+'?record_code='+attivita_code,
            //url: '{$BASE_URL}/ui/default/m3_attivita/insert',
            data: { modal_id: '{$modal_id}' },
            success: function () {
                elem.append('<div class="ui active inline loader"></div>');
            }
        })
        .done(function( data ) {
            if (_scrolltop || _scrollto !== false)
                $(".scrolling.content").scrollTop( 0 );
             
            elem.html(data).promise().done(function(){
                //your callback logic / code here
                console.log('promise done!');
                if (_step <= 1)
                    $(".back.button").addClass("invisible");
                else
                    $(".back.button").removeClass("invisible");
                if (_step >= 6)
                    $("#btn_wizard_salva_prosegui").addClass("invisible");
                else
                    $("#btn_wizard_salva_prosegui").removeClass("invisible");
                
                if (_scrollto !== false) {
                    console.log("scrolling.content", $(".scrolling.content").scrollTop() );
                    
                    //$(".scrolling.content").scrollTop( 500 );
                    //window.scrollTo(0, $(_scrollto).offset().top);
                    //elem.scrollTop( $(_scrollto).offset().top); 
                    var current_top = $(_scrollto).offset().top;
                    var x = _scrollto.split('_');
                    var sezione = x[2];
                    var domanda = sezione+'_'+x[3];
                    console.log(sezione, domanda);
                    
                    $(".scrolling.content").animate({
                        scrollTop: $(_scrollto).offset().top - $("#segnaposto_prima_domanda").offset().top 
                    },'slow');
                    for (let i = 1; i <= 20; i++) {
                        if ($("#div_domanda_A_"+parseInt(i)))
                            console.log("A_"+parseInt(i), $("#div_domanda_"+sezione+"_"+parseInt(i)).offset());
                    } 
                } 
            
                form_changed = false;
                $("#step").val(_step);
                
                $("#wizard_steps .active.step").removeClass("active");
                $("#wizard_step"+step).addClass("active");
                
                $(":input").change(function() {
                    
                    console.log('change');
                    form_changed = true;
                    
                });
            });

        }); 
    }
    /*
    if (form_changed == false) {
        var msg = {
            'level': "WARNING",
            'descritption': "Ci sono dati inseriti o modificati non salvati."
        };
        ShowMessage(msg, false, funcOK=function() {
            
            
        }, funcKO=function() {
            
        });
    }*/
      
}

function sendTrigger(codice_domanda, valore_risposta) {
	var myevent = new CustomEvent(
		codice_domanda, { 
			detail: {
				domanda: codice_domanda, risposta: valore_risposta
			},
			bubbles: true,
			cancelable: true
		}
	);
    console.log("sendTrigger", codice_domanda, valore_risposta);
	document.dispatchEvent(myevent);
}


$(document).ready(function() {
    loadWizardPage(1);
    
    $('#m3wizardform_{$modal_id}').form({
        fields: {
            {foreach item=rule from=$rules}{$rule}{/foreach}
        },
        onSuccess: function(event, fields) {
            event.preventDefault();
            $.ajax({
                type: 'post',
                url: '{$APP.url}/wizard/ui/{$context_name}/{$object_name}/{$action_name}?code_attivita='+attivita_code,
                data: $('form').serialize(),
                success: function () {
                    //"ok" label on success.
                    //$('#successLabel').css("display", "block");
                    console.debug("form #m3wizardform_{$modal_id} success sent.");
                }
            })
            .done(function( data ) {
                if (data.trim() == 'OK') {
                    /*
                    var msg = {
                        result:true,
                        level:'INFO', 
                        description:'Dati salvati, proseguiamo con lo step successivo.'
                    };
                    ShowMessage(msg, false, function() {
                        console.log('vado allo step successivo');
                        step = step + 1;
                        form_changed = false;
                        loadWizardPage(step, true);
                    });*/
                    console.log('vado allo step successivo');
                    step = step + 1;
                    form_changed = false;
                    loadWizardPage(step, true);
                }
                else if (data == 'KO') {
                    $('.ui.error.message').html("Errore");
                    $('.ui.form').addClass('error'); //response.msg
                }
                else if (data == 'FINE') {
                    step = step + 1;
                    form_changed = false;
                    loadWizardPage(99, true);
                }
                else {
                    //console.log(data);
                    var msg = JSON.parse(data);
                    
                    if (msg['result']) {
                        form_changed = false;
                        if (msg['customs']) {
                            var cust = msg['customs'];
                            if (cust.record_code)
                                attivita_code = cust.record_code;
                            console.log("attivita_code", attivita_code);
                            if (cust.campo_azione)
                                $("#header_tipo_campo_azione").text(cust.campo_azione.toUpperCase());
                        }
                        if (msg['description'].length > 0) {
                            ShowMessage(msg, false, function() {
                                step = step + 1;
                                loadWizardPage(step);
                            });
                        }
                        else {
                            step = step + 1;
                            loadWizardPage(step);
                        }
                    }
                    else {
                        if (msg['description'].length > 0) {
                            ShowMessage(msg, false);
                        }
                        else
                            ShowMessage("{ result:false,level:'ERROR', description:'Errore non specificato.' }", false);
                    }
                    return false;
                }
            });      
        }
    });
    
    
    
    
    $("#actions_div_{$modal_id} .save.button").click(function() {
        console.debug("#actions_div_{$modal_id} .save.button click()");
        //$("#m3wizardform_{$modal_id}").submit();
        $("#m3wizardform_{$modal_id}_button_submit").click();
    });
    
    $("#actions_div_{$modal_id} .back.button").click(function() {
        console.debug("#actions_div_{$modal_id} .back.button click()");
        step = step - 1;
        loadWizardPage(step);
    });
    
    $("#actions_div_{$modal_id} #btn_wizard_chiudi").click(function() {
        var res = true;
        if (form_changed == true) {
            res = confirm("Ci sono dati inseriti o modificati non salvati che verranno persi, vuoi proseguire?");
        }
        
        if (res) 
            $("#actions_div_{$modal_id} .cancel.button").click();
    });
    
    
    
    
    $("#actions_div_{$modal_id} .delete.button").click(function() {
        console.debug("#actions_div_{$modal_id} .delete.button click()");
        $("#m3wizardform_{$modal_id} #delete").val('S');
        $("#m3wizardform_{$modal_id}_button_submit").click();
    });
    
    
    $("#actions_div_{$modal_id} .clear.button").click(function() {
        console.debug("#actions_div_{$modal_id} .clear.button click()");
        $("#m3wizardform_{$modal_id}")[0].reset();
        document.getElementById("m3wizardform_{$modal_id}").reset(); 
        $(':input', "#m3wizardform_{$modal_id}")
              .not(':button, :submit, :reset, :hidden')
              .val('')
              .prop('checked', false)
              .prop('selected', false);
    });
    
});

function testPOST(form_id) {
    event.preventDefault();
    $.ajax({
        type: 'post',
        url: '{$BASE_URL}/test/post',
        data: $('#'+form_id).serialize(),
        success: function () {
            //"ok" label on success.
            //$('#successLabel').css("display", "block");
            console.debug("form #"+form_id+" success sent.");
        }
    })
    .done(function( data ) {
        console.log(data);
    });      
}

function refreshDomanda(domanda_code) {
    $.ajax({
        type: 'get',
        url: '{$APP.url}/wizard/{$action_name}/domanda-'+domanda_code+'?record_code='+attivita_code,
        data: { modal_id: '{$modal_id}' },
        success: function () {
            elem.append('<div class="ui active inline loader"></div>');
        }
    })
    .done(function( data ) {
        $( "#div_domanda_"+domanda_code ).replaceWith( data );
    });      
}


function annullaValidazione() {
    $.ajax({
        type: 'get',
        url: '{$BASE_URL}/api/wizard/annulla-validazione/'+attivita_code,
        //data: { modal_id: '{$modal_id}' },
        success: function () {
            elem.append('<div class="ui active inline loader"></div>');
        }
    })
    .done(function( data ) {
        if (data == 'OK') {
            $("#actions_div_{$modal_id} #btn_wizard_chiudi").click();
            var msg = {
                result:true,
                level:'INFO', 
                description:'Validazione annullata.'
            };
            ShowMessage(msg, false, function() {
                return true;
            });
        }
        else {
            var msg = {
                result:false,
                level:'ERROR', 
                description:'Impossibile annullare la validazione.'
            };
            ShowMessage(msg, false);
        }
    });      
}
</script>

{if $operatore->has("debug")}
{/if}

<form id="m3wizardform_{$modal_id}" class="ui form">
    <div id="wizard_div_{$modal_id}"></div>
        
    {*if $action_name == "insert" || $action_name == "update" || $action_name == "clone"}
    <p>
        <span class="ui red empty circular label"></span> Campi obbligatori.
    </p>
    <input type="{if $operatore->has('debug')}text{else}hidden{/if}" name="model_key_name" value="{$model_key_name}" readonly="" />
    <input type="{if $operatore->has('debug')}text{else}hidden{/if}" name="model_key_value" value="{$model_key_value}" readonly="" />
    {if $action_name == "clone"}
    <input type="hidden" name="clone" value="S" />
    {/if}
    <input type="hidden" name="delete" value="N" id="delete" />
    
    {/if*}
    <input type="hidden" name="step" value="{$step}" id="step" />
    <button id="m3wizardform_{$modal_id}_button_submit" type="submit" class="ui button invisible">Salva</button>
</form>
{/block}





{block name="modal_actions_footer"}
<div id="actions_div_{$modal_id}">
    
    {if $operatore->has("debug")}(archivio-modal-wizard-container.tpl)
    <div class="ui pink button" onclick="testPOST('m3wizardform_{$modal_id}');">Test POST</div>
    {/if}
    
    <a class="ui violet button" href="{$APP.url}/report/pdf/{$record_code}" target="_blank" >Stampa</a>
    
    <div class="ui ok button invisible">Hidden</div>
    <div class="ui cancel button invisible">Hidden</div>
    {if $action_name == "search"}
    <div class="ui blue search button">Ricerca</div>
    <div class="ui pink clear button">Pulisci</div>
    {elseif $action_name == "delete"}
    <div class="ui red delete button">Elimina</div>
    {elseif $action_name != "read"}
    <div class="ui grey back button invisible">Indietro</div>
    {*<div class="ui blue button" onclick="loadWizardPage(step);">Ricarica</div>*}
    <div class="ui green save button" id="btn_wizard_salva_prosegui">Salva e prosegui</div>
    {/if}
    {if $delete == 1}
    <div class="ui red delete button">Elimina</div>
    {/if}
    {if $action_name == "read" && ($operatore->has("ATENEO") || $operatore->has("DIPARTIMENTO"))}
    <div class="ui button" onclick="annullaValidazione();">Annulla validazione</div>
    {/if}
    {if $action_name != "readx"}
    <div class="ui button" id="btn_wizard_chiudi">Chiudi</div>
    {/if}
</div>
{/block}