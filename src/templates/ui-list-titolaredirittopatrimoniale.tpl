



{if $tot_perc < 100.0}
<div class="ui yellow message">
  Il totale delle percentuali è {$tot_perc}%. Per la validazione è necessario indicare il 100%.
  
  {if $action_name == "insert" || $action_name == "update"}
    {if $tot_perc < 100.0}
<button type="button" onclick="modal_page_new(
                        '{$current_id|md5}',
                        '{$BASE_URL}/ui/modal/default/M3_TITOLAREDIRITTOPATRIMONIALE/insert?code_attivita={$schema.code_attivita}&codice_domanda={$schema.codice_domanda}',
                        'large', 
                        function() {  }, 
                        function() { refreshDomanda('{$schema.codice_domanda}'); } 
                    )" class="ui green button">Nuovo</button> 
    {/if} 
{/if} 


</div>
{elseif $tot_perc == 100.0}
<div class="ui green message">
  Il totale delle percentuali è 100%.
</div>
{/if}
        
        

{foreach name="t" key=key item=item from=$list}

    {if $smarty.foreach.t.first}
    <table class="ui celled padded striped red table">
    <thead>
        <tr>
            <th>Tipologia</th>
            <th>Titolare</th>
            <th>Percentuale</th>
            {if $action_name == "insert" || $action_name == "update"}<th colspan="2"></th>{/if} 
        </tr>
    </thead>
    <tbody>
    {/if}
    
    <tr>
        <td>{$item.classif_titolare}</td>
        <td>{$item.titolare}</td>
        <td>{$item.percentuale} %</td>
        {if $action_name == "insert" || $action_name == "update"}   
        <td><button type="button" onclick="modal_page_new(
                        '{$current_id|md5}',
                        '{$BASE_URL}/ui/modal/default/M3_TITOLAREDIRITTOPATRIMONIALE/update?record_code={$item.code}&code_attivita={$schema.code_attivita}&codice_domanda={$schema.codice_domanda}',
                        'large', 
                        function() {  }, 
                        function() { refreshDomanda('{$schema.codice_domanda}'); }
                    )" class="ui green button">Modifica</button> </td>
        <td><button type="button" onclick="modal_page_new(
                        '{$current_id|md5}',
                        '{$BASE_URL}/ui/modal/default/M3_TITOLAREDIRITTOPATRIMONIALE/delete?record_code={$item.code}',
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
<p>Nessun titolare indicato.</p>
{/foreach}