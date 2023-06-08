

<script type="text/javascript">
google.charts.load('current', { 'packages':['corechart', 'table'] });
//google.charts.setOnLoadCallback(disegnaQuadrante3);

function drawCharts() {
    
    

    disegnaQuadrante3();
    disegnaQuadrante2();
}

var data2, chart2;
var data3, chart3;
  

// ***********************************************************************************
// QUADRANTE 3
// ***********************************************************************************
function disegnaQuadrante3() {
      
    $.ajax({
        type: 'POST',
        url: "{$APP.url}/json/pie{$versione}",
        data: $("#web3reportistica_form").serialize(),
        dataType:"json",
        async: true
    })
    .done(function( data ) {
        data3 = new google.visualization.DataTable(data);
        chart3 = new google.visualization.PieChart(document.getElementById('quadrante3'));
        
        var options = {
            title : "",
            height:400,
            chartArea: { width:"98%",height:"70%"},
            legend: { position: 'right', alignment: 'start', textStyle: { fontSize: 11, bold: false } }, 
            pieStartAngle: 0,
            tooltip: { textStyle:  {  fontSize: 11, bold: false } },
            is3D: false
        };
        
        //google.visualization.events.addListener(chart3, 'select', selectHandler3);
        google.visualization.events.addListener(chart3, 'ready', function () { 
            var chart_div1 = document.getElementById('chart_div1');
            chart_div1.innerHTML = '<img style="display:none" src="' + chart3.getImageURI() + '" class="img-responsive">';
            $("#btn_create_pdf").removeClass("invisible");
            $("#btn_download_image").removeClass("invisible");
            $("#btn_download_xls").removeClass("invisible");
            console.log('chart3, ready');
        });
        chart3.draw(data3, options);
    });
}

function selectHandler3() {
    return;
    var selectedItem = chart3.getSelection()[0];
    if (selectedItem) {
        selectedvalue = data3.getValue(selectedItem.row, 0);
        
        url = "/dashboard-patrimonio/ddu-sottoddu/"+selectedvalue;
        console.log(url);
        modal_page_new("ddusottoddu", url, "overlay fullscreen", function() { });
    }
}

// ***********************************************************************************
// QUADRANTE 2
// ***********************************************************************************
function disegnaQuadrante2() {
      
    $.ajax({
        type: 'POST',
        url: "{$APP.url}/json/pie{$versione}",
        data: $("#web3reportistica_form").serialize(),
        dataType:"json",
        async: true
    })
    .done(function( data ) {
        data2 = new google.visualization.DataTable(data);
        chart2 = new google.visualization.Table(document.getElementById('quadrante2'));
        
        var options = {
            title : "",
            height:400,
            chartArea: { width:"98%",height:"70%"},
            legend: { position: 'right', alignment: 'start', textStyle: { fontSize: 11, bold: false } }, 
            pieStartAngle: 0,
            tooltip: { textStyle:  {  fontSize: 11, bold: false } },
            is3D: false
        };
        
        //google.visualization.events.addListener(chart3, 'select', selectHandler3);
        
        google.visualization.events.addListener(chart2, 'ready', function () { 
            var chart_div2 = document.getElementById('chart_div2');
            //chart_div2.innerHTML = '<img style="display:none" src="' + chart2.getImageURI() + '" class="img-responsive">';
        });
        chart2.draw(data2);
    });
}

// ***********************************************************************************
// QUADRANTE 1
// ***********************************************************************************
function disegnaQuadrante1() {
    var codice_domanda = $("#codice_domanda").val();

    $('#quadrante1').html('');
    $('#quadrante1').load("{$APP.url}/inner/"+codice_domanda);
}

$(function(){
    $('.dropdown.domande').dropdown({ });
    
    $('#btn_create_pdf').click(function(){ 
        console.log($('#Chart_details').html());
        $('#hidden_div_html_pdf').val($('#Chart_details').html());
        $('#new_pdf').submit();
    });
    
    $('#btn_download_image').click(function(){ 
        console.log($('#Chart_details').html());
        $('#hidden_div_html_image').val($('#Chart_details').html());
        $('#download_image').submit();
    });
});
</script>


<div class="ui container">

