{extends file="template-private.tpl"}

{block name="html_head_extra"}

<style>

</style>

<script language="JavaScript" type="text/javascript">
$(function(){

});

</script>
{/block}


{block name="content"}

<style>

    
    .ui.cards > .card > .image { padding: 10px; }
</style>

<br />
<br />
<center>

<div class="ui link four stackable cards">
    {foreach name=macro item=item key=key from=$missione3_home_menu}
    <a class="card" href="{$item.link}">
        <div class="image">
            <i class="{$item.image} huge icon"></i>
        </div>
        <div class="content">
            <div class="header">{$item.title}</div>
        </div>
    </a>
    {/foreach}
</div>


</center>
<br />
<br />


<div class="ui icon message">
  <i class="calendar icon"></i>
  <div class="content">
    <div class="header">
      Scadenze
    </div>
    <p>Deadline Monitoraggio anno 2023: 28 febbraio 2024</p>
  </div>
</div>

<br />


<h4 class="ui horizontal left aligned divider header">
  Referenti
</h4>

<div class="ui cards">
    <div class="card">
        <div class="content">
            <div class="header">Marcello Sarino</div>
            <div class="meta">Settore Ricerca - Incubatore Enne3</div>
            <div class="description">
                <a href="mailto:marcello.sarino@uniupo.it">marcello.sarino@uniupo.it</a><br>
                (campi A B C D E I J)
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="content">
            <div class="header">Monica Ricupero</div>
            <div class="meta">Settore Alta Formazione</div>
            <div class="description">
                <a href="mailto:monica.ricupero@uniupo.it">monica.ricupero@uniupo.it</a><br>
                (campo F)
            </div>
        </div>
    </div>
    <div class="card">
        <div class="content">
            <div class="header">Selena Agnella</div>
            <div class="meta">Staff del Rettore e Comunicazione</div>
            <div class="description">
                <a href="mailto:selena.agnella@uniupo.it">selena.agnella@uniupo.it</a><br>
                (campi G H)
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="content">
            <div class="header">Simonetta Todi</div>
            <div class="meta">Ufficio Data Mining and Managing</div>
            <div class="description">
                <a href="mailto:simonetta.todi@uniupo.it">simonetta.todi@uniupo.it</a><br>
                (analisi dati e upgrade)
            </div>
        </div>
    </div>

</div>

{/block}