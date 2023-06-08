
<script language="JavaScript" type="text/javascript">

$(function(){
    $(".menu .dropdown.item").dropdown();
});
function modificaAttivita(record_code) {
    modal_page_new(
        'modificaAttivita',
        //'/admin/form/{$context_name}/{$object_code}/update?{$pks}='+record_code,
        //'{$BASE_URL}/admin/objects/{$object_code}/update/'+record_code,
        '{$APP.url}/wizard/update?record_code='+record_code,
        'overlay fullscreen', 
        refreshMissione3ArchivioContent, 
        refreshMissione3ArchivioContent
    );
}

function visualizzaAttivita(record_code) {
    modal_page_new(
        'modificaAttivita',
        //'/admin/form/{$context_name}/{$object_code}/update?{$pks}='+record_code,
        //'{$BASE_URL}/admin/objects/{$object_code}/update/'+record_code,
        '{$APP.url}/wizard/read?record_code='+record_code,
        'overlay fullscreen', 
        refreshMissione3ArchivioContent, 
        refreshMissione3ArchivioContent
    );
}

function nuovaAttivita() {
    modal_page_new(
        'nuovaAttivita', 
        "{$APP.url}/wizard/insert", 
        'overlay fullscreen', 
        refreshMissione3ArchivioContent, 
        refreshMissione3ArchivioContent
    );
}

function filtraAttivita() {
    modal_page_new(
        'filtraAttivita', 
        "{$APP.url}/filter", 
        'overlay fullscreen', 
        function() {  },
        refreshMissione3ArchivioContent
    );
}

function esportaAttivita() {
    var url = "{$APP.url}/report/xlsx?anno=2020&campo_azione=g"; 
    
    window.open(url, '_blank');
}




function mostraHelp(domanda, sezione) {
    modal_page_new(
        '{$current_id|md5}',
        '{$APP.url}/wizard/help/'+domanda+'/'+sezione,
        'medium', 
        function() { }, 
        function() { }
    );
}

function deleteAttivita(record) {
    console.log('deleteAttivita', record);
    ShowMessage({ level: 'WARNING', description: "Sei sicuro di voler cancellare l'attività? Una volta cancellata non sarà possibile tornare indietro." }, false, function() {
        var url = "{$APP.url}/delete/"+record;
        $.ajax({
            type: 'POST',
            url: url,
            success: function () {
                console.log("success");
            }
        })
        .done(function( data ) {
            console.log(data);
            if (data.trim() == 'OK') {
                ShowMessage({ description:"Attività cancellata" }, false, function() { refreshMissione3ArchivioContent(); });
            }
            else if (data.trim() == 'KO') {
                ShowMessage({ description:"Eliminazione fallita" });
            }
            else {
                var msg = JSON.parse(data.trim());
                ShowMessage(msg);
            }
        });   
    });
}
function changeFilter(name, value) {
    console.log('changeFilter', name, value);
    var url = "{$BASE_URL}/api/archivio/filter/"+name;
    $.ajax({
        type: 'POST',
        url: url,
        data: { filter_value : value },
        success: function () {
            console.log("success");
        }
    })
    .done(function( data ) {
        console.log(data);
        if (data.trim() == 'OK') {
            refreshMissione3ArchivioContent(); 
        }
    });   
}
</script>

<div class="ui text large stackable menu">
    <div class="item">
        <h3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Elenco attività</h3>
    </div>
    
    <div class="right menu">
        <div class="item"><button class="ui primary button" onclick="nuovaAttivita();">Inserisci nuova attività</button></div>
        <div class="item"><button class="ui primary button" onclick="filtraAttivita();">Filtri avanzati</button></div>
        {if $operatore->has('ADMIN')}
        <div class="item">
            <button class="ui primary button" onclick="esportaAttivita();">Esporta</a>
        </div>{/if}
        </div><div class="item">
            <a class="ui button" href="{$BASE_URL|default:'/'}">Indietro</a>
        </div>
    </div>
</div>

