<?php
//  
// Copyright (c) ASPIDE.NET. All rights reserved.  
// Licensed under the GPLv3 License. See LICENSE file in the project root for full license information.  
//  
// SPDX-License-Identifier: GPL-3.0-or-later
//

namespace web3;

#
# ALL
#
$this->respond(array('GET', 'POST'), '*', function ($request, $response, $service, $app) {
    $session = getSession();
    $session->assertLogin();
    $operatore = $session->user();
    
    if ($operatore->has("ATENEO") || $operatore->has("DIPARTIMENTO") || $operatore->has("DOCENTE") || $operatore->has("MISSIONE3ADMIN")) 
        ;
    else {
        $session->smarty()->display("403.tpl");
        http_response_code(403);
        exit();
    }
    
    $MODULO_CODE = "M3ARCHIVIO";
    #$applicazione->setModulo($MODULO_CODE);
    $APP = [
        "title" => "M3ARCHIVIO",
        "url" => BASE_URL."/archivio",
        "code" => $MODULO_CODE
    ];
    
    $session->assertLogin($APP['url']);
    $session->smarty()->assign("APP", $APP);
    $session->save();
});

# LIST
$this->respond('GET', "/?[list]?", function ($request, $response, $service, $app) {
    $session = getSession();
    
    $session->smarty()->assign("anno", $request->anno);
    $session->smarty->display("archivio-container-list.tpl");
    exit();
});

