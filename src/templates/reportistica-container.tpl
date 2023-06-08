{extends file="template-private.tpl"}




{block name="page_content"}

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script language="JavaScript" type="text/javascript">
var page = 1;
$(function(){
    refreshMissione3ReportisticaContent();
});

function refreshMissione3ReportisticaContent(_page) {
    if (_page)
        page = _page;
    /*if (page == 1) {
        $('#missione3_reportistica_content').html('');
        $('#missione3_reportistica_content').load("{$APP.url}/1/inner");
    }
    else if (page == 2) {
        $('#missione3_reportistica_content').html('');
        $('#missione3_reportistica_content').load("{$APP.url}/2/inner");
    }*/
    
    $.ajax({
        type: 'POST',
        url: '{$APP.url}/'+page+'/inner',
        data: $("#web3reportistica_parent_form").serialize(),
        success: function () {
            $('#missione3_reportistica_content').html('');
        }
    })
    .done(function( data ) {
        $('#missione3_reportistica_content').html(data);
    }); 
}

</script>


<div class="ui container">
    <div class="ui text large stackable menu">
        <div class="item">
            <div class="item"><button class="ui primary button" onclick="refreshMissione3ReportisticaContent(1);">Report qualitativo su indicatori</button></div>
            <div class="item"><button class="ui primary button" onclick="refreshMissione3ReportisticaContent(2);">Report quantitativo su indicatori</button></div>
            <div class="item">
                <a class="ui button" href="{$BASE_URL|default:'/'}">Indietro</a>
            </div>
        </div>
    </div>


    <form id="web3reportistica_parent_form" class="ui form">
        
        <div id="web3formfields_modal" class="web3formfields">
            <div class="five fields">
                <div class="field" id="anno">
                    <label>Anno</label>
                    <select name="anno" class="ui fluid anno dropdown" onchange="refreshMissione3ReportisticaContent();">
                        <option value=""></option>
                        {foreach item=item from=$anni}<option value="{$item}">{$item}</option>{/foreach}
                    </select>
                </div>
                <div class="field" id="campo_azione">
                    <label>Campo d'azione</label>
                    <select id="filter_campo_azione" name="campo_azione" class="ui fluid campo_azione clearable dropdown" onchange="refreshMissione3ReportisticaContent();">
                        <option value=""></option>
                        {foreach item=item from=$sezioni}<option value="{$item}">{$item}</option>{/foreach}
                    </select>
                </div>
                <div class="field" id="tipo_compilazione">
                    <label>Tipologia di compilazione</label>
                    <select name="tipo_compilazione" class="ui fluid tipo_compilazione clearable dropdown" onchange="refreshMissione3ReportisticaContent();">
                        <option value=""></option>
                        <option value="B" {if $filtri.tipo_compilazione == 'B'}selected{/if}>Base</option>
                        <option value="V" {if $filtri.tipo_compilazione == 'V'}selected{/if}>Valutazione</option>
                    </select>
                </div>
                <div class="field" id="stato">
                    <label>Stato attività</label>
                    <select name="stato" class="ui fluid stato clearable dropdown" onchange="refreshMissione3ReportisticaContent();">
                        <option value=""></option>
                        <option value="V" {if $filtri.stato == 'V'}selected{/if}>Validata</option>
                        <option value="B" {if $filtri.stato == 'B'}selected{/if}>Bozza</option>
                    </select>
                </div>
                <div class="field" id="struttura">
                    <label>Strutture</label>
                    <select name="struttura" class="ui fluid struttura clearable dropdown" onchange="refreshMissione3ReportisticaContent();">
                        <option value="">Tutto l'Ateneo</option>
                        {foreach item=item from=$strutture}<option value="{$item.codice}">{$item.etichetta}</option>{/foreach}
                    </select>
                </div>
            </div>
            
                
        </div>
    </form>
</div>    


        
<div id="missione3_reportistica_content" style="margin: 4px; border: 0px solid orange;"></div>
    


{/block}