<div class="ui secondary stackable menu">
    <div class="item">Utente: {*$operatore->code()*} <strong>{$operatore->label()}</strong></div>
    <div class="item">Profilo:  <strong>
            {if $operatore->has("ATENEO")}Ateneo
            {elseif $operatore->has("DIPARTIMENTO")}Dipartimento
            {elseif $operatore->has("DOCENTE")}Docente
            {else}nessuno{/if}
        </strong>
    </div>
    <div class="item">Visibilità:  <strong>
            {if $operatore->has("ATENEO")}Ogni attività presente in archivio
            {elseif $operatore->has("DIPARTIMENTO")}Ogni attività di docenti afferenti ai propri dipartimenti
            {elseif $operatore->has("DOCENTE")}Ogni attività creata dall'utente o nella quale l'utente ha partecipato
            {else}nessuna{/if}
        </strong>
    </div>
        {*
    <div class="item">Anno: <strong>{$filtri.anno|default:'tutti'}</strong></div>
    <div class="item">Campo d'azione: <strong>{$filtri.campo_azione|default:"tutti"}</strong></div>
    <div class="item">Tipo compilazione: <strong>{$filtri.tipo_compilazione|default:'tutti'}</strong></div>
    <div class="item">Stato: <strong>{$filtri.stato|default:'tutti'}</strong></div>
        *}
        
    <div class="right menu">
        <div class="ui dropdown item">
            Anno<i class="dropdown icon"></i>
            <div class="menu">
                <a class="item" onclick="changeFilter('anno', '');">tutti</a>
                {foreach item="anno" from=$anni}
                <a class="item" onclick="changeFilter('anno', '{$anno|lower}');">{$anno}</a>
                {/foreach}
            </div>
        </div>
        <div class="ui dropdown item">
            Campi d'azione <i class="dropdown icon"></i>
            <div class="menu">
                <a class="item" onclick="changeFilter('campo_azione', '');">tutti</a>
                {foreach item="campo" from=$sezioni}
                <a class="item" onclick="changeFilter('campo_azione', '{$campo|lower}');">{$campo}</a>
                {/foreach}
            </div>
        </div>
        <div class="ui dropdown item">
            Compilazione <i class="dropdown icon"></i>
            <div class="menu">
                <a class="item" onclick="changeFilter('tipo_compilazione', '');">tutte</a>
                <a class="item" onclick="changeFilter('tipo_compilazione', 'B');">base</a>
                <a class="item" onclick="changeFilter('tipo_compilazione', 'V');">validazione</a>
            </div>
        </div>
        <div class="ui dropdown item">
            Stato <i class="dropdown icon"></i>
            <div class="menu">
                <a class="item" onclick="changeFilter('stato', '');">tutti</a>
                <a class="item" onclick="changeFilter('stato', 'B');">bozza</a>
                <a class="item" onclick="changeFilter('stato', 'V');">validata</a>
            </div>
        </div>
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
        
        {if !$MOBILE || $operatore->has('MISSIONE3ADMIN')}
        <div class="sixteen wide mobile two wide computer column"style="font-size:0.8em;">
            {if $operatore->has('MISSIONE3ADMIN')}
            ID: {$item.ident}, {$item.ui}<br>
            {/if}
            Creazione: {$item.ti|date_format:"%d/%m/%Y %H:%M"}<br>
            Ultima modifica: {$item.tu|date_format:"%d/%m/%Y %H:%M"}
        </div>
        {/if}
        
        <div class="sixteen wide mobile two wide computer column">
        {if $item.stato == 'V'}
            <button type="button" onclick="visualizzaAttivita('{$item.code}');" class="ui fluid button">Visualizza</button>
        {else}
            <div class="ui unstackable very compact grid container">
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





 

{*foreach name="t" key=key item=item from=$attivita}

    {if $smarty.foreach.t.first}
    <table class="ui celled striped very compact tablet stackable table">
    <thead>
        <tr>
            <th>Anno</th>
            <th>Campo d'azione</th>
            <th>Tipo compilazione <span onclick="mostraHelp('tipo_compilazione', 'archivio');"><i class='question circle icon pointer'></i></span></th>
            <th>Stato <span onclick="mostraHelp('stato', 'archivio');"><i class='question circle icon pointer'></i></span></th>
            <th>Titolo</th>
            <th>Personale coinvolto</th>
            <th>Strutture collegate</th>
            <th>Data creazione/ultima modifica</th>
            <th colspan="2">Azioni</th>
        </tr>
    </thead>
    <tbody>
    {/if}
    
    
    
    <tr>
        <td class="collapsing"><h1>{$item.anno}</h1></td>
        <td class="collapsing"><a class="ui grey circular massive label">{$item.campo_azione}</a></td>
        <td class="collapsing">{if $item.tipo_compilazione == 'B'}<span class="ui orange fluid center label">BASE</span>{elseif $item.tipo_compilazione == 'V'}<span class="ui yellow fluid center label">VALUTAZIONE</span>{/if}</td>
        <td class="collapsing">{if $item.stato == 'B'}<span class="ui red fluid center label">BOZZA</span>{elseif $item.stato == 'V'}<span class="ui blue fluid center label">VALIDATA</span>{/if}</td>
        <td>{$item.titolo}</td>
        <td>{foreach name="p" key=codeattiv item=personale from=$personale_coinvolto[$item.code]}
            <i class="user icon"></i> <strong>{$personale.cognome} {$personale.nome}</strong> | Ruolo: {$personale.coinvolgimento} | Struttura: {$personale.strutt} <br>
            {/foreach}
        </td>
        <td>{foreach name="s" key=codeattiv item=struttura from=$strutture_coinvolte[$item.code]}
            <i class="home icon"></i> {$struttura.strutt} <br>
            {/foreach}
        </td>
        <td style="font-size:0.8em;">
            {if $operatore->has('MISSIONE3ADMIN')}
            ID: {$item.ident}, {$item.ui}<br>
            {/if}
            Creazione: {$item.ti|date_format:"%d/%m/%Y %H:%M"}<br>
            Ultima modifica: {$item.tu|date_format:"%d/%m/%Y %H:%M"}
        </td>
        
        {if $item.stato == 'V'}
        <td class="collapsing"><button type="button" onclick="visualizzaAttivita('{$item.code}');" class="ui fluid button">Visualizza</button></td>
        <td></td>
        {else}
        <td class="collapsing"><button type="button" onclick="modificaAttivita('{$item.code}');" class="ui fluid button">Modifica</button> </td>
        <td class="collapsing"><button type="button" onclick="deleteAttivita('{$item.code}');" class="ui fluid button">Elimina</button></td>
        {/if}    
    </tr>
    
    {if $smarty.foreach.t.last}
    </tbody>
    </table>
    
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

{/foreach*}
