{extends file="template-modal-page.tpl"}

{block name="html_head_extra"}

{/block}




{block name="modal_header"}

<div class="header" style="padding: 0.50rem 1.25rem;">
    Regole
</div>

{/block}



{block name="modal_content"}

<script>
$(document).ready(function() {
    
        
    $("#btn_config_regole_salva").click(function() {
        
        
        $.ajax({
            type: 'post',
            url: '{$APP.url}/form/{$tab_name}/{$codice_domanda}/{$sezione}',
            data: $('#frm_config_regole').serialize(),
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
                $("#actions_div_form_regole .ok.button").click();
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


<form id="frm_config_regole" class="ui form">

    <input type="hidden" name="anno" value="{$anno}" />
    <input type="hidden" name="codice_domanda" value="{$codice_domanda}" />
    <input type="hidden" name="tab_name" value="{$tab_name}" />
    <input type="hidden" name="sezione" value="{$sezione}" />

    <h1>Visibilità</h1>
    <div class="two fields">
        <div class="field">

            <div class="ui toggle checkbox">
                <input type="checkbox" name="default_visibile" value='S' {if $rules.default_visibile == 'S'}checked="checked"{/if} />
                <label>Visibilità di base</label>
            </div>

        </div>
        <div class="field">

            <div class="ui toggle checkbox">
                <input type="checkbox" name="visibile" value='S' {if $rules.visibile|default:$rules.default_visibile == 'S'}checked="checked"{/if} />
                <label>Visibilità di valutazione</label>
            </div>

        </div>
    </div>
        

    <h1>Obbligatorietà</h1>
    <div class="two fields">
        <div class="field">

            <div class="ui toggle checkbox">
                <input type="checkbox" name="default_obbligatoria" value='S' {if $rules.default_obbligatoria == 'S'}checked="checked"{/if} />
                <label>Obbligatorietà di base</label>
            </div>

        </div>
        <div class="field">

            <div class="ui toggle checkbox">
                <input type="checkbox" name="obbligatoria" value='S' {if $rules.obbligatoria|default:$rules.default_obbligatoria == 'S'}checked="checked"{/if} />
                <label>Obbligatorietà di valutazione</label>
            </div>

        </div>
    </div>
    

</form>

{/block}


{block name="modal_actions_footer"}
<div id="actions_div_form_regole">
    
    {if $operatore->has("debug")}(configurazione-form-regole.tpl)
    
    {/if}
    
    <div class="ui ok button invisible">Hidden</div>
    <div class="ui green save button" id="btn_config_regole_salva">Salva</div>
    <div class="ui cancel button">Chiudi</div>
</div>
{/block}