# INNER LIST
$this->respond('GET', "/inner/list", function ($request, $response, $service, $app) {
    $session = getSession();
    $db = getDB();
    $operatore = $session->user();
    
	$filtri = $_SESSION["M3FILTER"];
	$filtro_anno = $filtri["anno"];
	$filtro_campo_azione = $filtri["campo_azione"];
    
    if ($operatore->has("ATENEO") || $operatore->has("DIPARTIMENTO")) 
        $filtro_matricola = $filtri["matricola"];        
    elseif ($operatore->has("DOCENTE"))
        $filtro_matricola = $operatore->get("matricola");
    else
        $filtro_matricola = "XXX"; # forzo che non si veda nulla
	if ($filtro_matricola == '000000')
		$filtro_matricola = "";
	$filtro_tipo_compilazione = $filtri["tipo_compilazione"];
	$filtro_stato = $filtri["stato"];
    
    $stringa_risposte = implode($filtri["agenda2030"], ",");
    $stringa_strutture = implode($filtri["struttura"], ",");
    
    if ($operatore->has("ATENEO")) {
        ; # non si fa niente. Il check ad ATENEO va messo perché chi ha il flag ATENEO ha anche il flag DIPARTIMENTO 
    }
    elseif (count($filtri["struttura"])==0 && $operatore->has("DIPARTIMENTO")) {       
        $sql = "SELECT v.visibility_code as codice, v.label_code as etichetta
                FROM WEB3_Visibilities v
                JOIN WEB3_RelUserProfileVisibility pv ON pv.visibility_code=v.visibility_code
                JOIN WEB3_RelUserProfile up ON up.code=pv.userprofile_code
                WHERE up.user_code=?
                ORDER BY v.visibility_code";
        $rs = $db->Execute($sql, array($operatore->code()));
        $s = array();
        while(!$rs->EOF) {
            $s[] = $rs->Fields("codice");
            $rs->MoveNext();
        }
        $stringa_strutture = implode($s, ",");
    }
    
    
    $stringa_ssd = implode($filtri["ssd"], ",");
    
    # filtri speciali
    $filtro_speciale = implode($filtri["filtro_speciale_".strtolower($filtro_campo_azione)], ",");
    
    $current_page = $request->param("page", 1);
    
    $row_per_page = 30;
    $offset = ($current_page - 1) * $row_per_page;
    if ($offset < 0) {
        $offset = 0;
        $current_page = 1;
    }
    
    $sql = "SELECT ident, code, campo_azione, anno, tipo_compilazione, titolo, stato, ui, ti, uu, tu, COUNT(*) OVER () as numero_records 
            FROM MISSIONE3_Attivita 
			WHERE 1=1
            AND (
                code IN (
                    SELECT code_attivita
                    FROM MISSIONE3_Risposte 
                    where codice_risposta in (
                        select string from dbo.fn_CSVToTable('{$stringa_risposte}', ',')
                    )
                ) OR LEN('{$stringa_risposte}')=0 
            )
            AND (
                code IN (
                    SELECT code_attivita
                    FROM MISSIONE3_Risposte 
                    where codice_risposta in (
                        select string from dbo.fn_CSVToTable('{$filtro_speciale}', ',')
                    )
                ) OR LEN('{$filtro_speciale}')=0 
            )
			AND (
				code IN (
					SELECT code_attivita
					FROM MISSIONE3_PersonaleCoinvolto
					WHERE matricola='{$filtro_matricola}'
				)  OR LEN('{$filtro_matricola}')=0 
                OR ui='{$operatore->username()}'
			)
			AND (
				code IN (
					SELECT code_attivita
					FROM MISSIONE3_PersonaleCoinvolto
					WHERE codice_ssd IN (
                        select string from dbo.fn_CSVToTable('{$stringa_ssd}', ',')
                    )
				)  OR LEN('{$stringa_ssd}')=0 
			)
			AND (
				code IN (
					SELECT code_attivita
					FROM MISSIONE3_PersonaleCoinvolto
					WHERE codice_struttura IN (
                        select string from dbo.fn_CSVToTable('{$stringa_strutture}', ',')
                    )
				)  OR LEN('{$stringa_strutture}')=0 
                OR ui='{$operatore->username()}'
			)
			AND (
                code IN (
                    SELECT code_attivita
                    FROM MISSIONE3_Risposte 
                    where 1=1
                    OR (LEN('{$filtri["parola1"]}')>0 AND (
                        supplement1 like '%{$filtri["parola1"]}%' OR 
                        supplement2 like '%{$filtri["parola1"]}%' OR 
                        supplement3 like '%{$filtri["parola1"]}%' OR 
                        supplement4 like '%{$filtri["parola1"]}%' OR 
                        supplement5 like '%{$filtri["parola1"]}%'
                    ))
                    OR (LEN('{$filtri["parola2"]}')>0 AND (
                        supplement1 like '%{$filtri["parola2"]}%' OR 
                        supplement2 like '%{$filtri["parola2"]}%' OR 
                        supplement3 like '%{$filtri["parola2"]}%' OR 
                        supplement4 like '%{$filtri["parola2"]}%' OR 
                        supplement5 like '%{$filtri["parola2"]}%'
                    ))
                    OR (LEN('{$filtri["parola3"]}')>0 AND (
                        supplement1 like '%{$filtri["parola3"]}%' OR 
                        supplement2 like '%{$filtri["parola3"]}%' OR 
                        supplement3 like '%{$filtri["parola3"]}%' OR 
                        supplement4 like '%{$filtri["parola3"]}%' OR 
                        supplement5 like '%{$filtri["parola3"]}%'
                    ))
                    OR (LEN('{$filtri["parola4"]}')>0 AND (
                        supplement1 like '%{$filtri["parola4"]}%' OR 
                        supplement2 like '%{$filtri["parola4"]}%' OR 
                        supplement3 like '%{$filtri["parola4"]}%' OR 
                        supplement4 like '%{$filtri["parola4"]}%' OR 
                        supplement5 like '%{$filtri["parola4"]}%'
                    ))
                    OR (LEN('{$filtri["parola5"]}')>0 AND (
                        supplement1 like '%{$filtri["parola5"]}%' OR 
                        supplement2 like '%{$filtri["parola5"]}%' OR 
                        supplement3 like '%{$filtri["parola5"]}%' OR 
                        supplement4 like '%{$filtri["parola5"]}%' OR 
                        supplement5 like '%{$filtri["parola5"]}%'
                    ))
                ) OR LEN('{$filtri["parola1"]}{$filtri["parola2"]}{$filtri["parola3"]}{$filtri["parola4"]}{$filtri["parola5"]}')=0
            )
			AND (tipo_compilazione='{$filtro_tipo_compilazione}' OR LEN('{$filtro_tipo_compilazione}')=0)
			AND (stato='{$filtro_stato}' OR LEN('{$filtro_stato}')=0)
			AND (anno='{$filtro_anno}' OR LEN('{$filtro_anno}')=0)
			AND (campo_azione='{$filtro_campo_azione}' OR LEN('{$filtro_campo_azione}')=0)
            ORDER BY anno desc, campo_azione
            OFFSET {$offset} ROWS
            FETCH NEXT {$row_per_page} ROWS ONLY;";   
    $anno = $request->param("anno", date("Y"));
    $rs = $db->Execute($sql, array()); #echo $sql;
    $attivita = $rs->GetArray();
    $numero_records = $attivita[0]["numero_records"];
    
    
    $personale_coinvolto = array();
    $strutture_coinvolte = array();
    $sql = "SELECT pc.*, p.Nome, p.Cognome, COALESCE(s.descrizione, s2.descrizione, p.NomeStruttura) as strutt, pc.codice_struttura, p.CodeStruttura, ru.label as coinvolgimento
            FROM MISSIONE3_PersonaleCoinvolto pc
            LEFT JOIN CSA_STORICO_PERSONALEINSERVIZIO p ON p.MatricolaCsa=pc.matricola
            LEFT JOIN CSA_STORICO_REAL_STRUTTURE s ON s.[codice UO]=pc.codice_struttura
            left join STRUTTUREATTIVE s2 on s2.[CODICE UO]=pc.codice_struttura
            LEFT join MISSIONE3_CONF_TipoCoinvolgimento ru ON ru.code=pc.codice_coinvolgimento
            ORDER BY pc.codice_coinvolgimento";
    $rs = $db->Execute($sql, array()); #echo $sql;
    while(!$rs->EOF) {
        $row = $rs->GetRow();
        $codice_attivita = $row["code_attivita"];
        $codice_struttura = $row["codice_struttura"];
        $personale_coinvolto[$codice_attivita][] = $row;
        $strutture_coinvolte[$codice_attivita][$codice_struttura] = $row;
    }
    
    
    $sezioni = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
    
    $session->smarty()->assign("anno", $anno);
    $session->smarty()->assign("attivita", $attivita);
    $session->smarty()->assign("count_attivita", $numero_records);
    $session->smarty()->assign("pages", ceil($numero_records / $row_per_page));
    $session->smarty()->assign("current_page", $current_page);
    $session->smarty()->assign("record_code_new", md5(microtime()));
    $session->smarty()->assign("sezioni", $sezioni);
    $session->smarty()->assign("anni", array(2023, 2022, 2021, 2020));
    $session->smarty()->assign("filtri", $filtri);
    $session->smarty()->assign("personale_coinvolto", $personale_coinvolto);
    $session->smarty()->assign("strutture_coinvolte", $strutture_coinvolte);
    $session->smarty->display("archivio-inner-list.tpl");
    // echo $sql."<hr>";
    // print_r($filtri);
    // echo "<hr>";
    // print_r($_SESSION['USER']);
    // echo "<hr>";
    // print_r($_SESSION['SSO']);
    exit();
});

