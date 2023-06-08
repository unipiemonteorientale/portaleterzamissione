
<script language="JavaScript" type="text/javascript">

$(function(){
});
function modificaConfigurazioneDomanda(record_code) {
    modal_page_new(
        'modificaConfigurazioneDomanda',
        '{$APP.url}/'+record_code+'/',
        'overlay fullscreen', 
        refreshMissione3ConfContent, 
        refreshMissione3ConfContent
    );
}

function visualizzaAttivita(record_code) {
    modal_page_new(
        'modificaAttivita',
        //'/admin/form/{$context_name}/{$object_code}/update?{$pks}='+record_code,
        //'{$BASE_URL}/admin/objects/{$object_code}/update/'+record_code,
        '{$APP.url}/wizard/read?record_code='+record_code,
        'overlay fullscreen', 
        refreshMissione3ConfContent, 
        refreshMissione3ConfContent
    );
}

function nuovaAttivita() {
    modal_page_new(
        'nuovaAttivita', 
        "{$APP.url}/wizard/insert", 
        'overlay fullscreen', 
        refreshMissione3ConfContent, 
        refreshMissione3ConfContent
    );
}

function filtraElenco() {
    var anno = $("#filtro_anno").val();
    var sezione = $("#filtro_sezione").val();
    refreshMissione3ConfContent(anno, sezione);
}

function mostraHelp(domanda, sezione) {
    
    
}
</script>



<div class="ui secondary menu">
    <div class="left menu">
        
        {*<div class="item"><button class="ui primary button" onclick="nuovaAttivita();">Inserisci nuova attività</button></div>
        <div class="item"><button class="ui primary button" onclick="filtraAttivita();">Filtri</button></div>
        <button class="ui primary button">Esporta</button>*}
        

        <div class="item">
            <h3>Domande sezione: 
                <select class="ui inline dropdown" onchange="filtraElenco();" id="filtro_sezione">
                    <option value="AP" {if $sezione == 'AP'}selected=""{/if}>AP</option>
                    <option value="A" {if $sezione == 'A'}selected=""{/if}>A</option>
                    <option value="B" {if $sezione == 'B'}selected=""{/if}>B</option>
                    <option value="C" {if $sezione == 'C'}selected=""{/if}>C</option>
                    <option value="D" {if $sezione == 'D'}selected=""{/if}>D</option>
                    <option value="E" {if $sezione == 'E'}selected=""{/if}>E</option>
                    <option value="F" {if $sezione == 'F'}selected=""{/if}>F</option>
                    <option value="G" {if $sezione == 'G'}selected=""{/if}>G</option>
                    <option value="H" {if $sezione == 'H'}selected=""{/if}>H</option>
                    <option value="I" {if $sezione == 'I'}selected=""{/if}>I</option>
                    <option value="J" {if $sezione == 'J'}selected=""{/if}>J</option>
                    <option value="CH" {if $sezione == 'CH'}selected=""{/if}>CH</option>
                </select>
            </h3>
        </div>

        <div class="item">
            <h3>Anno: 
                <select class="ui inline dropdown" onchange="filtraElenco();" id="filtro_anno">
                    <option value="2023" {if $anno == '2023'}selected=""{/if}>2023</option>
                    <option value="2022" {if $anno == '2022'}selected=""{/if}>2022</option>
                    <option value="2021" {if $anno == '2021'}selected=""{/if}>2021</option>
                    <option value="2020" {if $anno == '2020'}selected=""{/if}>2020</option>
                </select>
            </h3>
        </div>
    </div>
    
    {*<div class="right item">
        <a class="ui button" href="{$BASE_URL}">Indietro</a>
    </div>*}
</div>


 

{foreach name="t" key=key item=item from=$domande}

    {if $smarty.foreach.t.first}
    <table class="ui celled striped very compact table">
    <thead>
        <tr>
            <th>Anno</th>
            <th>Codice domanda</th>
            <th>Testo domanda</th>
            <th>Help breve</th>
            <th colspan="2">Azioni</th>
        </tr>
    </thead>
    <tbody>
    {/if}
    
    
    
    <tr>
        <td class="collapsing"><h1>{$item.anno}</h1></td>
        <td class="collapsing"><h2>{$item.codice_domanda}</h2></td>
        <td class="">{$item.domanda}</td>
        <td class="">{$item.help_breve}</td>
        
        <td class="collapsing"><button type="button" onclick="modificaConfigurazioneDomanda('{$item.codice_domanda}');" class="ui gray fluid button">Modifica</button> </td>
        <td class="collapsing">{*<button type="button" onclick="" class="ui red button">Elimina</button>*}</td>
    </tr>
    
    {if $smarty.foreach.t.last}
    </tbody>
    </table>
    
    {*<div class="ui centered pagination menu">
        {for $p = 1 to $pages}
        <a class="{if $current_page == $p}active red{/if} item" onclick="refreshMissione3ConfContent({$p});">{$p}</a>
        {/for}
    </div>*}
    {/if}
{foreachelse}
<div class="ui message">
  <div class="header">Nessuna domanda</div>
  <p>Non sono presenti domande nell'anno e nella sezione indicate.</p>
</div>

{/foreach}
