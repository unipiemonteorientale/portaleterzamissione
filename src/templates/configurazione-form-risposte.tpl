{extends file="template-modal-page.tpl"}

{block name="html_head_extra"}

{/block}




{block name="modal_header"}

<div class="header" style="padding: 0.50rem 1.25rem;">
    Risposta {$risposta.codice_risposta}
</div>

{/block}



{block name="modal_content"}

<script>
$(document).ready(function() {
    
        
    $("#btn_config_risposta_salva").click(function() {
        console.log("btn_config_risposta_salva::click");
        
        $.ajax({
            type: 'POST',
            url: '{$APP.url}/form/{$tab_name}/{$risposta.codice_risposta}/{$sezione}',
            data: $('#frm_config_risposta').serialize(),
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
                $("#actions_div_form_risposta .ok.button").click();
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


<form id="frm_config_risposta" class="ui form">

    <input type="hidden" name="anno" value="{$anno}" />
    <input type="hidden" name="codice_domanda" value="{$risposta.codice_domanda}" />
    <input type="hidden" name="codice_risposta" value="{$risposta.codice_risposta}" />
    <input type="hidden" name="tab_name" value="{$tab_name}" />
    <input type="hidden" name="sezione" value="{$sezione}" />

    <div class="field">
        <label>Testo della risposta</label>
        <textarea name="testo_risposta">{$risposta.testo_risposta}</textarea>
    </div>
    
    <div class="field">
        <label>Ordine</label>
        <input type="number" step="1" name="ordinamento" value="{$risposta.ordinamento|default:'0'}" />
    </div>
        
    <div class="field">
        <label>Formato campo aggiuntivo</label>
        <select class="ui dropdown" name="campo_aggiuntivo_tipo">
            <option value="" {if $sezione == ''}selected=""{/if}>nessuno</option>
            <option value="TEXT" {if $sezione == 'TEXT'}selected=""{/if}>testo</option>
            <option value="INT" {if $sezione == 'INT'}selected=""{/if}>intero</option>
            <option value="DECIMAL" {if $sezione == 'DECIMAL'}selected=""{/if}>decimale</option>
            <option value="PERC" {if $sezione == 'PERC'}selected=""{/if}>percentuale</option>
        </select>
    </div>
    
    <div class="field">
        <label>Etichetta campo aggiuntivo</label>
        <input type="text" name="campo_aggiuntivo_label" value="{$risposta.campo_aggiuntivo_label}" />
    </div>

</form>

{/block}


{block name="modal_actions_footer"}
<div id="actions_div_form_risposta">
    
    {if $operatore->has("debug")}(configurazione-form-risposte.tpl)
    
    {/if}
    
    <div class="ui ok button invisible">Hidden</div>
    <div class="ui green button" id="btn_config_risposta_salva">Salva</div>
    <div class="ui cancel button">Chiudi</div>
</div>
{/block}



