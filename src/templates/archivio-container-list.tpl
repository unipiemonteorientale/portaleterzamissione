{extends file="template-private.tpl"}




{block name="page_content"}

<script language="JavaScript" type="text/javascript">
var page = 1;
$(function(){
    
    refreshMissione3ArchivioContent(1);
});

function refreshMissione3ArchivioContent(_page) {
    if (_page)
        page = _page;
    $('#missione3_archivio_content').html('');
    $('#missione3_archivio_content').load("{$APP.url}/inner/list?page="+page);
}

</script>






<div id="missione3_archivio_content" style="margin: 4px; border: 0px solid orange;">


</div>
{/block}