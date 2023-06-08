
function redirect(url) {
    window.location = url;
}

function reload(id, url) {
    $('#'+id).html('<div class="ui text loader">Loading</div>');
    $('#'+id).load(url);
}

function refreshPage() {
    location.href = location.href;
}
    
function navigate(href, newTab) {
    var a = document.createElement('a');
    a.href = href;
    if (newTab) {
        a.setAttribute('target', '_blank');
    }
    a.click();
}

function ShowMessage(msg, closeable=true, funcOK=function() {}, funcKO=function() {}) {
    // TODO Check msg format
    $('#modal_message .content').html("<p>"+msg.description+"</p>");
    //$('#modal_message #message_title').html(msg.title);
    if (msg.level == "WARNING") {
        $('#modal_message_si_button').css({ 'display':'initial' });
        $('#modal_message_no_button').css({ 'display':'initial' });
        $('#modal_message_ok_button').css({ 'display':'none' });
        $('#modal_message .content').append("<p>Vuoi proseguire?</p>");
    }
    else {
        $('#modal_message_si_button').css({ 'display':'none' });
        $('#modal_message_no_button').css({ 'display':'none' });
        $('#modal_message_ok_button').css({ 'display':'initial' });
    }
    $('#modal_message')
        .modal({
            allowMultiple: true,
            inverted: false,
            closable  : closeable,
            onApprove : funcOK,
            onDeny : funcKO
          })
        .modal('show');
}

function modal_popup_url(url, onhide=function() {}) {
    
    $('#modal_wizard_page .content').load(url);
    $('#modal_wizard_page')
                .modal({
                    inverted: false,
                    allowMultiple: true,
                    closable  : true,
                    //onApprove : function() { $("#frmOggetto").submit() }
                    onHide: onhide
                })
                .modal('show');
}


function modal_page_new(id, url, size, onHideFunc, onApproveFunc) {
    //var modal = $(".ui.modal.page").clone().appendTo('body');
    //modal.attr("id", id);
    console.log("modal_page_new: "+id);
    var ok_func = false;
    
    if (onApproveFunc === undefined) {
        onApproveFunc = function() { };
    }
    else
        console.log("onApproveFunc indicato");
    
    $('body').append('<div class="ui '+size+' modal page" id="'+id+'"><div class="ui text loader">Loading</div></div>');
    $('#'+id).load(url);
    $('#'+id)
        .modal({
            inverted: false,
            allowMultiple: true,
            closable  : false,
            onApprove : function(element) { console.log('modal approve ' + id); onApproveFunc(); return true; }, // return true chiude il form, false lo lascia aperto.
            onDeny: function(element) { console.log('modal deny ' + id); },
            onHide: function(element) { console.log('modal hide ' + id); onHideFunc(); },
            onHidden: function() { 
                console.log('modal hidden ' + id); 
                modal_page_close(id);
                $('#'+id).remove();
                if (ok_func)
                    onApproveFunc();
                //$('body').detach('#'+id);
                //$('body').remove('#'+id);
            }
            
        })
        .modal('setting', 'transition', 'vertical flip')
        .modal('show');
}

function modal_page_close(id) {
    console.log("modal_page_close: "+id);
    $('#'+id).modal('hide');
    $('#'+id).modal('hide dimmer');
    //$('body').detach('#'+id);
    //$('body').remove('#'+id);
}