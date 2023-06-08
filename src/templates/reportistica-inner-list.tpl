
<script language="JavaScript" type="text/javascript">

$(function(){
    $(".menu .dropdown.item").dropdown();
});
function modificaAttivita(record_code) {
    modal_page_new(
        'modificaAttivita',
        //'/admin/form/{$context_name}/{$object_code}/update?{$pks}='+record_code,
        //'{$BASE_URL}/admin/objects/{$object_code}/update/'+record_code,
        '{$BASE_URL}/archivio/wizard/update?record_code='+record_code,
        'overlay fullscreen', 
        function() { }, 
        function() { }
    );
}

function visualizzaAttivita(record_code) {
    modal_page_new(
        'modificaAttivita',
        //'/admin/form/{$context_name}/{$object_code}/update?{$pks}='+record_code,
        //'{$BASE_URL}/admin/objects/{$object_code}/update/'+record_code,
        '{$BASE_URL}/archivio/wizard/read?record_code='+record_code,
        'overlay fullscreen', 
        function() { }, 
        function() { }
    );
}
</script>

<div class="ui text large stackable menu">
    <div class="item">
        <h3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Elenco singole attività</h3>
    </div>
</div>

<style>
.wide.column { border: 0px solid violet; }
</style>



{foreach name="t" key=key item=item from=$attivita}

    {if $smarty.foreach.t.first}
    {/if}
    
    
    
    <div class="ui very compact stackable grid " style="border:1px solid grey; margin-top:8px; background:{cycle values='#fff,#ffe6e6'}">
    
        <div class="sixteen wide mobile three wide computer column">
            <div class="ui unstackable very compact grid container">
                <div class="three wide column"><h3>{$item.anno}</h3></div>
                <div class="three wide column"><a class="ui grey circular massive label">{$item.campo_azione}</a></div>
                <div class="five wide column">{if $item.tipo_compilazione == 'B'}<span class="ui orange fluid center label">BASE</span>{elseif $item.tipo_compilazione == 'V'}<span class="ui yellow fluid center label">VALUTAZIONE</span>{/if}</div>
                <div class="five wide column">{if $item.stato == 'B'}<span class="ui red fluid center label">BOZZA</span>{elseif $item.stato == 'V'}<span class="ui blue fluid center label">VALIDATA</span>{/if}</div>
            </div>
        </div>
        
        
        <div class="sixteen wide mobile three wide computer column"><em>{$item.titolo}</em></div>
        
        <div class="sixteen wide mobile three wide computer column">
            {foreach name="p" key=codeattiv item=personale from=$personale_coinvolto[$item.code]}
                {if $smarty.foreach.p.first}Personale coinvolto<br>{/if}
            <i class="user icon"></i> <strong>{$personale.cognome} {$personale.nome}</strong> {if !$MOBILE}<br>Ruolo: {$personale.coinvolgimento} | Struttura: {$personale.strutt} {/if}<br>
            {/foreach}
        </div>
        <div class="sixteen wide mobile three wide computer column">
            {foreach name="s" key=codeattiv item=struttura from=$strutture_coinvolte[$item.code]}
                {if $smarty.foreach.s.first}Strutture collegate<br>{/if}
            <i class="home icon"></i> <strong>{$struttura.strutt}</strong> <br>
            {/foreach}
        </div>

        <div class="sixteen wide mobile two wide computer column">
            {$item.codice_risposta} <br> {$item.testo_risposta} <br> {$item.supplement1}
        </div>
        
        <div class="sixteen wide mobile two wide computer column">
        {if $item.stato == 'V'}
            <button type="button" onclick="visualizzaAttivita('{$item.code}');" class="ui fluid button">Visualizza</button>
        {else}
            <div class="ui stackable very compact grid container">
                <div class="eight wide column"><button type="button" onclick="modificaAttivita('{$item.code}');" class="ui fluid button">Modifica</button> </div>
                <div class="eight wide column"><button type="button" onclick="deleteAttivita('{$item.code}');" class="ui fluid button">Elimina</button> </div>
            </div>
        {/if}    
        </div>  
    </div> 
        

    
    {if $smarty.foreach.t.last}
        
    <div class="ui centered pagination menu">
        {for $p = 1 to $pages}
        <a class="{if $current_page == $p}active red{/if} item" onclick="refreshMissione3ArchivioContent({$p});">{$p}</a>
        {/for}
    </div>
    {/if}
{foreachelse}
<div class="ui message">
  <div class="header">Nessuna attività</div>
  {if $operatore->has("DOCENTE")}
  <p>Non sono presenti attività in archivio in base ai filtri, se indicati, oppure alle quali ha partecipato l'utente.</p>
  {else}
  <p>Non sono presenti attività in archivio in base ai filtri, se indicati.</p>
  {/if}
</div>

{/foreach}




