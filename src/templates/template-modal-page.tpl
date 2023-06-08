{extends file="template-modal.tpl"}

{block name="html_head_extra"}
{/block}
    
{block name="content"}
<style>
    @media only screen and (max-width: 700px) {
        .actions {
            position: absolute;
            bottom: 0;
        }
    }
</style>

    <script>
    $(document).ready(function() {
        
        
    });

    </script>

    {* serve solo per capire chi è il padre *}
    <div id="modal{$modal_id}container" style="">{if !$MOBILE}</div>{/if}
    
        {block name="modal_header"}
        {if isset($header)}
        <div class="header" style="padding: 0.50rem 1.25rem;">
            {$header}
        </div>
        {/if}
        {/block}

        

        <div class="scrolling content">
        {block name="modal_content"}
            
        {/block}
        </div>

        <div class="actions" style="padding-top:4px; padding-bottom:4px; text-align: right;">
        {block name="modal_actions_footer"}
            <div class="ui cancel button">Chiudi</div>
        {/block}
        </div>
        
    {if $MOBILE}</div>{/if}

{/block}