# INNER LIST TABULATOR
$this->respond('GET', "/inner/list/tabulator", function ($request, $response, $service, $app) {
    $session = getSession();
    
    $tabulator = new UITabulator();
    $sql = "SELECT ident, code, campo_azione as [campo azione], anno, tipo_compilazione as compilazione, titolo, stato FROM MISSIONE3_Attivita";   
    $sqlparams = null;
    $tabulator->setSource($sql, $sqlparams, md5($sql), array("ident", "code"));
        
    $session->smarty()->assign("tabulator", $tabulator);
    $session->smarty()->assign("anno", $request->anno);
    $session->smarty->display("archivio-inner-list-tabulator.tpl");
    exit();
});

# FILTRI
$this->respond('GET', "/filter", function ($request, $response, $service, $app) {
    $session = getSession();
    $db = getDB();
    $operatore = $session->user();
    
    $filtri = $_SESSION["M3FILTER"];
    
    # anno
    $sql = "SELECT DISTINCT anno
			FROM MISSIONE3_Attivita
			ORDER BY anno desc";
    $rs = $db->Execute($sql, array("M3_CAMPI_AZIONE"));
    $anni = $rs->GetArray();
     
    # campo_azione
    $sql = "SELECT node_value as codice, node_value + ') ' + label_code as etichetta, sorting
			FROM WEB3_TreeNodes
			WHERE tree_code=?
			ORDER BY sorting";
    $rs = $db->Execute($sql, array("M3_CAMPI_AZIONE"));
    $campi_azione = $rs->GetArray();
     
    # Strutture coinvolte
    // $sql = "SELECT distinct [CODICE UO DI APPARTENENZA] as codice, DES_AFF_ORG as etichetta
			// FROM CSA_PERSONALE
			// where COD_SSD!='000000000000'";
    if ($operatore->has("ATENEO")) {       
        $sql = "SELECT visibility_code as codice, label_code as etichetta
                FROM WEB3_Visibilities
                ORDER BY visibility_code";
        $rs = $db->Execute($sql, array());
        $strutture = $rs->GetArray();
    }
    elseif ($operatore->has("DIPARTIMENTO")) {       
        $sql = "SELECT v.visibility_code as codice, v.label_code as etichetta
                FROM WEB3_Visibilities v
                JOIN WEB3_RelUserProfileVisibility pv ON pv.visibility_code=v.visibility_code
                JOIN WEB3_RelUserProfile up ON up.code=pv.userprofile_code
                WHERE up.user_code=?
                ORDER BY v.visibility_code";
        $rs = $db->Execute($sql, array($operatore->code()));
        $strutture = $rs->GetArray();
    }
    else
        $strutture = array();
    

    # AP_7: Agenda 2030
    $sql = "SELECT DISTINCT codice_risposta, testo_risposta, r.ordinamento,d.descrizione as testo_domanda
			FROM MISSIONE3_CONF_PossibiliRisposte r
			join MISSIONE3_CONF_Domande d on d.codice=r.codice_domanda AND r.anno=d.anno
			WHERE codice_domanda=?
			ORDER BY r.ordinamento";
    $rs = $db->Execute($sql, array("AP_7"));
    $AP_7 = $rs->GetArray();

    # SSD: 
    $sql = "SELECT distinct COD_SSD as codice, COD_SSD + ' - ' + DES_SSD as etichetta
			FROM CSA_PERSONALE
			WHERE COD_SSD!='000000000000'";
    $rs = $db->Execute($sql, array());
    $SSD = $rs->GetArray();
	

    # Specifiche del campo
    
    $sql = "SELECT * FROM MISSIONE3_CONF_FiltriSpecifici";
    $rs = $db->Execute($sql, array());
    $rows = $rs->GetArray();
    $filtri_specifici = array();
    foreach($rows as $item) {
        $codice_domanda = $item["codice_domanda"];
        
        $sql ="SELECT DISTINCT codice_risposta, testo_risposta, r.ordinamento,d.descrizione as testo_domanda, codice_domanda, sezione
               FROM MISSIONE3_CONF_PossibiliRisposte r
               join MISSIONE3_CONF_Domande d on d.codice=r.codice_domanda AND r.anno=d.anno
               WHERE codice_domanda=?
               ORDER BY r.ordinamento";
        $rs = $db->Execute($sql, array($codice_domanda));
        $filtri_specifici[$codice_domanda] = $rs->GetArray();
    }
    
    /*
    # G_6: Categoria di attività di Public Engagement
    $sql ="SELECT codice_risposta, testo_risposta, r.ordinamento,d.descrizione as testo_domanda
           FROM MISSIONE3_CONF_PossibiliRisposte r
           join MISSIONE3_CONF_Domande d on d.codice=r.codice_domanda
           WHERE codice_domanda=?
           ORDER BY r.ordinamento";
    $rs = $db->Execute($sql, array("G_6"));
    $G_6 = $rs->GetArray();
    
    # B_1: Tipologia di imprenditoria
    $sql ="SELECT codice_risposta, testo_risposta, r.ordinamento,d.descrizione as testo_domanda
           FROM MISSIONE3_CONF_PossibiliRisposte r
           join MISSIONE3_CONF_Domande d on d.codice=r.codice_domanda
           WHERE codice_domanda=?
           ORDER BY r.ordinamento";
    $rs = $db->Execute($sql, array("B_1"));
    $B_1 = $rs->GetArray();
    */
    
    $filtro_strutture = "'".implode($filtri["struttura"], "','")."'";
    $filtro_ssd = "'".implode($filtri["ssd"], "','")."'";
    $filtro_agenda2030 = "'".implode($filtri["agenda2030"], "','")."'";
    $filtro_categoria_pe = "'".implode($filtri["categoria_pe"], "','")."'";
     
    $session->smarty()->assign("modal_id", md5(microtime()));
    $session->smarty()->assign("anni", $anni);
    $session->smarty()->assign("strutture", $strutture);
    $session->smarty()->assign("AP_7", $AP_7);
    $session->smarty()->assign("campi_azione", $campi_azione);
    // $session->smarty()->assign("G_6", $G_6);
    // $session->smarty()->assign("B_1", $B_1);
    $session->smarty()->assign("SSD", $SSD);
    $session->smarty()->assign("filtri", $filtri);
    $session->smarty()->assign("filtro_strutture", $filtro_strutture);
    $session->smarty()->assign("filtro_ssd", $filtro_ssd);
    $session->smarty()->assign("filtro_agenda2030", $filtro_agenda2030);
    $session->smarty()->assign("filtro_categoria_pe", $filtro_categoria_pe);
    $session->smarty()->assign("filtri_specifici", $filtri_specifici);
    $session->smarty->display("archivio-modal-filtri-container.tpl");
    #print_r($filtri);
    exit();
});





