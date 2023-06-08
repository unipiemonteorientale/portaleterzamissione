<style>
#table_ui_domanda_validazione th {
    background: var(--colorPrimary);
    color: white;
}
</style>


<h1>Validazione Sezione {$sezione} <button type="button" onclick="loadWizardPage({$step_sezione});" class="ui primary button">Vai alla sezione</button></h1>

{foreach name="t" key=key item=item from=$risultati}

    {if $smarty.foreach.t.first}
    <table class="ui celled padded striped table" id="table_ui_domanda_validazione">
    <thead>
        <tr>
            <th colspan="2">Domanda</th>
            <th>Obbligatoria</th>
            <th>Risposta</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    {/if}
    
    <tr {if $item.obbligatoria == 'S' and !$item.risposta_ok}class="error"{/if}>
        <td>{$item.codice_domanda}</td>
        <td>{$item.testo_domanda}</td>
        <td>{if $item.obbligatoria == 'S'}SI{else}NO{/if}</td>
        <td>{if $item.risposta_ok}SI{else}NO{/if}</td>
        
        <td>{if $item.obbligatoria == 'S' && !$item.risposta_ok}
            <button type="button" onclick="loadWizardPage({$step_sezione}, false, '#div_domanda_{$item.codice_domanda}');" class="ui primary button">Vai alla domanda</button>{/if}</td>
    </tr>
    
    {if $smarty.foreach.t.last}
    </tbody>
    </table>
    {/if}
{foreachelse}
<p>Nessun personale coinvolto.</p>
{/foreach}