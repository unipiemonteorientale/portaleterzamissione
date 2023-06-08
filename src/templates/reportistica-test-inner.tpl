 <html>
      <head>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
          <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

        <script type="text/javascript">
          google.charts.load("current", { packages:['corechart']});
          google.charts.setOnLoadCallback(drawChart);
          function drawChart() { 

            var data = google.visualization.arrayToDataTable([
              ['Element', 'Density', {  role: 'style' }],
              ['Copper', 8.94, '#b87333', ],
              ['Silver', 10.49, 'silver'],
              ['Gold', 19.30, 'gold'],
              ['Platinum', 21.45, 'color: #e5e4e2' ]
            ]);

            var options = { 
              title: "Density of Precious Metals, in g/cm^3",
              bar: { groupWidth: '95%'},
              legend: 'none',
            };
        // google chart 1
             var g_chart_1 = document.getElementById('g_chart_1');
             var g_chart_1 = new google.visualization.ColumnChart(g_chart_1);
                g_chart_1.draw(data, options);

            var chart_div = document.getElementById('chart_div');
            var chart = new google.visualization.ColumnChart(chart_div);

            google.visualization.events.addListener(chart, 'ready', function () { 
            
                 chart_div.innerHTML = '<img style="display:none" src="' + chart.getImageURI() + '" class="img-responsive">';
                 console.log('chart ready', chart.getImageURI());
                 console.log('chart ready', chart_div.innerHTML);
            });

            chart.draw(data, options);

        // google chart 2
        var g_chart_2 = document.getElementById('g_chart_2');
              var g_chart_2 = new google.visualization.LineChart(g_chart_2);
        g_chart_2.draw(data, options);

        var chart_div1 = document.getElementById('chart_div1');
            var chart1 = new google.visualization.LineChart(chart_div1);
            google.visualization.events.addListener(chart1, 'ready', function () { 

              chart_div1.innerHTML = '<img style="display:none" src="' + chart1.getImageURI() + '" class="img-responsive">';

              console.log(chart_div1.innerHTML);
            });

            chart1.draw(data, options);

        }
        </script>

      <div class="container" id="Chart_details">
          <div id='chart_div'></div><div id='g_chart_1'></div>
          <div id='chart_div1'></div><div id='g_chart_2'></div>
      </div>
          <div align="center">
             <form method="post" id="new_pdf" action="/terza-missione/reportistica/test/print">
              <input type="hidden" name="hidden_div_html" id="hidden_div_html" />
              <button type="button" name="create_pdf" id="create_pdf" class="btn btn-danger btn-xs">Create PDF</button>
             </form>
            </div>

      <script>
      $(document).ready(function(){ 
       $('#create_pdf').click(function(){ 
            console.log($('#Chart_details').html());
            $('#hidden_div_html').val($('#Chart_details').html());
            $('#new_pdf').submit();
       });
      });
      </script>

      </body>
      </html>