# DELETE
$this->respond('POST', "/delete/[:record_code]", function ($request, $response, $service, $app) {
    $session = getSession();
    $db = getDB();
    $record_code = $request->record_code;
    
    # CHECK BOZZA
    $sql = "SELECT * FROM MISSIONE3_Attivita WHERE code=?";   
    $rs = $db->Execute($sql, array($record_code));
    if (strtoupper($rs->Fields("stato")) != 'B') {
        $result->setResult(false);
        $result->setCode("KO");
        $result->setDescription("L'attività non è in BOZZA per cui non è possibile cancellarla.");
        $result->setLevel(Result::ERROR);
        $result->toJson();
        exit();
    }
    
    
    $sql = "DELETE FROM MISSIONE3_Allegati WHERE code_attivita=?";   
    $rs = $db->Execute($sql, array($record_code));
    
    $sql = "DELETE FROM MISSIONE3_TitolariDirittoPatrimoniale WHERE code_attivita=?";   
    $rs = $db->Execute($sql, array($record_code));
    
    $sql = "DELETE FROM MISSIONE3_Risposte WHERE code_attivita=?";   
    $rs = $db->Execute($sql, array($record_code));
    
    $sql = "DELETE FROM MISSIONE3_Attivita WHERE code=?";   
    $rs = $db->Execute($sql, array($record_code));
    

    exit('OK');
});