<div class="ui icon yellow message">
    <i class="exclamation icon"></i>
    <div class="content">
    {if $versione == 1}
        <div class="header">
          Analisi qualitativa
        </div>
        <p>I grafici e i totali sono calcolati contando il numero di attività che hanno l'indicatore selezionato; non viene considerata la quantità che rappresenta il dato. Per avere una misura sulla quantità, usare l'Analisi quantitativa.<br>
        <em>Esempio: Indicatore sugli studenti. Pesano 1 tutte quelle attività che hanno studenti, ma non viene preso in considerazione il numero degli studenti.</em></p>
    {else}
        <div class="header">
          Analisi quantitativa
        </div>
        <p>I grafici e i totali sono calcolati sommando le quantità associate a ciascun indicatore.<br>
        <em>Esempio: Indicatore sugli studenti. Vengono sommati il numero degli studenti collegati all'attività, per cui un'attività con 1000 studenti partecipanti pesa per 1000 rispetto ad un'altra attività con 100 studenti che pesa per 100.</em></p>
    {/if}
    </div>
</div>
    
<form id="web3reportistica_form" class="ui form">
    
    <div id="web3formfields_modal" class="web3formfields">
        
        <input type="hidden" name="stato" value="{$filtri.stato}" />
        <input type="hidden" name="tipo_compilazione" value="{$filtri.tipo_compilazione}" />
        <input type="hidden" name="campo_azione" value="{$filtri.campo_azione}" />
        <input type="hidden" name="anno" value="{$filtri.anno}" />
        <input type="hidden" name="versione" value="{$versione}" />
        <input type="hidden" name="struttura" value="{$filtri.struttura}" />
    
        <div class="onexx fieldsxxx">
            <div class="field">
                <label>Selezionare una domanda a risposta multipla</label>
                <select id="codice_domanda" name="codice_domanda" class="ui fluid search dropdown domande">
                    <option value=""></option>
                    {foreach item=item from=$domande}<option value="{$item.codice}">{$item.sezione} | {$item.codice} | {$item.descrizione}</option>{/foreach}
                </select>
            </div>
        </div>
        
    </div>
</form>

<form method="post" id="new_pdf" action="/terza-missione/reportistica/download-pdf">
        <input type="hidden" name="hidden_div_html" id="hidden_div_html_pdf" />
</form>

<form method="post" id="download_image" action="/terza-missione/reportistica/download-image">
        <input type="hidden" name="hidden_div_html" id="hidden_div_html_image" />
</form>

<button class="ui button" id="btn_web3filters_modal_submit" onclick="drawCharts();">Genera</button>
<button class="ui button invisible" type="button" name="create_pdf" id="btn_create_pdf">Esporta PDF</button>
<button class="ui button invisible" type="button" name="download_image" id="btn_download_image">Immagine grafico</button>
<a class="ui button invisible" href="{$APP.url}/download-xls"id="btn_download_xls">XLS totali</a>


<div class="container" id="Chart_details">
    <div id='chart_div1'></div><div id='g_chart_1'></div>
    <div id='chart_div2'></div><div id='g_chart_2'></div>
</div>
      
<div align="center">
    
</div>

</div>

<div class="ui very compact stackable grid " style="border:1px solid grey; margin-top:8px;">
    
    <div class="sixteen wide mobile eight wide computer column" id="quadrante3" style="height: 420px; border: 0px solid orange;"></div>
    
    <div class="sixteen wide mobile eight wide computer column" id="quadrante2" style="height: 420px; border: 0px solid red;"></div>

    <div class="sixteen wide column" id="quadrante1" style="border: 0px solid blue;"></div>

</div>

{*
<div class="ui four cards">

  <div class="card">
    <div class="image" id="mini_quadrante1"></div>
    <div class="extra">
      Rating:
    </div>
  </div>

  <div class="card">
    <div class="image" id="mini_quadrante2"></div>
    <div class="extra">
      Rating:
    </div>
  </div>

  <div class="card">
    <div class="image" id="mini_quadrante3"></div>
    <div class="extra">
      Rating:
    </div>
  </div>

  <div class="card">
    <div class="image" id="mini_quadrante4"></div>
    <div class="extra">
      Rating:
    </div>
  </div>
  
</div>
*}
