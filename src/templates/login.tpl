{extends file="template-public.tpl"}


{block name="html_head_extra"}




<style type="text/css">
    body {
      background: #DADADA ;
    }
    
    body > .grid {
      height: 100%;
    }
    .image {
      margin-top: -100px;
    }
    .column {
      max-width: 450px;
    }
</style>
<script>
var tid, i=1;

$(document)
    .ready(function() {
    });
    
</script>
{/block}

{block name="html_body"}
<div class="ui middle aligned center aligned grid">
    <div class="column">
    
     
        <br>
        <a class="ui primary fluid button" href="{$BASE_URL}/sso" title="Login UNIUPO">Login personale UPO</a>

        <br>
        <br>

        <a class="ui blue fluid button" href="{$BASE_URL}/login-wm" title="Login Webmanagement">Login Webmanagement</a>

        <div class="copyright">
        </div>
    
    </div>
</div>
{/block}
