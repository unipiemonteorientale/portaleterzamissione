{extends file="template-private.tpl"}




{block name="page_content"}

<script language="JavaScript" type="text/javascript">
var argomento = '{$argomento}';
$(function(){
    
    refreshMissione3ConfContent('{$anno}', '{$sezione}', '{$argomento}');
});

function refreshMissione3ConfContent(anno, sezione, argomento) {
    console.log('refreshMissione3ConfContent', anno, sezione, argomento);
    if (!sezione)
        sezione = '{$sezione}';
    if (sezione.length == 0)
        sezione = '{$sezione}';
    if (!anno)
        anno = '{$anno}';
    if (anno.length == 0)
        anno = '{$anno}';
    if (!argomento)
        argomento = '{$argomento}';
    if (argomento.length == 0)
        argomento = '{$argomento}';
    $('#missione3_conf_content').html('');
    $('#missione3_conf_content').load("{$APP.url}/inner/"+argomento+"/list?anno="+anno+"&sezione="+sezione);
}

</script>



<div class="ui secondary menu">
    <div class="left menu">
        
        <div class="item"><a class="ui primary button" href="{$APP.url}/list?argomento=domande">Domande</a></div>
        {if $operatore->has('MISSIONE3ADMIN')}
        <div class="item"><a class="ui primary button" href="{$APP.url}/list?argomento=documentazione">Documentazione</a></div>
        <div class="item"><a class="ui primary button" href="{$APP.url}/list?argomento=faq">FAQ</a></div>
        {/if}

    </div>
    
    <div class="right item">
        <a class="ui button" href="{$BASE_URL|default:'/'}">Indietro</a>
    </div>
</div>
<div class="ui divider"></div>


<div id="missione3_conf_content" style="margin: 4px; border: 0px solid orange;">


</div>
{/block}