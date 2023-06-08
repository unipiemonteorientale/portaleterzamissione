
<script language="JavaScript" type="text/javascript">

$(function(){
    $(".faq.accordion").accordion();
});

</script>


<div class="ui secondary menu">
    <div class="left menu">
        
        <div class="item">
            <h3>F.A.Q.</h3>
        </div>
    </div>
    
    <div class="right item">
        <a class="ui button" href="{$BASE_URL|default:'/'}">Indietro</a>
    </div>
</div>

{foreach name="t" key=key item=item from=$faqs}

    {if $smarty.foreach.t.first}
    <div class="ui styled fluid faq accordion">
    {/if}
    
    <div class="title">
        <i class="dropdown icon"></i>  {$item.domanda}
    </div>
    <div class="content">
        <p class="transition hidden">{$item.risposta}</p>
    </div>

    {if $smarty.foreach.t.last}
    </div>
    {/if}
    
    
{foreachelse}
<div class="ui message">
  <div class="header">Nessuna FAQ</div>
  <p>Non sono presenti FAQ per questa sezione.</p>
</div>

{/foreach}




















{*foreach name="t" key=key item=item from=$faqs}


    
    <div class="ui clearing segment">
        <h1>{$item.domanda}</h1>
        <p>{$item.risposta}</p>
    </div
    
    <hr>
    
    
{foreachelse}
<div class="ui message">
  <div class="header">Nessuna FAQ</div>
  <p>Non sono presenti FAQ per questa sezione.</p>
</div>

{/foreach*}
