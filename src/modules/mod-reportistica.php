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

    $MODULO_CODE = "M3ARCHIVIO";
    #$applicazione->setModulo($MODULO_CODE);
    $APP = [
        "title" => "M3ARCHIVIO",
        "url" => BASE_URL."/reportistica",
        "code" => $MODULO_CODE
    ];
    
    $session->assertLogin($APP['url']);
    $session->smarty()->assign("APP", $APP);
    $session->save();
});

# CONTAINER
$this->respond('GET', "/?", function ($request, $response, $service, $app) {
    $session = getSession();
    $db = getDB();
    $operatore = $session->user();
    
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
    
    $sezioni = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
    
    $session->smarty()->assign("anno", $request->param("anno", date('Y')));
    $session->smarty()->assign("anni", array(2023, 2022, 2021, 2020));
    $session->smarty()->assign("sezioni", $sezioni);
    $session->smarty()->assign("strutture", $strutture);
    $session->smarty->display("reportistica-container.tpl");
    exit();
});



# 
$this->respond('GET', "/ws/domande/[1|2:versione]/[:sezione]", function ($request, $response, $service, $app) {
    $session = getSession();
    $db = getDB();
    $operatore = $session->user();
    
    if ($request->versione == '1')
        $sql = "SELECT distinct len(sezione) as l, sezione
                ,codice as value
                ,descrizione as name
                ,d.ordinamento
                FROM MISSIONE3_CONF_Domande d
                JOIN MISSIONE3_CONF_PossibiliRisposte r ON d.codice=r.codice_domanda AND d.anno=r.anno
                WHERE (sezione In ('AP', ?, 'CH') OR ?='*')
                order by len(sezione) desc, sezione, d.ordinamento";
    else
        $sql = "SELECT distinct len(sezione) as l, sezione
                ,codice as value
                ,descrizione as name
                ,d.ordinamento
                FROM MISSIONE3_CONF_Domande d
                JOIN MISSIONE3_CONF_PossibiliRisposte r ON d.codice=r.codice_domanda AND d.anno=r.anno
                WHERE campo_aggiuntivo_tipo IN ('INT', 'DEC', 'NM')
                AND (sezione In ('AP', ?, 'CH') OR ?='*')
                order by len(sezione) desc, sezione, d.ordinamento";
        
    $rs = $db->Execute($sql, array($request->sezione, $request->sezione)); #echo $sql;
    $domande = $rs->GetArray();

    $result = array(
        "success" => true,
        "results" => $domande
    );
    echo_json($result);
    exit();
});





# INNER
$this->respond('POST', "/[1|2:versione]/inner", function ($request, $response, $service, $app) {
    $session = getSession();
    $db = getDB();
    $operatore = $session->user();
    $filtro_versione2 = "";
    
    $filtri = array(
        "campo_azione" => $request->param("campo_azione", ''),
        "anno" => $request->param("anno", 0),
        "stato" => $request->param("stato", '%'),
        "tipo_compilazione" => $request->param("tipo_compilazione", ''),
        "struttura" => $request->param("struttura", '')
    );
    
    # INNER QUALITATIVA
    if ($request->versione == '1')
        ;
    
    # INNER QUANTITATIVA
    if ($request->versione == '2')
        $filtro_versione2 = " AND campo_aggiuntivo_tipo IN ('INT', 'DEC', 'NM') ";
    
    $filtro_campo_azione = " AND (sezione IN ('AP', '{$filtri["campo_azione"]}', 'CH') OR '{$filtri["campo_azione"]}'='') ";
    $filtro_anno = " AND (a.anno=0{$filtri["anno"]} OR 0{$filtri["anno"]}=0) ";
    $filtro_stato = " AND a.stato LIKE '{$filtri["stato"]}%' ";
    $filtro_tipo_compilazione = " AND a.tipo_compilazione LIKE '{$filtri["tipo_compilazione"]}%' ";
    $filtro_struttura = " AND (ISNULL(p.codice_struttura, '')='{$filtri["struttura"]}' OR '{$filtri["struttura"]}'='') ";
     
    $sql = "SELECT distinct len(sezione) as l, sezione
                ,codice
                ,descrizione
                ,d.ordinamento
                FROM MISSIONE3_CONF_Domande d
                JOIN MISSIONE3_CONF_PossibiliRisposte r ON d.codice=r.codice_domanda AND d.anno=r.anno
				JOIN MISSIONE3_Risposte risp ON risp.codice_domanda=r.codice_domanda 
				JOIN MISSIONE3_Attivita a ON risp.code_attivita=a.code AND a.anno=r.anno
                LEFT JOIN MISSIONE3_PersonaleCoinvolto p ON p.code_attivita=a.code
                WHERE 1=1
                {$filtro_campo_azione}
                {$filtro_anno}
                {$filtro_stato}
                {$filtro_tipo_compilazione}
                {$filtro_struttura}
                {$filtro_versione2}
                order by len(sezione) desc, sezione, d.ordinamento"; #echo $sql;
    $rs = $db->Execute($sql, array()); 
    $domande = $rs->GetArray();

    
    $session->smarty()->assign("domande", $domande);
    $session->smarty()->assign("filtri", $filtri);
    $session->smarty()->assign("versione", $request->versione);
    $session->smarty->display("reportistica-inner.tpl");
});

