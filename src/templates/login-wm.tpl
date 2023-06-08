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
        //tid = setInterval(timerfunc, 5000);

        $('.ui.form')
        .form({
          fields: {
            uname: {
              identifier  : 'uname',
              rules: [
                {
                  type   : 'empty',
                  prompt : "Inserisci il tuo username"
                }
              ]
            },
            passwd: {
              identifier  : 'passwd',
              rules: [
                {
                  type   : 'empty',
                  prompt : 'Inserisci la password'
                },
                {
                  type   : 'length[7]',
                  prompt : 'La password deve essere di almeno 7 caratteri'
                }
              ]
            }
          },
        onSuccess: function(event, fields) {
            event.preventDefault();
            $.ajax({
                type: 'post',
                url: '{$BASE_URL}/api/login',
                data: $('form').serialize(),
                success: function () {
                    //"ok" label on success.
                    //$('#successLabel').css("display", "block");
                    console.log("success");
                }
            })
            .done(function( data ) {
                console.log(data);
                if (data.trim() == 'OK') {
                    window.location = "/";
                }
                else if (data == 'KO') {
                    $('.ui.error.message').html("Errore nel login");
                    $('.ui.form').addClass('error'); //response.msg
                }
                else {
                    var msg = JSON.parse(data);
                    if (msg['result']) {
                        window.location = msg['customs']['url'];
                    }
                    else
                        ShowMessage(msg, false);
                }
            });      
        }
        });
    
    
        $(document).keypress(function(event){
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13'){
                $("#btnLogin").click();
            }
        });
        
        $("#uname").focus();
        
        
    });
    
</script>
{/block}

{block name="html_body"}
<div class="ui middle aligned center aligned grid">
    <div class="column">
    
     
    
        <form class="ui large form" method="POST">
        
          <div class="ui stacked segment">
          
          <div class="ui header primary" style="margin:30px 0px;">
            <img src="{$STATIC_URL}/img/logo_login.jpg" style="width: 70px;"/> Accedi a {$APPNAME}
          </div>
        
            
            <div class="field">
              <div class="ui left icon input">
                <i class="user icon"></i>
                <input type="text" name="uname" id="uname" placeholder="Nome utente" />
              </div>
            </div>
            <div class="field">
              <div class="ui left icon input">
                <i class="lock icon"></i>
                <input type="password" name="passwd" placeholder="Password" />
              </div>
            </div>
            
            <div style="text-align:right;">
                <button type="button" id="btnLogin" class="ui left aligned secondary submit button">Login</button>
            </div>
          </div>

          <div class="ui error message">Login fallita!</div>

        </form>
        <br>

        
          <a class="ui primary fluid button" href="{$BASE_URL}/sso">Log in personale UPO</a>

        {*
        <div class="ui message">
          Non hai un account? <a href="/registrazione">Registrati</a>
        </div>

        <div class="ui message">
          Hai dimenticato la password? <a href="/registrazione/recupera-password">Recuperala</a>
        </div>
        
        
        <div class="center aligned ">
        Ritorna a <a href="/">Home</a>
        </div>
        *}
        
        <div class="copyright">
        </div>
    
    </div>
</div>
{/block}