# REPORT
$this->respond('GET', "/report/[pdf|html:format]/[:record_code]", function ($request, $response, $service, $app) {
    $session = getSession();
    $db = getDB();
    $record_code = $request->record_code;
    $format = $request->format;
    $html = "";
    $action_name = "print"; #$request->param("action_name");
    
    $image_path = STATIC_URL;
    if ($format == 'pdf') {
        $image_path = STATIC_DIR;
    }
    
    $sql = "SELECT * FROM MISSIONE3_Attivita WHERE code=?";
    $rs = $db->Execute($sql, array($record_code));
    $attivita = $rs->GetRow();        
    
    $tipo_compilazione = "BASE";
    if ($attivita["tipo_compilazione"] == 'V')
        $tipo_compilazione = "VALUTAZIONE";

    $html .= <<<EOT
<center>
<div style='text-align:center; font-size:3em; font-weight:bold;'>Terza missione</div>
<br>
<table>
<tr>
    <th>ANNO</th>
    <th>CAMPO D'AZIONE</th>
    <th>MODALIT&Agrave; DI COMPILAZIONE</th>
</tr>
<tr>
    <td>{$attivita["anno"]}</td>
    <td>{$attivita["campo_azione"]}</td>
    <td>{$tipo_compilazione}</td>
</tr>
</table>
</br>
<div style='font-size:1.5em; margin-top:1em; margin-bottom:1em;'>Titolo attività: <em>{$attivita["titolo"]}</em></div>

</center>    

EOT;    
    # STEP 1: Dati basilari attività
    
    # STEP 2: campi di apertura
    
    # campi di apertura
    #elseif (in_array($step, array('3', '4', '5'))) {
    $steps = array('3', '4', '5'); #array();
    foreach($steps as $step) {
        $object_name = "M3_DOMANDE";
        #$action_name = $request->param("action_name");
        $record_code = $request->param("record_code", false);
        $style = $request->param("style", "");
        
        if ($step == '3') {
            $context_name = "AP";
            $titolo_sezione = "Domande di apertura";
        }
        if ($step == '4') {
            $context_name = strtoupper($attivita["campo_azione"]);
            $titolo_sezione = "Domande caratteristiche";
        }
        if ($step == '5') {
            $context_name = "CH";
            $titolo_sezione = "Domande di chiusura";
        }
        $options = array(
            "context_name" => $context_name,
            "object_name" => $object_name,
            "action_name" => $action_name,
            "record_code" => $record_code,
            "style" => $style
        );

        $uiobj = new UIObject();
        $uiobj->init($context_name, $object_name, $action_name, $record_code);
        $class = $uiobj->getClass();
        if (strlen($class) > 0) 
            if (class_exists($class)) {
                $uiobj = new $class();
                $uiobj->init($context_name, $object_name, $action_name, $record_code);
            }
        
        $uiobj->set(array("style" => $style));
        $html .= "<h1>{$titolo_sezione}</h1>";
        $html .= $uiobj->display($options);
    }    
    
    $smarty = $session->smarty();
    
    $data = $smarty->createData();
    $data->assign("html_content", $html);
    $data->assign("STATIC_URL", $image_path);
    $tpl = $smarty->createTemplate("report-attivita.tpl", $data);
    $html2 = $smarty->fetch($tpl);
        
    #--------------------------------------------------------------------------------------------
    if ($format == 'html') {
        echo $html2;
        exit();
    }
    #--------------------------------------------------------------------------------------------
    #$mpdf = new \mPDF();#$mpdf->showImageErrors = true;
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->PDFA = true;
    $mpdf->PDFAauto = true;
    #$mpdf->SetProtection(array('print'));
    $mpdf->SetTitle("");
    $mpdf->SetAuthor("");
    // $mpdf->SetWatermarkText("Paid");
    // $mpdf->showWatermarkText = true;
    // $mpdf->watermark_font = 'DejaVuSansCondensed';
    // $mpdf->watermarkTextAlpha = 0.1;
    // $mpdf->SetDisplayMode('fullpage');
    $mpdf->WriteHTML($html2);
    $mpdf->Output("terza-missione-attivita-{$record_code}.pdf", 'D');    

});

