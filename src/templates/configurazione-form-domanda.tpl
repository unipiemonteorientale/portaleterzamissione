{extends file="template-modal-page.tpl"}

{block name="html_head_extra"}

{/block}




{block name="modal_header"}

<div class="header" style="padding: 0.50rem 1.25rem;">
    Domanda
</div>

{/block}



{block name="modal_content"}

<script>
$(document).ready(function() {
    
        
    $("#btn_config_domanda_salva").click(function() {
        
        
        $.ajax({
            type: 'post',
            url: '{$APP.url}/form/{$tab_name}/{$codice_domanda}/{$sezione}',
            data: $('#frm_config_domanda').serialize(),
            success: function () {
                console.debug("form success sent.");
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
                $("#actions_div_form_domanda .ok.button").click();
            }
            else if (data == 'KO') {
                $('.ui.error.message').html("Errore");
                $('.ui.form').addClass('error'); //response.msg
            }
            else
                console.log(data);
        });    
    });
});
</script>


<form id="frm_config_domanda" class="ui form">

    <input type="hidden" name="anno" value="{$anno}" />
    <input type="hidden" name="codice_domanda" value="{$codice_domanda}" />
    <input type="hidden" name="tab_name" value="{$tab_name}" />
    <input type="hidden" name="sezione" value="{$sezione}" />

    <div class="field">
        <label>Testo della domanda</label>
        <textarea name="testo_domanda">{$domanda.descrizione}</textarea>

    </div>
    
    <div class="field">
        <label>Help "breve"</label>
        <textarea name="help_breve">{$domanda.help_breve}</textarea>

    </div>
    


</form>

{/block}


{block name="modal_actions_footer"}
<div id="actions_div_form_domanda">
    
    {if $operatore->has("debug")}(configurazione-form-regole.tpl)
    
    {/if}
    
    <div class="ui ok button invisible">Hidden</div>
    <div class="ui green save button" id="btn_config_domanda_salva">Salva</div>
    <div class="ui cancel button">Chiudi</div>
</div>
{/block}



