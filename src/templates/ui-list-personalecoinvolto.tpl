
{if $action_name == "insert" || $action_name == "update"}
<button type="button" onclick="modal_page_new(
                        '{$current_id|md5}',
                        '{$BASE_URL}/ui/modal/default/M3_PERSONALECOINVOLTO/insert?code_attivita={$schema.code_attivita}&codice_domanda={$schema.codice_domanda}',
                        'large', 
                        function() {  }, 
                        function() { refreshDomanda('{$schema.codice_domanda}'); } 
                    )" class="ui green button">Nuovo</button> 
{/if} 

{foreach name="t" key=key item=item from=$list}

    {if $smarty.foreach.t.first}
    <table class="ui celled padded striped red table">
    <thead>
        <tr>
            <th>Nominativo</th>
            <th colspan="2">SSD</th>
            <th>Struttura</th>
            <th>Tipo di attività svolta</th>
            {if $action_name == "insert" || $action_name == "update"}<th colspan="2"></th>{/if} 
        </tr>
    </thead>
    <tbody>
    {/if}
    
    <tr>
        <td>{$item.cognome} {$item.nome}</td>
        <td>{$item.cod_ssd}</td>
        <td>{$item.des_ssd}</td>
        <td>{$item.struttura}</td>
        <td>{$item.coinvolgimento}{if $item.altro|count_characters}<br><em>{$item.altro}</em>{/if}</td>
        {if $action_name == "insert" || $action_name == "update"}   
        <td><button type="button" onclick="modal_page_new(
                        '{$current_id|md5}',
                        '{$BASE_URL}/ui/modal/default/M3_PERSONALECOINVOLTO/update?record_code={$item.code}&code_attivita={$schema.code_attivita}&codice_domanda={$schema.codice_domanda}',
                        'large', 
                        function() {  }, 
                        function() { refreshDomanda('{$schema.codice_domanda}'); }
                    )" class="ui green button">Modifica</button> </td>
        <td><button type="button" onclick="modal_page_new(
                        '{$current_id|md5}',
                        '{$BASE_URL}/ui/modal/default/M3_PERSONALECOINVOLTO/delete?record_code={$item.code}',
                        'tiny', 
                        function() {  }, 
                        function() { refreshDomanda('{$schema.codice_domanda}'); }
                    )" class="ui red button">Elimina</button> </td>
        {/if} 
    </tr>
    
    {if $smarty.foreach.t.last}
    </tbody>
    </table>
    {/if}
{foreachelse}
<p>Nessun personale coinvolto.</p>
{/foreach}