$this->respond('GET', "/report/[xlsx:format]", function ($request, $response, $service, $app) {
    $session = getSession();
    $db = getDB();
    $operatore = $session->user();
    $format = $request->format;
    
	$filtri = $_SESSION["M3FILTER"];
	$filtro_anno = ($filtri["anno"] > 0) ? $filtri["anno"] : date("Y")-1;
	$filtro_campo_azione = (strlen($filtri["campo_azione"])) ? $filtri["campo_azione"] : 'g';
    
    if ($operatore->has("ATENEO") || $operatore->has("DIPARTIMENTO")) 
        $filtro_matricola = $filtri["matricola"];        
    elseif ($operatore->has("DOCENTE"))
        $filtro_matricola = $operatore->get("matricola");
    else
        $filtro_matricola = "XXX"; # forzo che non si veda nulla
	if ($filtro_matricola == '000000')
		$filtro_matricola = "";
	$filtro_tipo_compilazione = $filtri["tipo_compilazione"];
	$filtro_stato = $filtri["stato"];
    
    $stringa_risposte = implode($filtri["agenda2030"], ",");
    $stringa_strutture = implode($filtri["struttura"], ",");
    
    if ($operatore->has("ATENEO")) {
        ; # non si fa niente. Il check ad ATENEO va messo perché chi ha il flag ATENEO ha anche il flag DIPARTIMENTO 
    }
    elseif (count($filtri["struttura"])==0 && $operatore->has("DIPARTIMENTO")) {       
        $sql = "SELECT v.visibility_code as codice, v.label_code as etichetta
                FROM WEB3_Visibilities v
                JOIN WEB3_RelUserProfileVisibility pv ON pv.visibility_code=v.visibility_code
                JOIN WEB3_RelUserProfile up ON up.code=pv.userprofile_code
                WHERE up.user_code=?
                ORDER BY v.visibility_code";
        $rs = $db->Execute($sql, array($operatore->code()));
        $s = array();
        while(!$rs->EOF) {
            $s[] = $rs->Fields("codice");
            $rs->MoveNext();
        }
        $stringa_strutture = implode($s, ",");
    }
    
    # ATTIVITA'
    $sql = "SELECT ident, code, campo_azione, anno, tipo_compilazione, titolo, stato, ui, ti, uu, tu
            FROM MISSIONE3_Attivita 
			WHERE 1=1
            AND (
                code IN (
                    SELECT code_attivita
                    FROM MISSIONE3_Risposte 
                    where codice_risposta in (
                        select string from dbo.fn_CSVToTable('{$stringa_risposte}', ',')
                    )
                ) OR LEN('{$stringa_risposte}')=0 
            )
            AND (
                code IN (
                    SELECT code_attivita
                    FROM MISSIONE3_Risposte 
                    where codice_risposta in (
                        select string from dbo.fn_CSVToTable('{$filtro_speciale}', ',')
                    )
                ) OR LEN('{$filtro_speciale}')=0 
            )
			AND (
				code IN (
					SELECT code_attivita
					FROM MISSIONE3_PersonaleCoinvolto
					WHERE matricola='{$filtro_matricola}'
				)  OR LEN('{$filtro_matricola}')=0 
                OR ui='{$operatore->username()}'
			)
			AND (
				code IN (
					SELECT code_attivita
					FROM MISSIONE3_PersonaleCoinvolto
					WHERE codice_ssd IN (
                        select string from dbo.fn_CSVToTable('{$stringa_ssd}', ',')
                    )
				)  OR LEN('{$stringa_ssd}')=0 
			)
			AND (
				code IN (
					SELECT code_attivita
					FROM MISSIONE3_PersonaleCoinvolto
					WHERE codice_struttura IN (
                        select string from dbo.fn_CSVToTable('{$stringa_strutture}', ',')
                    )
				)  OR LEN('{$stringa_strutture}')=0 
                OR ui='{$operatore->username()}'
			)
			AND (
                code IN (
                    SELECT code_attivita
                    FROM MISSIONE3_Risposte 
                    where 1=1
                    OR (LEN('{$filtri["parola1"]}')>0 AND (
                        supplement1 like '%{$filtri["parola1"]}%' OR 
                        supplement2 like '%{$filtri["parola1"]}%' OR 
                        supplement3 like '%{$filtri["parola1"]}%' OR 
                        supplement4 like '%{$filtri["parola1"]}%' OR 
                        supplement5 like '%{$filtri["parola1"]}%'
                    ))
                    OR (LEN('{$filtri["parola2"]}')>0 AND (
                        supplement1 like '%{$filtri["parola2"]}%' OR 
                        supplement2 like '%{$filtri["parola2"]}%' OR 
                        supplement3 like '%{$filtri["parola2"]}%' OR 
                        supplement4 like '%{$filtri["parola2"]}%' OR 
                        supplement5 like '%{$filtri["parola2"]}%'
                    ))
                    OR (LEN('{$filtri["parola3"]}')>0 AND (
                        supplement1 like '%{$filtri["parola3"]}%' OR 
                        supplement2 like '%{$filtri["parola3"]}%' OR 
                        supplement3 like '%{$filtri["parola3"]}%' OR 
                        supplement4 like '%{$filtri["parola3"]}%' OR 
                        supplement5 like '%{$filtri["parola3"]}%'
                    ))
                    OR (LEN('{$filtri["parola4"]}')>0 AND (
                        supplement1 like '%{$filtri["parola4"]}%' OR 
                        supplement2 like '%{$filtri["parola4"]}%' OR 
                        supplement3 like '%{$filtri["parola4"]}%' OR 
                        supplement4 like '%{$filtri["parola4"]}%' OR 
                        supplement5 like '%{$filtri["parola4"]}%'
                    ))
                    OR (LEN('{$filtri["parola5"]}')>0 AND (
                        supplement1 like '%{$filtri["parola5"]}%' OR 
                        supplement2 like '%{$filtri["parola5"]}%' OR 
                        supplement3 like '%{$filtri["parola5"]}%' OR 
                        supplement4 like '%{$filtri["parola5"]}%' OR 
                        supplement5 like '%{$filtri["parola5"]}%'
                    ))
                ) OR LEN('{$filtri["parola1"]}{$filtri["parola2"]}{$filtri["parola3"]}{$filtri["parola4"]}{$filtri["parola5"]}')=0
            )
			AND (tipo_compilazione='{$filtro_tipo_compilazione}' OR LEN('{$filtro_tipo_compilazione}')=0)
			AND (stato='{$filtro_stato}' OR LEN('{$filtro_stato}')=0)
			AND (anno='{$filtro_anno}' OR LEN('{$filtro_anno}')=0)
			AND (campo_azione='{$filtro_campo_azione}' OR LEN('{$filtro_campo_azione}')=0)
            ORDER BY anno desc, campo_azione";   
    $rs = $db->Execute($sql, array()); #echo $sql;
    while(!$rs->EOF) {
        $row = $rs->GetRow();
        $code_attivita = $row["code"];
        $attivita[$code_attivita]["data"] = $row;
        $attivita[$code_attivita]["risposte"] = array();
    }
    
    # PERSONALE COINVOLTO
    $personale_coinvolto = array();
    $strutture_coinvolte = array();
    $sql = "SELECT pc.*, p.Nome, p.Cognome, COALESCE(s.descrizione, s2.descrizione, p.NomeStruttura) as strutt, pc.codice_struttura, p.CodeStruttura, ru.label as coinvolgimento
            FROM MISSIONE3_PersonaleCoinvolto pc
            LEFT JOIN CSA_STORICO_PERSONALEINSERVIZIO p ON p.MatricolaCsa=pc.matricola
            LEFT JOIN CSA_STORICO_REAL_STRUTTURE s ON s.[codice UO]=pc.codice_struttura
            left join STRUTTUREATTIVE s2 on s2.[CODICE UO]=pc.codice_struttura
            LEFT join MISSIONE3_CONF_TipoCoinvolgimento ru ON ru.code=pc.codice_coinvolgimento
            ORDER BY pc.codice_coinvolgimento";
    $rs = $db->Execute($sql, array()); #echo $sql;
    while(!$rs->EOF) {
        $row = $rs->GetRow();
        $codice_attivita = $row["code_attivita"];
        if (!isset($attivita[$codice_attivita]))
            continue;
        
        $codice_struttura = $row["codice_struttura"];
        if (!isset($personale_coinvolto[$codice_attivita]))
            $personale_coinvolto[$codice_attivita] = '';
        //$session->log($codice_attivita." {$row['cognome']} {$row['nome']} {$row['coinvolgimento']}; ");
        $personale_coinvolto[$codice_attivita] .= "{$row['cognome']} {$row['nome']} {$row['coinvolgimento']}; ";
        $strutture_coinvolte[$codice_attivita][$codice_struttura] = $row;
    }
    
    # DOMANDE
    $domande = array();
    $sql = "SELECT anno, sezione, codice, descrizione, lista
            FROM MISSIONE3_CONF_Domande
            WHERE anno=? AND sezione in ('AP', ?, 'CH')
            ORDER BY sorting";
    $rs = $db->Execute($sql, array($filtro_anno, $filtro_campo_azione)); #echo $sql;
    while(!$rs->EOF) {
        $row = $rs->GetRow();
        $codice = $row["codice"];
        $domande[$codice] = $row;
    }
    
    # RISPOSTE
    $risposte = array();
    $sql = "SELECT d.anno, sezione, r.testo_risposta, risp.*
            FROM MISSIONE3_CONF_Domande d
			JOIN MISSIONE3_CONF_PossibiliRisposte r ON r.codice_domanda=d.codice AND r.anno=d.anno
			JOIN MISSIONE3_Risposte risp ON risp.codice_domanda=d.codice and risp.codice_risposta=r.codice_risposta
            WHERE anno=? AND sezione in ('AP', ?, 'CH')
            ORDER BY sorting";
    $rs = $db->Execute($sql, array($filtro_anno)); #echo $sql;
    while(!$rs->EOF) {
        $row = $rs->GetRow();
        $codice = $row["codice"];
        $risposte[$codice] = $row;
    }
    
    
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    // Set properties
    $spreadsheet->getProperties()
        ->setCreator("WebTree")
        ->setLastModifiedBy("WebTree")
        ->setTitle("")
        ->setSubject("")
        ->setDescription("")
        ->setKeywords("")
        ->setCategory("");
        
    $sheet = $spreadsheet->getActiveSheet();
    
    //----------------------------------------------------------------------------
    // Record:
    // Per ogni riga:
    $row_index = 1;
    $first = true;
    $numfields = 0;
    foreach($attivita as $codice_attivita => $row) {
        $code_attivita = $row["code"];
        $f = 1;
        if ($first) {
            //----------------------------------------------------------------------------
            
            $sheet->setCellValueByColumnAndRow($f++, 1, "Anno");
            $sheet->setCellValueByColumnAndRow($f++, 1, "Campo d'azione");
            $sheet->setCellValueByColumnAndRow($f++, 1, "Modalità di compilazione");
            $sheet->setCellValueByColumnAndRow($f++, 1, "Stato attività");
            $sheet->setCellValueByColumnAndRow($f++, 1, "Titolo dell'attività");
            $sheet->setCellValueByColumnAndRow($f++, 1, "Personale coinvolto");
            $sheet->setCellValueByColumnAndRow($f++, 1, "Strutture collegate");
            
            foreach($domande as $codice_domanda => $domanda) {
                $sheet->setCellValueByColumnAndRow($f++, 1, $domanda["descrizione"]);
            }
            $first = false;
            $row_index++;
            $f = 1;
        }
        
        $data = $row["data"];
        
        $sheet->setCellValueByColumnAndRow($f++, $row_index, $data["anno"]);
        $sheet->setCellValueByColumnAndRow($f++, $row_index, $data["campo_azione"]);
        $sheet->setCellValueByColumnAndRow($f++, $row_index, $data["tipo_compilazione"]);
        $sheet->setCellValueByColumnAndRow($f++, $row_index, $data["stato"]);
        $sheet->setCellValueByColumnAndRow($f++, $row_index, $data["titolo"]);
        $session->log($codice_attivita."  ".$personale_coinvolto[$codice_attivita]);
        $sheet->setCellValueByColumnAndRow($f++, $row_index, $personale_coinvolto[$codice_attivita]);
        $sheet->setCellValueByColumnAndRow($f++, $row_index, $data["strutture_coinvolte"]);
        
        foreach($domande as $codice_domanda => $domanda) {
            //$sheet->setCellValueByColumnAndRow($f++, $row_index, $domanda["descrizione"]);
            //$sheet->setCellValueByColumnAndRow($f++, 1, $risposte[$code_attivita]["descrizione"]);
        }
        
        // for ($i=0; $i < $numfields; $i++) {
            // $valore = @trim($row[$nomi_colonne[$i]]);
            // $sheet->setCellValueByColumnAndRow($i+1, $row_index, $valore);
        // }
        $row_index++;
    }
    
    $spreadsheet->setActiveSheetIndex(0);
    
    ob_clean();
    
    // Redirect output to a client’s web browser (Xlsx)
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment;filename=\"terza-missione-{$filtro_campo_azione}-{$filtro_anno}.xlsx\"");
    header("Content-Transfer-Encoding: binary");
    
     // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Pragma: public'); // HTTP/1.0
    
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save("php://output");
    
});
    