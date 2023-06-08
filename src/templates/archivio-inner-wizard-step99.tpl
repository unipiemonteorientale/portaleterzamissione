<script>

function valitateAttivita(record_code) {
    console.log('valitateAttivita', attivita_code);
    
    $.ajax({
            type: 'post',
            url: '{$APP.url}/wizard/validate?record_code='+attivita_code,
            success: function () { }
        })
        .done(function( data ) {
            //console.log(data);
            if (data == 'OK') {
                $("#btn_wizard_chiudi").click();
                var msg = {
                    result:true,
                    level:'INFO', 
                    description:'Attività validata con successo.'
                };
                ShowMessage(msg, false, function() {
                    //
                    //modal_page_close('modificaAttivita');
                    return true;
                });
            }
            else
                $("#response").html(data);
        });
}
</script>

<div class="ui massive message" style="font-size:1.1em;">
    <p>Attività salvata in <strong>BOZZA</strong>.<br>
    La tipologia di compilazione scelta è: {if $tipo_compilazione == 'B'}<strong>BASE</strong>{elseif $tipo_compilazione == 'V'}<strong>VALUTAZIONE</strong>{/if}</p>
    &Egrave; possibile:
    <ul>
        <li><em>chiedere la validazione dell'attività inserita.</em> Per la validazione è necessario aver risposto a tutte le domande obbligatorie per la tipologia di compilazione indicata;</li>
        <li><em>cambiare la tipologia di compilazione;</em></li>
        <li><em>chiudere e validare l'attività inserita in un secondo momento.</em></li>
    </ul>
    
    
    
    
    <p>Come vuoi procedere?</p>
    <p>
    
    <div class="huge ui green button" onclick="valitateAttivita('{$record_code}');">VALIDA</div> 
    <div class="huge ui blue button" onclick="loadWizardPage(2, true);">CAMBIA</div> 
    <div class="huge ui button" onclick="$('#btn_wizard_chiudi').click();">CHIUDI</div> 
    
    </p>
    
    
</div>

<div id="response"></div>