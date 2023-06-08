{extends file="template-init.tpl"}


{block name="html_head_extra"}

{/block}


{block name="html_head_default_style"}
<style type="text/css">
#web3footer { background: rgb(138, 138, 141); padding: 8px; }
#web3footer p { text-align: center; }
.invisible { display: none !important; }
</style>
<script>
$(document).ready(function() {
    if (window.document.documentMode) {
        console.log('internet explorer');
        alert("ATTENZIONE! Internet Explorer non è più supportato e non è compatibile con questo sito. Ti consigliamo di usare Mozilla Firefox, Google Chrome o Microsoft Edge.");
    }

    // fix menu when passed
    $('.masthead').visibility({
        once: false,
        onBottomPassed: function() {
            $('.fixed.menu').transition('fade in');
        },
        onBottomPassedReverse: function() {
            $('.fixed.menu').transition('fade out');
        }
    });

    // create sidebar and attach to menu open
    $('.ui.sidebar')
        .sidebar({
            // Overlay will mean the sidebar sits on top of your content
            transition: 'overlay'
        })
        .sidebar('attach events', '.toc.item');
  
    $('.ui.dropdown').dropdown();
});
</script>
{/block}

{block name="html_head_extra"}

{/block}

{block name="html_body"}




<div style="border-top: 0px solid teal; width:100%; z-index:99; position:absolute; bottom:0px;" id="content_bottom_limit"></div>
    
<!-- Page -->
<div id="page" class="pusher">
    {block name="page_superheader"}{/block}
    
    {block name="page_header"}
    <div id="page_header" class="ui" style="background: #ce181e;">
        {block name="header"}
      
      
        
        
        {if $TEST}
        <div role="alert" class="ribbon">{$RIBBON}</div>
        {/if}       
        <header id="web3header">
            <div class="ui container masthead">
            
                <div class="ui large secondary menu">
                    <a class="header-logo-link" href="/">
         
                        <img src="{$STATIC_URL}/img/logo_upo_rosso.png" style="height: 87px;" />
                    </a>
                    <a class="header-logo-link" href="/">
         
                        <img src="{$STATIC_URL}/img/logo_simnova.png" style="height: 87px;" />
                    </a>
                    
                </div>
            </div>
        </header>

		
      
      
      
        {/block}
    </div>
    {/block}
  
    <!-- Page Contents -->
    <div id="page_content" class="ui">
	{block name="page_menu"}
    	{if $login}
        <div class="ui container" style="margin-top:5px; text-align:right;">
			<a class="ui primary " href="/iscrizione/home" title="Home"><i class="home icon"></i></a>
			<a class="ui primary " href="/logout" title="Esci"><i class="sign out alternate icon"></i></a>
        </div>
		{/if}
    {/block}
    
    {block name="page_content"}
        <div class="ui container" style="">
        {block name="content"}
        {/block}
        </div>
    {/block}
    </div>
  
  
    <!-- Page Footer -->
    {block name="page_footer"}
    <footer id="web3footer">
        <div class="web3footer footer-closure">
            <div class="ui container">
                {*<p>Universit&agrave; degli Studi del Piemonte Orientale <em>Amedeo Avogadro</em> |
                Rettorato, via Duomo, 6 - 13100 Vercelli
                </p>*}
                
                <p>
                    <a href="https://www.uniupo.it/it/la-privacy-e-cookie-policy-di-questo-sito">Codice privacy</a> | RSS
                </p>
                <p class="address">
                    <em> Centro Interdipartimentale di Didattica Innovativa e di Simulazione in Medicina e Professioni Sanitarie Polo Formativo delle Professioni Sanitarie, Via Lanino 1 - 28100 Novara</em>
                </p>
            </div>
        </div>
    </footer>
    {/block}
</div>
{/block}
