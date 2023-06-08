{extends file="template-modal-page.tpl"}

{block name="html_head_extra"}

{/block}




{block name="modal_header"}

<div class="header" style="padding: 0.50rem 1.25rem;">
    Help
</div>

{/block}



{block name="modal_content"}

<script>
var editor;
$(document).ready(function() {
    console.log('configurazione-form-help::ready');
    editor = ClassicEditor
        .create( document.querySelector( '#editor' ) )
        .then( newEditor => {
            editor = newEditor;
        } )
        .catch( error => {
            console.error( error );
        } );
        
    $("#btn_config_help_salva").click(function() {
        
        $("#help_text_new").val(editor.getData());
        
        $.ajax({
            type: 'post',
            url: '{$APP.url}/form/{$tab_name}/{$codice_domanda}/{$sezione}',
            data: $('#frm_config_help').serialize(),
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
                $("#actions_div_form_help .ok.button").click();
            }
            else if (data == 'KO') {
                $('.ui.error.message').html("Errore");
                $('.ui.form').addClass('error'); //response.msg
            }
            else {
                console.log(data);
                var msg = JSON.parse(data);
                
                if (msg['result']) {
                    if (msg['description'].length > 0) {
                        ShowMessage(msg, false, function() {
                            $("#actions_div_form_help .ok.button").click();
                        });
                    }
                    else {
                        $("#actions_div_form_help .ok.button").click();
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
    });
});
</script>


<form id="frm_config_help" class="ui form">

    <input type="hidden" name="anno" value="{$anno}" />
    <input type="hidden" name="codice_domanda" value="{$codice_domanda}" />
    <input type="hidden" name="tab_name" value="{$tab_name}" />
        {if $nuovo}
            <div class="field">
                <label>Campo d'azione</label>
                <select class="ui dropdown" name="nuova_sezione">
                    <option value="" {if $sezione == ''}selected=""{/if}>qualsiasi</option>
                    <option value="A" {if $sezione == 'A'}selected=""{/if}>A</option>
                    <option value="B" {if $sezione == 'B'}selected=""{/if}>B</option>
                    <option value="C" {if $sezione == 'C'}selected=""{/if}>C</option>
                    <option value="D" {if $sezione == 'D'}selected=""{/if}>D</option>
                    <option value="E" {if $sezione == 'E'}selected=""{/if}>E</option>
                    <option value="F" {if $sezione == 'F'}selected=""{/if}>F</option>
                    <option value="G" {if $sezione == 'G'}selected=""{/if}>G</option>
                    <option value="H" {if $sezione == 'H'}selected=""{/if}>H</option>
                    <option value="I" {if $sezione == 'I'}selected=""{/if}>I</option>
                    <option value="J" {if $sezione == 'J'}selected=""{/if}>J</option>
                </select>
            </div>
        {else}
    <input type="hidden" name="sezione" value="{$sezione}" />
        {/if}
    <input type="hidden" name="help_text_new" id="help_text_new" value="" />

    <textarea id="editor" name="testo_help">
    {$help.testo_help|default:"Inserisci qui il testo dell'help."}
    </textarea>

</form>

{/block}


{block name="modal_actions_footer"}
<div id="actions_div_form_help">
    
    {if $operatore->has("debug")}(configurazione-form-help.tpl)
    
    {/if}
    
    <div class="ui ok button invisible">Hidden</div>
    <div class="ui green save button" id="btn_config_help_salva">Salva</div>
    <div class="ui cancel button">Chiudi</div>
</div>
{/block}