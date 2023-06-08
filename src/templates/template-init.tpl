<!DOCTYPE html>
<html>
<head>
{block name="html_head"}
    <!-- Standard Meta -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <!-- Site Properties -->
    <title>{if $TEST}TEST {/if}{$APPNAME}</title>

    <link rel="shortcut icon" type="image/ico" href="{$STATIC_URL}/img/favicon.ico"/>
        {*<script language="JavaScript" type="text/javascript" src="{$STATIC_URL}/jquery.js"></script>*}
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.3/dist/jquery.min.js"></script>


    <link href="https://fonts.googleapis.com/css?family=Montserrat|Open+Sans" rel="stylesheet"> 
    
    {* FOMANTIC UI
    <link rel="stylesheet" type="text/css" href="{$STATIC_URL}/semantic-dist/semantic.min.css">
    <script src="{$STATIC_URL}/semantic-dist/semantic.min.js"></script>
    *}
    
    {*<script src="{$STATIC_URL}/jquery-ui.min.js"></script>*}
    
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.1/dist/semantic.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.1/dist/semantic.min.js"></script>
    
    {* MOMENT
    <script type="text/javascript" src="{$STATIC_URL}/moment-with-locales.min.js"></script>
    *}
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment-with-locales.min.js"></script>
    
    {* TABULATOR
    <link href="{$STATIC_URL}/tabulator-dist/css/tabulator.min.css" rel="stylesheet">
    <script type="text/javascript" src="{$STATIC_URL}/tabulator-dist/js/tabulator.min.js"></script>
    <script type="text/javascript" src="{$STATIC_URL}/tabulator-dist/js/jquery_wrapper.min.js"></script>

    <link href="{$STATIC_URL}/tabulator-dist/css/semantic-ui/tabulator_semantic-ui.min.css" rel="stylesheet">
    
    <link href="https://unpkg.com/tabulator-tables@4.1.4/dist/css/tabulator.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.1.4/dist/js/tabulator.min.js"></script>
    *}
    
    <link href="https://unpkg.com/tabulator-tables@4.9.3/dist/css/tabulator.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.9.3/dist/js/tabulator.min.js"></script>
    
    
    <link rel="stylesheet" type="text/css" href="{$STATIC_URL}/css/style.css" />
    <script type="text/javascript" src="{$STATIC_URL}/web3.js"></script>
    
    <script>
    
    let isMobile = window.matchMedia("only screen and (max-width: 760px)").matches;

    if (isMobile) {
        console.log("MOBILE");
    }
    $(document).ready(function() {
        
    }); // fine OnReady.
    
    
    function messageManager(data, func_ok, func_ko) {
        console.log("messageManager", data);
        
        if (func_ok === undefined) {
            func_ok = function() { };
        }
        
        if (func_ko === undefined) {
            func_ko = function() { };
        }
    
        if (data.trim() == 'OK') {
            func_ok();
        }
        else if (data == 'KO') {
            func_ko();
        }
        else {
            var msg = JSON.parse(data);
            if (msg['result']) {
                if (msg['customs'])
                    if (msg['customs']['url'])
                        window.location = msg['customs']['url'];
            }
            else
                ShowMessage(msg, false);
        }
    }
    
    </script>
    
    <style>
    
    </style>
    
    {block name="html_head_default_style"}
    {/block}
  
    {block name="html_head_extra"}
    {/block}
    
{/block}
</head>
<body>
{block name="html_body"}
{/block}


<div class="ui modal" id="modal_message">
    <div class="ui block header center aligned" style="color:white; background:var(--colorPrimaryDark); padding: 5px;">
        <i class="announcement large icon"></i> {$APPNAME}
    </div>
    <div class="content">
        Messaggio
    </div>
    <div class="actions">
        <div class="ui primary ok  button" id="modal_message_ok_button" style="">
            <i class="checkmark icon"></i> OK
        </div>
        <div class="ui green ok button" id="modal_message_si_button" style="display: none;">
            <i class="checkmark icon"></i> SI
        </div>
        <div class="ui red cancel button" id="modal_message_no_button" style="display: none;">
            <i class="remove icon"></i> NO
        </div>
    </div>
</div>



</body>
</html> 