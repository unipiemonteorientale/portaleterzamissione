
{if $action_name == "insert" || $action_name == "update"}
<button type="button" onclick="modal_page_new(
                        '{$current_id|md5}',
                        '{$BASE_URL}/ui/modal/default/{$object_name}/insert?code_attivita={$schema.code_attivita}&codice_domanda={$schema.codice_domanda}',
                        'large', 
                        function() { }, 
                        function() { refreshDomanda('{$schema.codice_domanda}'); } 
                    )" class="ui green button">Nuovo</button> 
{/if}
                    
{foreach name="t" key=key item=item from=$list}

    {if $smarty.foreach.t.first}
    <table class="ui celled striped very compact table">
    <thead>
        <tr>
            <th>Tipologia</th>
            <th>Titolo e descrizione</th>
            <th>File / URL risorsa</th>
            {if $action_name == "insert" || $action_name == "update"}<th colspan="2"></th>{/if}
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
            
        {if $action_name == "insert" || $action_name == "update"}
        <td class="collapsing"><button type="button" onclick="modal_page_new(
                        '{$current_id|md5}',
                        '{$BASE_URL}/ui/modal/default/{$object_name}/update?record_code={$item.code}&code_attivita={$schema.code_attivita}&codice_domanda={$schema.codice_domanda}',
                        'large', 
                        function() {  }, 
                        function() { refreshDomanda('{$schema.codice_domanda}'); } 
                    )" class="ui green button">Modifica</button> </td>
        <td class="collapsing"><button type="button" onclick="modal_page_new(
                        '{$current_id|md5}',
                        '{$BASE_URL}/ui/modal/default/{$object_name}/delete?record_code={$item.code}',
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
<p>Nessun elemento.</p>
{/foreach}