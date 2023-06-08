{extends file="template-modal-page.tpl"}

{block name="modal_actions_footer"}
<button class="ui ok button" style="display:none;">
    <i class="close icon"></i> OK
</button>  
<button class="ui primary disabled button creautente" onclick="modal_user_new();" title="Crea l'utente">
    <i class="plus icon"></i> Crea
</button> 
<button class="ui grey cancel button">
    <i class="close icon"></i> Chiudi
</button>  
{/block}




{block name="modal_content"}








<script>
var user_uid = null;

$(document).ready(function() {
    $('.ui.dropdown.personale').dropdown({
        clearable: true,
        ignoreCase: true,
        ignoreDiacritics: true,
        fullTextSearch: 'true',
        preserveHTML: false,
        minCharacters: 3,
        apiSettings: {
            url: "{$BASE_URL}/api/admin/personale/{literal}{query}{/literal}"
        },
        saveRemoteData: false,
        onChange: function(value, text, $selectedItem) {
            // custom action
            console.log(value);
            console.log(text);
            console.log($selectedItem);
            user_uid = value;
            $("#div_info_utente").load("{$BASE_URL}/api/admin/html/info/users/"+user_uid);
            $(".ui.button.creautente").removeClass("disabled");
        }
    });

});


function modal_user_new() { 
    console.log("modal_user_new");
    /*if (users_selected.length == 0) {
        alert("Nessun utente selezionato");
        return false;
    }
    if (visibilities_selected.length == 0) {
        alert("Nessuna visibilità selezionata");
        return false;
    }*/
    //return;
    $.ajax({
        type: "POST",
        url: "{$BASE_URL}/api/admin/user/new",
        data: { uid: user_uid },
        success: function () {
            console.log("modal_user_new success");
        }
    })
    .done(function( data ) {
        console.log(data);
        //return false;
        if (data.trim() == 'OK') {
            //redirect("/admin/config/users/list"); //
            $(".ui.ok.button").click();
        }
        else if (data.trim() == 'KO') {
            var msg = JSON.parse('{ "description":"Errore non specificato." }');
            ShowMessage(msg, false);
        }
        else {
            var msg = JSON.parse(data);
            ShowMessage(msg, false, function() { 
                if (msg.result) {
                    //redirect("/admin/config/users/list");
                    $('#modal_message').modal('hide');
                    $(".ui.ok.button").click();
                }
            });
        }
    });   
}
</script>


<div>
    <div class="ui header">Personale UniUPO</div>
    <div class="ui fluid personale search selection dropdown" style="margin-top: 15px;">
        <input type="hidden" name="matricola">
        
        <div class="default text">Cerca una persona per uid, matricola, cognome o nome</div>
        <div class="menu">
        </div>
    </div>
    
    <div id="div_info_utente"><br><br><br><br><br><br><br></div>

</div>

{/block}z