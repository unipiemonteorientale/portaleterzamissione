
<script language="JavaScript" type="text/javascript">

$(function(){
});
function modificaFAQ(record_code) {
    modal_page_new(
        'modificaFAQ',
        '{$APP.url}/form/faq/'+record_code+'/-',
        'large', 
        refreshMissione3ConfContent, 
        refreshMissione3ConfContent
    );
}

function nuovaFAQ() {
    modal_page_new(
        'nuovaFAQ', 
        '{$APP.url}/form/faq/-/-?nuovo=1',
        'large', 
        refreshMissione3ConfContent, 
        refreshMissione3ConfContent
    );
}
</script>



<div class="ui secondary menu">
    <div class="left menu">
        <div class="item"><button class="ui button" onclick="nuovaFAQ();">Nuova</button></div>
        <div class="item"><h3>FAQ</h3></div>
    </div>    
</div>


 

{foreach name="t" key=key item=item from=$faq}


    
    <div class="ui clearing segment">
        <div class="ui right floated button" onclick="modificaFAQ('{$item.code}');">Modifica</div>
        <h1><label class="ui orange horizontal label">{$item.categoria}</label>  {$item.domanda}</h1>
        {$item.risposta}
    </div
    
    <hr>
    
    
{foreachelse}
<div class="ui message">
  <div class="header">Nessuna FAQ</div>
  <p>Non sono presenti FAQ.</p>
</div>

{/foreach}
