<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xml:lang="it" xmlns="http://www.w3.org/1999/xhtml" lang="it"><head>
<head>
    <title>Terza Missione&nbsp;-&nbsp;Universit&egrave; degli Studi del Piemonte Orientale&nbsp;Amedeo Avogadro</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    

<style type="text/css">
@page {
	size: portrait; 
    width: 210mm;
	margin: 10mm; /* <any of the usual CSS values for margins> */
	             /*(% of page-box width for LR, of height for TB) */
	margin-header: 7mm; /* <any of the usual CSS values for margins> */
	margin-footer: 7mm; /* <any of the usual CSS values for margins> */
    margin-top: 40mm;
    margin-bottom: 25mm;
	header: myheader;
	footer: myfooter;
	background: white;
	
}
body { 
    font-family: Verdana, sans-serif;
    font-size: 11px; 
    font-weight: normal;
}
h1 { font-size: 13px; 
    margin-top: 5mm; 
    margin-left: -3mm;
    background: #555; 
    color: #FFF; 
    padding: 2mm; 
    -webkit-border-radius: 1mm;
    -moz-border-radius: 1mm;
    border-radius: 1mm;
}
h2 { font-size: 12px; margin-top: 4mm; }
h3 { font-size: 11.5px; margin-top: 3mm; }
h4 { font-size: 0.8em; }

th {
    background: #999; 
}

th, td { border: 1px solid black; margin: 1px; padding: 2px 4px; }

.bg_rosso {
    background: rgba(206, 24, 30, 1); /* ROSSO */
}
.bold { font-weight: bold; }

.bianco { color: white; }
.grigio { color: rgba(138, 138, 141, 1); } /* GRIGIO */
.rosso { color: rgba(206, 24, 30, 1); } /* ROSSO */

.sx { text-align: left; }
.dx { text-align: right; }
.center { text-align: center; }
.justify { text-align: justify; }

#content {
    margin-left: 5mm;
    margin-right: 4mm;
}


</style>
</head>
<body>

<!--mpdf
<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->

<!-- CONTAINER ***************************************************************************** -->
<div id="container">	

    <!-- HEADER ***************************************************************************** -->
    <htmlpageheader name="myheader" id="header" style="padding:30px;">
		<div style="padding-bottom:7mm; border-bottom: 1px solid #999;"><img src="{$STATIC_URL}/img/logo_pdf.jpg" style="height: 15mm;" /></div>
        {block name="custom_header"}{/block}
	</htmlpageheader>
    
    <!-- CONTENT ***************************************************************************** -->
    <div id="content">
    {block name="content"}
    
    {/block}
    </div>

    <!-- FOOTER ***************************************************************************** -->
    <htmlpagefooter name="myfooter" id="footer">
        {block name="custom_footer"}{/block}
        <div style="border-top: 1px solid #999;">
            <div class="center" style="font-size:0.8em; margin:5mm;">{literal}{PAGENO}{/literal} / {literal}{nb}{/literal}</div>
            <div class="center" style="font-size:0.5em; margin:5mm;">&nbsp;</div>
            <div class="center" style="font-size:0.7em; margin:5mm;">Universit&egrave; degli Studi del Piemonte Orientale <em>Amedeo Avogadro</em> | Rettorato, via Duomo, 6 - 13100 Vercelli</div>
        </div>
	</htmlpagefooter>
</div>
</body></html>