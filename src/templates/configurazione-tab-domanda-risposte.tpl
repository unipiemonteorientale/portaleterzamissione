
<script>
function modificaConfigurazioneRisposta(anno, codice_risposta, sezione) {
    modal_page_new(
        'modifica_{$codice_risposta}_{$tab_name}',
        //'/admin/form/{$context_name}/{$object_code}/update?{$pks}='+record_code,
        //'{$BASE_URL}/admin/objects/{$object_code}/update/'+record_code,
        '{$APP.url}/form/{$tab_name}/'+codice_risposta+'/'+sezione+'?anno='+anno,
        'large', 
        loadConfigPage, 
        loadConfigPage
    );
}
</script>


{foreach name="t" key=key item=item from=$risposte}

    {if $smarty.foreach.t.first}
    <table class="ui celled striped very compact table">
    <thead>
        <tr>
            <th>Anno</th>
            <th>Codice risposta</th>
            <th>Testo risposta</th>
            <th>Ordine</th>
            <th>Formato campo aggiuntivo</th>
            <th>Etichetta campo aggiuntivo</th>
            <th colspan="2">Azioni</th>
        </tr>
    </thead>
    <tbody>
    {/if}
    
    
    
    <tr>
        <td class="collapsing"><h2>{$item.anno|default:$anno}</h2></td>
        <td class="collapsing"><h3>{$item.codice_risposta}</h3></td>
        <td class="collapsing">{$item.testo_risposta}</td>
        <td class="collapsing">{$item.ordinamento}</td>
        <td class="collapsing">{$item.campo_aggiuntivo_tipo}</td>
        <td class="collapsing">{$item.campo_aggiuntivo_label}</td>
        
        <td class="collapsing"><button type="button" onclick="modificaConfigurazioneRisposta('{$item.anno|default:$anno}', '{$item.codice_risposta}', '-');" class="ui gray fluid button">Modifica</button> </td>
        <td class="collapsing">{*<button type="button" onclick="" class="ui red button">Elimina</button>*}</td>
    </tr>
    
    {if $smarty.foreach.t.last}
    </tbody>
    </table>
    {/if}
{foreachelse}
<div class="ui message">
  <div class="header">Nessuna risposta</div>
  <p>Non sono presenti risposte o la domanda è a risposta libera.</p>
</div>

{/foreach}
