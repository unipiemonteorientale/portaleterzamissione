{extends file="template-modal-page.tpl"}

{block name="html_head_extra"}

{/block}




{block name="modal_header"}

<div class="header" style="padding: 0.50rem 1.25rem;">
    FAQ
</div>

{/block}



{block name="modal_content"}

<script>
var faqeditor;
$(document).ready(function() {
    console.log('configurazione-form-faq::ready');
    /*faqeditor = ClassicEditor
        .create( document.querySelector( '#faqeditor' ) )
        .then( newfaqeditor => {
            faqeditor = newfaqeditor;
        } )
        .catch( error => {
            console.error( error );
        } );*/
    /*var editor = new nicEditor().panelInstance('faqeditor'); 
    faqeditor = nicEditors.findEditor('faqeditor');
    
    faqeditor.setContent("{$faq.risposta|escape:javascript}");*/
    
    CKEDITOR.replace( 'faqeditor' );
        
    $("#btn_config_faq_salva").click(function() {
        
        // faqeditor.getContent()
        var data = CKEDITOR.instances.faqeditor.getData();
        $("#faq_text_new").val(data);
        // return false;
        $.ajax({
            type: 'post',
            url: '{$APP.url}/form/{$tab_name}/{$codice_domanda}/{$sezione}',
            data: $('#frm_config_faq').serialize(),
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
                $("#actions_div_form_faq .ok.button").click();
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
                            $("#actions_div_form_faq .ok.button").click();
                        });
                    }
                    else {
                        $("#actions_div_form_faq .ok.button").click();
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
    
    $("#btn_config_faq_delete").click(function() {
        
        $.modal({
            title: 'Important Notice',
            class: 'mini',
            allowMultiple: true,
            content: 'Sei sicuro di voler cancellare definitivamente questa FAQ?',
            text: {
                ok    : 'Sì',
                cancel: 'No'
            },
            actions: [{
                text: 'Sì',
                class: 'green ok'
            },{
                text: 'No',
                class: 'red'
            }],
            onApprove: function() {
                $("#delete").val('1');
                    
                $.ajax({
                    type: 'post',
                    url: '{$APP.url}/form/{$tab_name}/{$codice_domanda}/{$sezione}',
                    data: $('#frm_config_faq').serialize(),
                    success: function () {
                        console.debug("form success sent.");
                    }
                })
                .done(function( data ) {
                    if (data.trim() == 'OK') {
                        $("#actions_div_form_faq .ok.button").click();
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
                                    $("#actions_div_form_faq .ok.button").click();
                                });
                            }
                            else {
                                $("#actions_div_form_faq .ok.button").click();
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
        }).modal('show');
        
        return false;
        
         
    });
});
</script>


<form id="frm_config_faq" class="ui form">

    <input type="hidden" name="anno" value="{$anno}" />
    <input type="hidden" name="code" value="{$codice}" />
    <input type="hidden" name="tab_name" value="{$tab_name}" />
    <input type="hidden" name="delete" id="delete" value="0" />
    <input type="hidden" name="faq_text_new" id="faq_text_new" value="" />
    
    <div class="field">
        <label>Categoria</label>
        <select class="ui dropdown" name="categoria">
            <option value="generale" {if $categoria == 'generale'}selected=""{/if}>generale</option>
            <option value="public_engagement" {if $categoria == 'public_engagement'}selected=""{/if}>public engagement</option>
        </select>
    </div>

    <div class="field">
        <label>Ordinamento</label>
        <input type="number" step="1" name="sorting" value="{$faq.sorting}" />
    </div>

    <div class="field">
        <label>Domanda</label>
        <textarea name="domanda" rows="2">{$faq.domanda|default:'Inserisci qui il testo della domanda'}</textarea>
    </div>

    <div class="field">
        <label>Risposta</label>
        <textarea id="faqeditor" name="risposta">{$faq.risposta|default:'Inserisci qui il testo della risposta'}</textarea>
    </div>

</form>

{/block}


{block name="modal_actions_footer"}
<div id="actions_div_form_faq">
    
    {if $operatore->has("debug")}(configurazione-form-faq.tpl)
    
    {/if}
    
    <div class="ui ok button invisible">Hidden</div>
    <div class="ui green save button" id="btn_config_faq_salva">Salva</div>
    {if $codice|count_characters}
    <div class="ui red button" id="btn_config_faq_delete">Elimina</div>
    {/if}
    <div class="ui cancel button">Chiudi</div>
</div>
{/block}