{extends file="template-private.tpl"}




{block name="content"}

<script language="JavaScript" type="text/javascript">
$(function(){
    
    refreshMissione3DocumentazioneContent('{$anno}');
});

function refreshMissione3DocumentazioneContent(anno) {
    if (!anno)
        anno = '{$anno}';
    if (anno.length == 0)
        anno = '{$anno}';
    $('#missione3_documentazione_content').html('');
    $('#missione3_documentazione_content').load("{$APP.url}/inner/list?anno="+anno);
}

</script>


<div style="margin: 6px; border: 0px solid orange;">

    
    <div id="missione3_documentazione_content" style="margin-top: 4px;">


    </div>
</div>
{/block}