
<script language="JavaScript" type="text/javascript">

$(function(){
    
});

</script>


<div class="ui secondary menu">
    <div class="left menu">
        
        <div class="item">
            <h3>Documentazione</h3>
        </div>
    </div>
    
    <div class="right item">
        <a class="ui button" href="{$BASE_URL|default:'/'}">Indietro</a>
    </div>
</div>



<div class="ui middle aligned divided  very relaxed list">

{foreach name="t" key=key item=item from=$risorse}

    
    <div class="item">
        <div class="left floated content">
            {if $item.file_locazione|count_characters}<a download href="{$item.file_url}"><i class="file big icon "></i></a>
            {elseif $item.url_risorsa|count_characters}<a href="{$item.url_risorsa}" target="_blank"><i class="external alternate big icon"></i></a>
            {/if}
        </div>
        <div class="content">
            <h4>{$item.file_etichetta}</h4>
            {if $item.file_locazione|count_characters}
            {elseif $item.url_risorsa|count_characters}<a href="{$item.url_risorsa}" target="_blank">{$item.url_risorsa}</a>
            {else}non indicato
            {/if}
        </div>
    </div>
    
    
    {*
    <div class="ui clearing segment">
        
        <div class="ui right floated">
        {if $item.file_locazione|count_characters}<a download href="{$item.file_url}"><i class="file icon large"></i></a>
        {elseif $item.url_risorsa|count_characters}<a href="{$item.url_risorsa}" target="_blank">{$item.url_risorsa}</a>
        {else}non ancora indicato
        {/if}
        </div>
        
        <h3>{$item.file_etichetta}</h3>
    </div
    <hr>
    *}
    
{foreachelse}
<div class="ui message">
  <div class="header">Nessun documento</div>
  <p>Non è presente documentazione.</p>
</div>
{/foreach}

</div>