# FILTRI
$this->respond('GET', "/filter", function ($request, $response, $service, $app) {
    $session = getSession();
    $db = getDB();
    
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
    $sql = "SELECT distinct [CODICE UO DI APPARTENENZA] as codice, DES_AFF_ORG as etichetta
			FROM CSA_PERSONALE
			where COD_SSD!='000000000000'";
    $rs = $db->Execute($sql, array());
    $strutture = $rs->GetArray();

    # AP_7: Agenda 2030
    $sql = "SELECT codice_risposta, testo_risposta, r.ordinamento,d.descrizione as testo_domanda
			FROM MISSIONE3_CONF_PossibiliRisposte r
			join MISSIONE3_CONF_Domande d on d.codice=r.codice_domanda AND d.anno=r.anno
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
        
        $sql ="SELECT codice_risposta, testo_risposta, r.ordinamento,d.descrizione as testo_domanda, codice_domanda, sezione
               FROM MISSIONE3_CONF_PossibiliRisposte r
               join MISSIONE3_CONF_Domande d on d.codice=r.codice_domanda
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





#
# JSON COLUMN
#
$this->respond('POST', '/json/column/[i:quadrante]/[:parent1]/[:parent2]?', function ($request, $response, $service, $app) { 
    $session = getSession();
    $db = getDB();
    $quadrante = $request->quadrante;
    $parent1 = $request->parent1;
    $parent2 = $request->parent2;
    
    $MODULO_CODE = $session->get("MODULO_CODE");
    $PARAMETRI_SP = "";
    $result = array();
    
    # Edifici selezionati
    $filtro_edifici_selezionati = '';
    if (isset($_SESSION[$MODULO_CODE]["FILTRO_EDIFICI"])) {
        $edifici_selezionati = $_SESSION[$MODULO_CODE]["FILTRO_EDIFICI"];
        if (count($edifici_selezionati))
            $filtro_edifici_selezionati = implode(",", $edifici_selezionati);
        $session->log("Edifici selezionati: ");
        $session->log($edifici_selezionati);
    }
    
    # CDC selezionati
    $filtro_cdc_selezionati = '';
    if (isset($_SESSION[$MODULO_CODE]["FILTRO_CDC"])) {
        $cdc_selezionati = $_SESSION[$MODULO_CODE]["FILTRO_CDC"];
        if (count($cdc_selezionati))
            $filtro_cdc_selezionati = implode(",", $cdc_selezionati);
        $session->log("CDC selezionati: ");
        $session->log($cdc_selezionati);
    }
    
    # DDU selezionate
    $filtro_ddu_selezionati = '';
    if (isset($_SESSION[$MODULO_CODE]["FILTRO_DDU"])) {
        $ddu_selezionati = $_SESSION[$MODULO_CODE]["FILTRO_DDU"];
        if (count($ddu_selezionati))
            $filtro_ddu_selezionati = implode(",", $ddu_selezionati);
        $session->log("DDU selezionati: ");
        $session->log($ddu_selezionati);
    }
      
        
    
    # QUADRANTE #1 - CDC
    if ($quadrante == 1) {
        $sql = "CONSOLIDAMENTO.dbo.DASHBOARD_CDC ?, ?, ?";
        $rs = $db->Execute($sql, array($filtro_cdc_selezionati, $filtro_edifici_selezionati, $filtro_ddu_selezionati));
    }
    # QUADRANTE #2 - Edifici
    elseif ($quadrante == 2) {
        $sql = "CONSOLIDAMENTO.dbo.DASHBOARD_Edifici ?, ?, ?";
        $rs = $db->Execute($sql, array($filtro_cdc_selezionati, $filtro_edifici_selezionati, $filtro_ddu_selezionati));
        $session->log($filtro_edifici_selezionati);
    }
    # QUADRANTE #3 - DDU di primo livello
    if ($quadrante == 3) {
        $sql = "CONSOLIDAMENTO.dbo.DASHBOARD_DDU ?, ?, ?";
        $rs = $db->Execute($sql, array($filtro_cdc_selezionati, $filtro_edifici_selezionati, $filtro_ddu_selezionati));
    }    
    # QUADRANTE #4 - Persone in Edificio
    elseif ($quadrante == 4) {
        $sql = "CONSOLIDAMENTO.dbo.DASHBOARD_Persone_Edifici ?, ?, ?";
        $rs = $db->Execute($sql, array($filtro_cdc_selezionati, $filtro_edifici_selezionati, $filtro_ddu_selezionati));
    }
    # QUADRANTE #1.1 - Edifici da CDC
    elseif ($quadrante == 11) {
        $sql = "CONSOLIDAMENTO.dbo.DASHBOARD_Edifici ?, ?, ?";
        $rs = $db->Execute($sql, array($parent1, $filtro_edifici_selezionati, $filtro_ddu_selezionati));
    }
    # QUADRANTE #111 - DDU in Edificio da CDC
    elseif ($quadrante == 111) {
        $sql = "CONSOLIDAMENTO.dbo.DASHBOARD_DDU ?, ?, ?";
        $rs = $db->Execute($sql, array($parent1, $parent2, $filtro_ddu_selezionati));
    }
    # QUADRANTE #2.1 - CDC in Edificio
    elseif ($quadrante == 21) {
        $sql = "CONSOLIDAMENTO.dbo.DASHBOARD_CDC ?, ?, ?";
        $rs = $db->Execute($sql, array($filtro_cdc_selezionati, $parent1, $filtro_ddu_selezionati));
    }
    # QUADRANTE #211 - DDU in CDC in Edificio
    elseif ($quadrante == 211) {
        $sql = "CONSOLIDAMENTO.dbo.DASHBOARD_DDU ?, ?, ?";
        $rs = $db->Execute($sql, array($parent2, $parent1, $filtro_ddu_selezionati));
    }
    
    ##############################################################################
    # QUADRANTE #211 - DDU in CDC in Edificio
    elseif ($quadrante == 1110001) {
        $sql = "CONSOLIDAMENTO.dbo.DASHBOARD_DDU ?, ?, ?";
        $rs = $db->Execute($sql, array($filtro_cdc_selezionati, $parent1, $filtro_ddu_selezionati));
    }
    ##############################################################################
    
    error_log($sql."-".$parent1."-".$parent2);
    $session->log(array($parent1, $parent2));
    
    if ($rs != FALSE) {
        $result['cols'] = array();
        $result['cols'][0] = array('label'=>'','pattern'=>'','type'=>'string','id'=>'string');
        $result['rows'] = array();
        $result['rows'][0]["c"] = array();
        $result['rows'][0]["c"][] = array("v"=>"","f"=>"");
            
        if ($rs->RecordCount() == 0) {
            $codice = "";
            $numero = 0;
            $etichetta = "Non definito";
            
            $result['cols'][] = array('id'=>$codice,'label'=>$etichetta,'pattern'=>'','type'=>'number');
            $result['rows'][0]["c"][] = array("v"=>$numero,"f"=>$codice);
        }
        else {
            while(!$rs->EOF) {
                $codice = $rs->Fields("codice");
                $numero = $rs->Fields("numero");
                $unita = $rs->Fields("unita");
                #$etichetta = $rs->Fields("etichetta")." ({$unita} ".number_format($numero, 2, ',', '.').")";
                $etichetta = $rs->Fields("etichetta")." ({$unita})";
                
                $result['cols'][] = array('id'=>$codice,'label'=>$etichetta,'pattern'=>'','type'=>'number');
                $result['rows'][0]["c"][] = array("v"=>$numero,"f"=>$codice);
                $rs->MoveNext();
            }
        }
    }
    echo_json($result);
    exit();
});



#
# JSON PIE QUALITATIVA
#
$this->respond(array('GET', 'POST'), '/json/pie[1]?', function ($request, $response, $service, $app) { 
    $session = getSession();
    $db = getDB();
    
    $anno = $request->anno;
    $campo_azione = $request->campo_azione;
    $tipo_compilazione = $request->tipo_compilazione;
    $stato = $request->stato;
    $codice_domanda = $request->codice_domanda;
    $struttura = $request->struttura;
    if (strlen($codice_domanda) == 0)
        exit();
    if (strlen($anno) == 0)
        $anno = 0;
    if (strlen($campo_azione) == 0)
        $campo_azione = '*';
    if (strlen($tipo_compilazione) == 0)
        $tipo_compilazione = '*';
    if (strlen($stato) == 0)
        $stato = '*';
    if (strlen($struttura) == 0)
        $struttura = '*';
    
    $_SESSION["FILTER-REPORT"] = array(
        "anno" => $anno,
        "stato" => $stato,
        "struttura" => $struttura,
        "campo_azione" => $campo_azione,
        "tipo_compilazione" => $tipo_compilazione,
        "codice_domanda" => $codice_domanda,
        "versione" => 1,
    );
    
    $result = array();
    $sql = "SELECT risp.codice_risposta as codice, testo_risposta as etichetta, count(DISTINCT risp.code) as numero, 'unità' as unita
            FROM MISSIONE3_Attivita a
            join MISSIONE3_Risposte risp ON a.code=risp.code_attivita
            join MISSIONE3_CONF_PossibiliRisposte r ON risp.codice_risposta=r.codice_risposta AND a.anno=r.anno
            join MISSIONE3_CONF_Domande d on d.codice=r.codice_domanda AND d.anno=r.anno
            LEFT JOIN MISSIONE3_PersonaleCoinvolto p ON p.code_attivita=a.code
            WHERE risp.codice_domanda=?
            AND (a.anno=? OR ?=0)
            AND (a.campo_azione=? OR ?='*')
            AND (a.tipo_compilazione=? OR ?='*')
            AND (a.stato=? OR ?='*')
            AND (p.codice_struttura=? OR ?='*')
            GROUP BY risp.codice_risposta, testo_risposta, r.ordinamento
            ORDER BY r.ordinamento";
    $rs = $db->Execute($sql, array(
        $codice_domanda,
        $anno, $anno,
        $campo_azione, $campo_azione,
        $tipo_compilazione, $tipo_compilazione,
        $stato, $stato,
        $struttura, $struttura
    ));
    
    
    $_SESSION["FILTER-REPORT"]["datatable"] = $rs->GetArray();
    
    
    
    if ($rs != FALSE) {
        $result['cols'] = array(
          array('label'=>'Valore selezionato nelle risposte','pattern'=>'','type'=>'string'),
          array('label'=>'Numero di attività','pattern'=>'','type'=>'number')
        );
        if ($rs->RecordCount() == 0) {
            $codice = "";
            $numero = 100;
            $etichetta = "Non definito";
            
            $result['rows'][] = array(
                "c"=>array(
                    array("v"=>$codice,"f"=>$etichetta),
                    array("v"=>intval($numero),"f"=>null)
                )
            );
        }
        else {
            while(!$rs->EOF) {
                $codice = $rs->Fields("codice");
                $numero = $rs->Fields("numero");
                $unita = $rs->Fields("unita");
                #$etichetta = $rs->Fields("etichetta")." ({$unita} ".number_format($numero, 2, ',', '.').")";
                $etichetta = $rs->Fields("etichetta")." ({$numero} {$unita})";
                
                $result['rows'][] = array(
                    "c"=>array(
                        array("v"=>$codice,"f"=>$etichetta),
                        array("v"=>intval($numero),"f"=>number_format($numero, 0, ".", ","))
                    )
                );
                $rs->MoveNext();
            }
        }
    }
    echo_json($result);
    
    exit();
});

#
# JSON PIE QUANTITATIVA
#
$this->respond(array('GET', 'POST'), '/json/pie[2]?', function ($request, $response, $service, $app) { 
    $session = getSession();
    $db = getDB();
    
    $anno = $request->anno;
    $campo_azione = $request->campo_azione;
    $tipo_compilazione = $request->tipo_compilazione;
    $stato = $request->stato;
    $codice_domanda = $request->codice_domanda;
    $struttura = $request->struttura;
    if (strlen($codice_domanda) == 0)
        exit();
    if (strlen($anno) == 0)
        $anno = 0;
    if (strlen($campo_azione) == 0)
        $campo_azione = '*';
    if (strlen($tipo_compilazione) == 0)
        $tipo_compilazione = '*';
    if (strlen($stato) == 0)
        $stato = '*';
    if (strlen($struttura) == 0)
        $struttura = '*';
    
    $_SESSION["FILTER-REPORT"] = array(
        "anno" => $anno,
        "stato" => $stato,
        "struttura" => $struttura,
        "campo_azione" => $campo_azione,
        "tipo_compilazione" => $tipo_compilazione,
        "codice_domanda" => $codice_domanda,
        "versione" => 2,
    );
    
    $result = array();
    $sql = "SELECT risp.codice_risposta as codice, testo_risposta as etichetta, sum(CONVERT(decimal, supplement1)) as numero, 'unità' as unita
            FROM MISSIONE3_Attivita a
            join MISSIONE3_Risposte risp ON a.code=risp.code_attivita
            join MISSIONE3_CONF_PossibiliRisposte r ON risp.codice_risposta=r.codice_risposta AND a.anno=r.anno
            join MISSIONE3_CONF_Domande d on d.codice=r.codice_domanda AND d.anno=r.anno
            LEFT JOIN MISSIONE3_PersonaleCoinvolto p ON p.code_attivita=a.code
            WHERE risp.codice_domanda=?
            and campo_aggiuntivo_tipo IN ('INT', 'DEC', 'NM')
            AND (a.anno=? OR ?=0)
            AND (a.campo_azione=? OR ?='*')
            AND (a.tipo_compilazione=? OR ?='*')
            AND (a.stato=? OR ?='*')
            AND (p.codice_struttura=? OR ?='*')
            GROUP BY risp.codice_risposta, testo_risposta, r.ordinamento
            ORDER BY r.ordinamento";
    $rs = $db->Execute($sql, array(
        $codice_domanda,
        $anno, $anno,
        $campo_azione, $campo_azione,
        $tipo_compilazione, $tipo_compilazione,
        $stato, $stato,
        $struttura, $struttura
    ));
    
    $_SESSION["FILTER-REPORT"]["datatable"] = $rs->GetArray();
    
    
    
    if ($rs != FALSE) {
        $result['cols'] = array(
          array('label'=>'Valore selezionato nelle risposte','pattern'=>'','type'=>'string'),
          array('label'=>'Peso dell\'indicatore ','pattern'=>'','type'=>'number')
        );
        if ($rs->RecordCount() == 0) {
            $codice = "";
            $numero = 100;
            $etichetta = "Non definito";
            
            $result['rows'][] = array(
                "c"=>array(
                    array("v"=>$codice,"f"=>$etichetta),
                    array("v"=>intval($numero),"f"=>null)
                )
            );
        }
        else {
            while(!$rs->EOF) {
                $codice = $rs->Fields("codice");
                $numero = $rs->Fields("numero");
                $unita = $rs->Fields("unita");
                #$etichetta = $rs->Fields("etichetta")." ({$unita} ".number_format($numero, 2, ',', '.').")";
                $etichetta = $rs->Fields("etichetta")." ({$numero} {$unita})";
                
                $result['rows'][] = array(
                    "c"=>array(
                        array("v"=>$codice,"f"=>$etichetta),
                        array("v"=>intval($numero),"f"=>number_format($numero, 0, ".", ","))
                    )
                );
                $rs->MoveNext();
            }
        }
    }
    echo_json($result);
    
    exit();
});

# INNER LIST
$this->respond('GET', "/inner/[:domanda]", function ($request, $response, $service, $app) {
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
    
    /*
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
			AND (tipo_compilazione='{$filtro_tipo_compilazione}' OR LEN('{$filtro_tipo_compilazione}')=0)
			AND (stato='{$filtro_stato}' OR LEN('{$filtro_stato}')=0)
			AND (anno='{$filtro_anno}' OR LEN('{$filtro_anno}')=0)
			AND (campo_azione='{$filtro_campo_azione}' OR LEN('{$filtro_campo_azione}')=0)
            ORDER BY anno desc, campo_azione
            OFFSET {$offset} ROWS
            FETCH NEXT {$row_per_page} ROWS ONLY;";   
    */

    $anno = $request->param("anno", date("Y"));
    $codice_domanda = $request->param("domanda", '');
    $sql = "select a.*, r.codice_risposta, r.supplement1, pr.testo_risposta
            from MISSIONE3_Attivita a
            join MISSIONE3_Risposte r ON a.code=r.code_attivita 
            join MISSIONE3_CONF_PossibiliRisposte pr ON pr.codice_risposta=r.codice_risposta AND pr.anno=a.anno
            where r.codice_domanda=?";
    $rs = $db->Execute($sql, array($codice_domanda)); #echo $sql;
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
    $session->smarty->display("reportistica-inner-list.tpl");
});







$this->respond('GET', "/test", function ($request, $response, $service, $app) {
    $session = getSession();
    
    $session->smarty->display("reportistica-test-inner.tpl");
});



$this->respond('POST', "/download-image", function ($request, $response, $service, $app) {
    if(isset($_POST["hidden_div_html"]) && $_POST["hidden_div_html"] != '') {
        $session = getSession();
        $session->log("/test/print dentro");
        $html = $_POST["hidden_div_html"];
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);
        $tags = $doc->getElementsByTagName('img');
        $i=1;
        $result = "";
        
        # GRAFICO
        foreach ($tags as $tag) {
            $file_name = STATIC_DIR.'/img/google_chart'.$i.'.png';
            $session->log($file_name);
            $img_Src=$tag->getAttribute('src');
            $data = explode( ',', $img_Src );
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="grafico.png"');
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
            header('Expires: January 01, 2013'); // Date in the past
            header('Pragma: no-cache');
            header("Content-Type: image/jpg");
            /* header("Content-Length: " . filesize($name)); */
            echo base64_decode($data[1]);
            exit();
          $i++;
        }
    }
});


$this->respond('POST', "/download-pdf", function ($request, $response, $service, $app) {
    $session = getSession();
    $db = getDB();
    
    $filtri = $_SESSION["FILTER-REPORT"];
    $datatable = $filtri["datatable"];
    
    $sql = "SELECT TOP 1 *
            FROM MISSIONE3_CONF_Domande 
            WHERE codice=? ORDER BY anno DESC";
    $rs = $db->Execute($sql, array(
        $filtri["codice_domanda"]
    ));
    $domanda = $rs->GetRow();
    
    if(isset($_POST["hidden_div_html"]) && $_POST["hidden_div_html"] != '') {
        $session = getSession();
        $session->log("/test/print dentro");
        $html = $_POST["hidden_div_html"];
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);
        $tags = $doc->getElementsByTagName('img');
        $i=1;
        $result = "<h1>Domanda {$filtri["codice_domanda"]}</h1><h3>{$domanda["descrizione"]}</h3>";
        
        # FILTRI
        $result .= "<h3>Filtri</h3><ul>";
        $result .= "<li>Anno: ";
        $result .= ($filtri["anno"] == 0) ? "tutti" : $filtri["anno"];
        $result .= "</li><li>Campo d'azione: ";
        $result .= ($filtri["campo_azione"] == '*') ? "tutti" : $filtri["campo_azione"];
        $result .= "</li><li>Tipologia di compilazione: ";
        if ($filtri["tipo_compilazione"] == '*')
            $result .= "tutte";
        else
            $result .= ($filtri["tipo_compilazione"] == 'B') ? 'base' : 'valutazione';
        $result .= "</li><li>Stato attività: ";
        if ($filtri["stato"] == '*')
            $result .= "tutti";
        else
            $result .= ($filtri["stato"] == 'B') ? 'bozza' : 'validata';
        $result .= "</li></ul>";
        
        # GRAFICO
        foreach ($tags as $tag) {
            $file_name = STATIC_DIR.'/img/google_chart'.$i.'.png';
            $session->log($file_name);
            $img_Src=$tag->getAttribute('src');
            $data = explode( ',', $img_Src );
            file_put_contents($file_name, base64_decode($data[1]));
            $res= '<img src="'.STATIC_DIR.'/img/google_chart'.$i.'.png">';
            $result .= $res;
          $i++;
        }

        # TABELLA
        $result .= "<div><table border=1><tr><th>Valore selezionato nelle risposte</th><th>Numero di occorrenze</th></tr>";
        foreach($datatable as $item) {
            $result .= "<tr><td>{$item["etichetta"]}</td><td>{$item["numero"]}</td></tr>";
        }
        $result .= "</table></div>";
        
        //include make_pdf
        //include("mpdf60/mpdf.php");
        $mpdf = new \Mpdf\Mpdf();

        $mpdf->allow_charset_conversion = true;
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->showImageErrors = true;
        $mpdf->list_indent_first_level = 0; // 1 or 0 - whether to indent the first level of a list
        $mpdf->WriteHTML($result);
        $mpdf->Output();
    }
});

