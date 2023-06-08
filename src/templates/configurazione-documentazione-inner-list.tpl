
<script language="JavaScript" type="text/javascript">

$(function(){
});
function modificaDocumentazione(record_code) {
    modal_page_new(
        'modificaDocumentazione',
        '{$APP.url}/form/documentazione/'+record_code+'/-',
        'large', 
        refreshMissione3ConfContent, 
        refreshMissione3ConfContent
    );
}

function nuovaDocumentazione() {
    modal_page_new(
                        '{$current_id|md5}',
                        '{$BASE_URL}/ui/modal/default/{$object_name}/insert',
                        'large', 
                        function() {  }, 
                        refreshMissione3ConfContent
                    );
    /*modal_page_new(
        'nuovaDocumentazione', 
        '{$APP.url}/form/documentazione/-/-?nuovo=1',
        'large', 
        refreshMissione3ConfContent, 
        refreshMissione3ConfContent
    );*/
}
</script>



<div class="ui secondary menu">
    <div class="left menu">
        <div class="item"><button class="ui button" onclick="nuovaDocumentazione();">Nuova</button></div>
        <div class="item"><h3>Documentazione</h3></div>
    </div>    
</div>



{foreach name="t" key=key item=item from=$documentazione}

    {if $smarty.foreach.t.first}
    <table class="ui celled striped very compact table">
    <thead>
        <tr>
            <th>Tipologia</th>
            <th>Titolo e descrizione</th>
            <th>File / URL risorsa</th>
            <th>Ordinamento</th>
            <th colspan="2"></th>
        </tr>
    </thead>
    <tbody>
    {/if}
    
    
    
    <tr>
        <td>{$item.tipologia_allegato}</td>
        <td>{$item.file_etichetta}</td>
        <td class="collapsing">
            {if $item.file_locazione|count_characters}<a download href="{$item.file_url}"><i class="file icon large"></i></a>
            {elseif $item.url_risorsa|count_characters}<a href="{$item.url_risorsa}" target="_blank">{$item.url_risorsa}</a>
            {else}non ancora indicato
            {/if}
        </td>
            
        <td class="collapsing">{$item.sorting}</td>
        <td class="collapsing"><button type="button" onclick="modal_page_new(
                        '{$current_id|md5}',
                        '{$BASE_URL}/ui/modal/default/{$object_name}/update?record_code={$item.code}',
                        'large', 
                        function() {  }, 
                        refreshMissione3ConfContent
                    )" class="ui green button">Modifica</button> </td>
        <td class="collapsing"><button type="button" onclick="modal_page_new(
                        '{$current_id|md5}',
                        '{$BASE_URL}/ui/modal/default/{$object_name}/delete?record_code={$item.code}',
                        'tiny', 
                        function() {  }, 
                        refreshMissione3ConfContent
                    )" class="ui red button">Elimina</button> </td>
    </tr>
    
    {if $smarty.foreach.t.last}
    </tbody>
    </table>
    {/if}
{foreachelse}
<div class="ui message">
  <div class="header">Nessuna documentazione</div>
  <p>Non sono presenti file di documentazione.</p>
</div>
{/foreach}