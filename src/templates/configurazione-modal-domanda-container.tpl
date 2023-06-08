{extends file="template-modal-page.tpl"}



{block name="modal_header"}

<div class="header" style="padding: 0.50rem 1.25rem;">
    <center>
    {$codice_domanda}
    <div class="ui mini steps" id="config_sections">
      <a class="active step" onclick="loadConfigPage('domanda', true);" id="config_section_domanda">
        <!--i class="truck icon"></i-->
        <div class="content">
          <div class="title">Domanda</div>
        </div>
      </a>
      <a class="step" onclick="loadConfigPage('risposte', true);" id="config_section_risposte">
        <div class="content">
          <div class="title">Risposte</div>
        </div>
      </a>
      <a class="step" onclick="loadConfigPage('regole', true);" id="config_section_regole">
        <div class="content">
          <div class="title">Regole</div>
        </div>
      </a>
      <a class="step" onclick="loadConfigPage('help', true);" id="config_section_help">
        <div class="content">
          <div class="title">Help</div>
        </div>
      </a>
    </div> 
    </center> 
</div>

{/block}

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

<script src="https://cdn.ckeditor.com/ckeditor5/36.0.0/classic/ckeditor.js"></script>
<script>
var step = 1;
var tab = 'domanda';
var form_changed = false;

function loadConfigPage(_tab, _scrolltop, _ask) {
    
    if (typeof _tab === 'undefined') 
        _tab = tab;
    
    if (typeof _step === 'undefined') 
        _step = step;
    
    if (typeof _scrolltop === 'undefined') 
        _scrolltop = false;
    
    if (typeof _ask === 'undefined') 
        _ask = false;
    
    if (_step != 99)
        step = _step;
    
    tab = _tab;
    
    var res = true;

    if (res) {
        elem = $("#wizard_div_{$modal_id}");
        $.ajax({
            type: 'get',
            url: '{$APP.url}/{$codice_domanda}/'+tab,
            //url: '{$BASE_URL}/ui/default/m3_attivita/insert',
            data: { modal_id: '{$modal_id}' },
            success: function () {
                elem.append('<div class="ui active inline loader"></div>');
            }
        })
        .done(function( data ) {
            if (_scrolltop)
                $(".scrolling.content").scrollTop( 0 );
            elem.html(data).promise().done(function(){
                //your callback logic / code here
                console.log('promise done!');
   
                
                $("#config_sections .active.step").removeClass("active");
                $("#config_section_"+tab).addClass("active");
                
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

$(document).ready(function() {
    loadConfigPage('domanda');
    
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
                        loadConfigPage(step, true);
                    });*/
                    console.log('vado allo step successivo');
                    step = step + 1;
                    form_changed = false;
                    loadConfigPage(step, true);
                }
                else if (data == 'KO') {
                    $('.ui.error.message').html("Errore");
                    $('.ui.form').addClass('error'); //response.msg
                }
                else if (data == 'FINE') {
                    step = step + 1;
                    form_changed = false;
                    loadConfigPage(99, true);
                }
                else {
                    console.log(data);
                    var msg = JSON.parse(data);
                    
                    if (msg['result']) {
                        form_changed = false;
                        if (msg['customs']) {
                            var cust = msg['customs'];
                            if (cust.record_code)
                                attivita_code = cust.record_code;
                            console.log("attivita_code", attivita_code);
                        }
                        if (msg['description'].length > 0) {
                            ShowMessage(msg, false, function() {
                                step = step + 1;
                                loadConfigPage(step);
                            });
                        }
                        else {
                            step = step + 1;
                            loadConfigPage(step);
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
        loadConfigPage(step);
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
    
    <input type="hidden" name="step" value="{$step}" id="step" />
    <button id="m3wizardform_{$modal_id}_button_submit" type="submit" class="ui button invisible">Salva</button>
</form>
{/block}





{block name="modal_actions_footer"}
<div id="actions_div_{$modal_id}">
    
    {*if $operatore->has("debug")}(configurazione-modal-domanda-container.tpl)
    <div class="ui pink button" onclick="testPOST('m3wizardform_{$modal_id}');">Test POST</div>
    <a class="ui violet button" href="{$APP.url}/report/pdf/{$record_code}" target="_blank" >Stampa</a>
   
    {/if}
    
    <div class="ui ok button invisible">Hidden</div>
    <div class="ui cancel button invisible">Hidden</div>
    {if $action_name == "search"}
    <div class="ui blue search button">Ricerca</div>
    <div class="ui pink clear button">Pulisci</div>
    {elseif $action_name == "delete"}
    <div class="ui red delete button">Elimina</div>
    {elseif $action_name != "read"}
    <div class="ui grey back button invisible">Indietro</div>
    <div class="ui green save button" id="btn_wizard_salva_prosegui">Salva e prosegui</div>
    {/if}
    {if $delete == 1}
    <div class="ui red delete button">Elimina</div>
    {/if}
    {if $action_name == "read" && ($operatore->has("ATENEO") || $operatore->has("DIPARTIMENTO"))}
    <div class="ui button" onclick="annullaValidazione();">Annulla validazione</div>
    {/if*}
    
    
    {*<div class="ui blue button" onclick="loadConfigPage(step);">Ricarica</div>*}
    <div class="ui cancel button">Chiudi</div>
</div>
{/block}