$this->respond('GET', "/download-xls", function ($request, $response, $service, $app) {
    $session = getSession();
    $db = getDB();
    
    $filtri = $_SESSION["FILTER-REPORT"];

    $datatable = $filtri["datatable"];
    
    $sql = "SELECT TOP 1 *
            FROM MISSIONE3_CONF_Domande 
            WHERE codice=? ORDER BY anno DESC";
    $rs = $db->Execute($sql, array(
        $filtri["codice_domanda"]
    ));
    $domanda = $rs->GetRow();
    
    
    # TABELLA
        $result .= "<div><table border=1><tr><th>Valore selezionato nelle risposte</th><th>Numero di occorrenze</th></tr>";
        foreach($datatable as $item) {
            $result .= "<tr><td>{$item["etichetta"]}</td><td>{$item["numero"]}</td></tr>";
        }
        $result .= "</table></div>";
        
        
    
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
    foreach($datatable as $row) {
        if ($first) {
            //----------------------------------------------------------------------------
            $sheet->setCellValueByColumnAndRow(1, 1, "Valore selezionato nelle risposte");
            $sheet->setCellValueByColumnAndRow(2, 1, "Numero di occorrenze");
            $first = false;
            $row_index++;
        }
        
        $valore = @trim($row["etichetta"]);
        $sheet->setCellValueByColumnAndRow(1, $row_index, $valore);
            
        $valore = @trim($row["numero"]);
        $sheet->setCellValueByColumnAndRow(2, $row_index, $valore);
        
        $row_index++;
    }
    
    $spreadsheet->setActiveSheetIndex(0);
    
    ob_clean();
    
    // Redirect output to a client’s web browser (Xlsx)
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="terza-missione-totali-grafico.xlsx"');
    header("Content-Transfer-Encoding: binary");
    
     // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Pragma: public'); // HTTP/1.0
    
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save("php://output");
    
});
