
<script>
function modificaHelp(anno, codice_domanda, sezione) {
    modal_page_new(
        'modifica_{$codice_domanda}_{$tab_name}',
        //'/admin/form/{$context_name}/{$object_code}/update?{$pks}='+record_code,
        //'{$BASE_URL}/admin/objects/{$object_code}/update/'+record_code,
        '{$APP.url}/form/{$tab_name}/{$codice_domanda}/'+sezione+'?anno='+anno,
        'large', 
        loadConfigPage, 
        loadConfigPage
    );
}
function nuovoHelp(anno, codice_domanda, sezione) {
    modal_page_new(
        'modifica_{$codice_domanda}_{$tab_name}',
        //'/admin/form/{$context_name}/{$object_code}/update?{$pks}='+record_code,
        //'{$BASE_URL}/admin/objects/{$object_code}/update/'+record_code,
        '{$APP.url}/form/{$tab_name}/{$codice_domanda}/'+sezione+'?new=1&anno='+anno,
        'large', 
        loadConfigPage, 
        loadConfigPage
    );
}
</script>


<div class="ui button" onclick="nuovoHelp({$anno}, '{$codice_domanda}', '{$sezione}');">Nuovo</div>

{foreach name="t" key=key item=item from=$helps}


    
    <div class="ui clearing segment">
        <div class="ui right floated button" onclick="modificaHelp({$anno}, '{$item.codice_domanda}', '{$item.sezione}');">Modifica</div>
        <h1>2023 | {$item.sezione}</h1>
        {$item.testo_help}
    </div
    
    <hr>
    
    
{foreachelse}
<div class="ui message">
  <div class="header">Nessun help</div>
  <p>Non sono presenti testi di help relativi alla domanda <em>{$codice_domanda}</em>.</p>
</div>

{/foreach}


