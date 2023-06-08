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
    
    if (!$operatore->has("MISSIONE3ADMIN")) {
        $session->smarty()->display("403.tpl");
        http_response_code(403);
        exit();
    }

    $MODULO_CODE = "M3CONF";
    #$applicazione->setModulo($MODULO_CODE);
    $APP = [
        "title" => "M3CONF",
        "url" => BASE_URL."/configurazione",
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
    $session->smarty()->assign("argomento", $request->param("argomento", 'domande'));
    $session->smarty->display("configurazione-container-list.tpl");
    exit();
});


# INNER LIST 
$this->respond('GET', "/inner/[domande|documentazione|faq:argomento]/list", function ($request, $response, $service, $app) {
    $session = getSession();
    $db = getDB();
    
    $anno = $request->param("anno", '');
    if (strlen($anno) == 0)
        $anno = (strlen($_SESSION["filter_anno"])) ? $_SESSION["filter_anno"] : date("Y");
    else
        $_SESSION["filter_anno"] = $anno;
    $session->smarty()->assign("anno", $anno);
    
    $argomento = $request->argomento;
    
    if ($argomento == "domande") {
        $sezione = $request->param("sezione", '');
        if (strlen($sezione) == 0)
            $sezione = (strlen($_SESSION["filter_sezione"])) ? $_SESSION["filter_sezione"] : 'AP';
        else
            $_SESSION["filter_sezione"] = $sezione;
                
        $sql = "SELECT anno, sezione as context_name, codice as codice_domanda, descrizione as domanda, sorting as ordinamento, d.help_breve
                FROM MISSIONE3_CONF_Domande d
                WHERE sezione=? AND anno=?
                ORDER BY sorting";   
        $sqlparams = array($sezione, $anno);
        $rs = $db->Execute($sql, $sqlparams);
        
        $session->smarty()->assign("domande", $rs->GetArray());
        $session->smarty()->assign("sezione", $sezione);
        $session->smarty->display("configurazione-inner-list.tpl");
    }
    elseif ($argomento == 'faq') {
        $sql = "SELECT *
                FROM MISSIONE3_CONF_FAQ
                ORDER BY sorting";   
        $sqlparams = array();
        $rs = $db->Execute($sql, $sqlparams);
        
        $session->smarty()->assign("faq", $rs->GetArray());
        $session->smarty->display("configurazione-faq-inner-list.tpl");
    }
    elseif ($argomento == "documentazione") {
        $sql = "SELECT pc.*, n.label_code as tipologia_allegato
                FROM MISSIONE3_CONF_Documentazione pc
                LEFT JOIN WEB3_TreeNodes n ON n.node_value=pc.tipologia AND n.tree_code='M3_TIPO_ALLEGATO'
                ORDER BY sorting";   
        $sqlparams = array();
        $rs = $db->Execute($sql, $sqlparams);
        $list = $rs->GetArray();
        foreach($list as $k => $item) {
            $url = BASE_URL."/files/MISSIONE3/".base64_encode(FILES_DIR."/MISSIONE3/".$item["file_locazione"]);
            $list[$k]["file_url"] = $url;
        }
        
        $session->smarty()->assign("documentazione", $list);
        $session->smarty()->assign("file_base_url", $file_base_url);
        $session->smarty()->assign("object_name", "M3_DOCUMENTAZIONE");
        $session->smarty->display("configurazione-documentazione-inner-list.tpl");
    }
    
    exit();
});



# TAB 
$this->respond('GET', "/[:codice_domanda]/?[:tab_name]?", function ($request, $response, $service, $app) {
    $session = getSession();
    $db = getDB();
    
    $codice_domanda = $request->codice_domanda;
    $tab_name = $request->tab_name;
    $anno = $_SESSION["filter_anno"];  //$request->param("anno", date("Y"));
    $sezione = $request->param("sezione", explode('_', $codice_domanda)[0]);
        
    $session->smarty()->assign("codice_domanda", $codice_domanda);
    $session->smarty()->assign("anno", $anno);
    $session->smarty()->assign("tab_name", $tab_name);
    $session->smarty()->assign("sezione", $sezione);
    
    if (strlen($tab_name) == 0) {
        $session->smarty()->assign("modal_id", md5(microtime()));
        $session->smarty()->display("configurazione-modal-domanda-container.tpl");
    }
    elseif ($tab_name == "domanda") {
        $sql = "SELECT anno, codice, descrizione, help_breve
                FROM MISSIONE3_CONF_Domande d
                WHERE codice=? AND anno=?";  # 
        #$sql = "SELECT label1_code as descrizione FROM WEB3_Elements WHERE element_code=?";  
        $rs = $db->Execute($sql, array($codice_domanda, $anno));
        $domanda = $rs->GetRow();
        $session->smarty()->assign("domanda", $domanda);
        $session->smarty()->display("configurazione-tab-domanda-domanda.tpl");
    }
    elseif ($tab_name == "risposte") {
        $sql = "SELECT *
                FROM MISSIONE3_CONF_PossibiliRisposte d
				WHERE codice_domanda=? AND anno=?
                ORDER BY ordinamento";  # 
        #$sql = "SELECT label1_code as descrizione FROM WEB3_Elements WHERE element_code=?";  
        $rs = $db->Execute($sql, array($codice_domanda, $anno));
        $risposte = $rs->GetArray();
        $session->smarty()->assign("risposte", $risposte);
        $session->smarty()->display("configurazione-tab-domanda-risposte.tpl");
    }
    elseif ($tab_name == "regole") {
        
        # Rules default
        $sql = "SELECT default_visibile, default_obbligatoria, d.sezione, d.codice as codice_domanda, w3e.label1_code /*d.descrizione*/ as testo_domanda
                    , codice_risposta_padre, codice_padre
                FROM MISSIONE3_CONF_Domande d
				JOIN WEB3_Elements w3e ON w3e.context_name=d.sezione AND w3e.element_code=d.codice
                WHERE d.codice=?
                ORDER BY LEN(d.sezione) desc, w3e.sorting, codice_domanda";
        $rs = $db->Execute($sql, array($codice_domanda));
        $rows = $rs->GetArray();
        foreach($rows as $row) {
            $visibile = $row["default_visibile"];
            $obbligatoria = $row["default_obbligatoria"];
            
            $rules[$codice_domanda] = array(
                "default_visibile" => $visibile,
                "default_obbligatoria" => $obbligatoria,
                // "visibile" => $visibile,
                // "obbligatoria" => $obbligatoria,
            );
        }
        
        # Rules proprie del campo di azione
        $sql = "select * from MISSIONE3_CONF_Regole WHERE codice_domanda=?";
        $rs = $db->Execute($sql, array($codice_domanda));
        $rows = $rs->GetArray();
        foreach($rows as $row) {
            
            if ($row["regola_visibile"] == "RU02" || $row["regola_visibile"] == "ALL") 
                $rules[$codice_domanda]["visibile"] = $row["visibile"];
            if ($row["regola_obbligatoria"] == "RU02" || $row["regola_obbligatoria"] == "ALL") 
                $rules[$codice_domanda]["obbligatoria"] = $row["obbligatoria"];
        }
        
        $session->smarty()->assign("rules", $rules[$codice_domanda]);
        $session->smarty()->display("configurazione-tab-domanda-regole.tpl");
    }
    elseif ($tab_name == "help") {
        $sql = "SELECT * FROM MISSIONE3_CONF_Help WHERE codice_domanda=? ORDER BY sezione";
        $rs = $db->Execute($sql, array($codice_domanda));
        $helps = $rs->GetArray();
        $session->smarty()->assign("helps", $helps);
        $session->smarty()->display("configurazione-tab-domanda-help.tpl");
    }
    else {
        echo "<h1>TODO {$tab_name}</h1>";
        exit();
        
    }
});




# FORM 
$this->respond('GET', "/form/[:tab_name]/[:codice]/[:sezione]", function ($request, $response, $service, $app) {
    $session = getSession();
    $db = getDB();
    
    $anno = $request->param("anno", date("Y"));
    $codice = $request->codice;
    $codice_domanda = $codice;
    $tab_name = $request->tab_name;
    $sezione = $request->sezione;
    $nuovo = $request->param("new", false);
    
    if ($tab_name == "domanda") {
        $sql = "SELECT w3e.element_code as codice, w3e.label1_code as descrizione, d.help_breve
                FROM MISSIONE3_CONF_Domande d
				JOIN WEB3_Elements w3e ON w3e.context_name=d.sezione AND w3e.element_code=d.codice 
                WHERE codice=?";  # 
        #$sql = "SELECT label1_code as descrizione FROM WEB3_Elements WHERE element_code=?";  
        $rs = $db->Execute($sql, array($codice_domanda));
        $domanda = $rs->GetRow();
        $session->smarty()->assign("domanda", $domanda);
    }
    elseif ($tab_name == "risposte") {
        $sql = "SELECT *
                FROM MISSIONE3_CONF_PossibiliRisposte d
				WHERE codice_risposta=?";  # 
        #$sql = "SELECT label1_code as descrizione FROM WEB3_Elements WHERE element_code=?";  
        $rs = $db->Execute($sql, array($codice));
        $risposta = $rs->GetRow();
        $session->smarty()->assign("risposta", $risposta);
    }
    elseif ($tab_name == "help") {
        $sql = "SELECT * FROM MISSIONE3_CONF_Help WHERE codice_domanda=? AND sezione=? "; # AND anno
        $rs = $db->Execute($sql, array($codice_domanda, $sezione));
        $help = $rs->GetRow();
        
        if ($nuovo) {
            if ($sezione == "AP" || $sezione == "CH") {
                $sezioni = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
                $session->smarty()->assign("sezioni", $sezioni);
            }
        }
        $session->smarty()->assign("nuovo", $nuovo);
        $session->smarty()->assign("help", $help);
    }
    elseif ($tab_name == "regole") {
        # Rules default
        $sql = "SELECT default_visibile, default_obbligatoria, d.sezione, d.codice as codice_domanda, w3e.label1_code /*d.descrizione*/ as testo_domanda
                    , codice_risposta_padre, codice_padre
                FROM MISSIONE3_CONF_Domande d
				JOIN WEB3_Elements w3e ON w3e.context_name=d.sezione AND w3e.element_code=d.codice
                WHERE d.codice=?
                ORDER BY LEN(d.sezione) desc, w3e.sorting, codice_domanda";
        $rs = $db->Execute($sql, array($codice_domanda));
        $rows = $rs->GetArray();
        foreach($rows as $row) {
            $visibile = $row["default_visibile"];
            $obbligatoria = $row["default_obbligatoria"];
            
            $rules[$codice_domanda] = array(
                "default_visibile" => $visibile,
                "default_obbligatoria" => $obbligatoria,
                // "visibile" => $visibile,
                // "obbligatoria" => $obbligatoria,
            );
        }
        
        # Rules proprie del campo di azione
        $sql = "select * from MISSIONE3_CONF_Regole WHERE codice_domanda=?";
        $rs = $db->Execute($sql, array($codice_domanda));
        $rows = $rs->GetArray();
        foreach($rows as $row) {
            
            if ($row["regola_visibile"] == "RU02" || $row["regola_visibile"] == "ALL") 
                $rules[$codice_domanda]["visibile"] = $row["visibile"];
            if ($row["regola_obbligatoria"] == "RU02" || $row["regola_obbligatoria"] == "ALL") 
                $rules[$codice_domanda]["obbligatoria"] = $row["obbligatoria"];
        }
        
        $session->smarty()->assign("rules", $rules[$codice_domanda]);
    }
    elseif ($tab_name == "faq") {
        if ($nuovo) {
            $codice = '';
            $faq = array();
        }
        else {
            $sql = "SELECT * FROM MISSIONE3_CONF_FAQ WHERE code=?"; # 
            $rs = $db->Execute($sql, array($codice));
            $faq = $rs->GetRow();
        }
        
        $categorie = array('generale');
        $session->smarty()->assign("categorie", $categorie);
        $session->smarty()->assign("codice", $codice);
        $session->smarty()->assign("faq", $faq);
    }
    elseif ($tab_name == "documentazione") {
        if ($nuovo) {
            $codice = '';
            $documentazione = array();
        }
        else {
            $sql = "SELECT * FROM MISSIONE3_CONF_Documentazione WHERE code=?"; # 
            $rs = $db->Execute($sql, array($codice));
            $documentazione = $rs->GetRow();
        }
        
        $categorie = array('generale');
        $session->smarty()->assign("categorie", $categorie);
        $session->smarty()->assign("codice", $codice);
        $session->smarty()->assign("documentazione", $documentazione);
    }
    
    $session->smarty()->assign("anno", $anno);
    $session->smarty()->assign("tab_name", $tab_name);
    $session->smarty()->assign("codice_domanda", $codice_domanda);
    $session->smarty()->assign("sezione", $sezione);
    $session->smarty()->display("configurazione-form-{$tab_name}.tpl");
});
# FORM POST
$this->respond('POST', "/form/[:tab_name]/[:codice]/[:sezione]", function ($request, $response, $service, $app) {
    $session = getSession();
    $db = getDB();
    $result = new Result();
    
    $anno = $request->param("anno", date("Y"));
    $codice_domanda = $request->codice;
    $tab_name = $request->tab_name;
    $sezione = $request->sezione;
    
    if ($tab_name == "help") {
        $nuova_sezione = $request->param("nuova_sezione", false);
        $testo = $request->help_text_new;
        
        if ($nuova_sezione === false) {
            $nuovo = false;
        }
        else {
            $nuovo = true;
            $sezione = $nuova_sezione;
            
            $sql = "SELECT * FROM MISSIONE3_CONF_Help WHERE codice_domanda=? AND sezione=? "; # AND anno
            $rs = $db->Execute($sql, array($codice_domanda, $sezione));
            if ($rs->recordCount() > 0) {
                $result->setResult(false);
                $result->setCode("KO");
                $result->setDescription("Esiste già un help per la domanda {$codice_domanda} e il campo d'azione {$sezione}. Devi modificare quel record.");
                $result->setLevel(Result::ERROR);
                $result->toJson();
                exit();
                
            }
        }
        
        $sql = "MERGE MISSIONE3_CONF_Help AS target  
                USING (SELECT ? as sezione, ? as codice_domanda, ? as testo_help) as src   
                    ON (target.sezione=src.sezione AND target.codice_domanda=src.codice_domanda)  
                WHEN MATCHED THEN
                    UPDATE SET testo_help = src.testo_help  
                WHEN NOT MATCHED THEN  
                    INSERT (sezione, codice_domanda, testo_help)  
                    VALUES (src.sezione, src.codice_domanda, src.testo_help);";
        $rs = $db->Execute($sql, array($sezione, $codice_domanda, $testo));
    }
    elseif ($tab_name == "domanda") {
        $testo_domanda= $request->param("testo_domanda", '');
        $help_breve= $request->param("help_breve", '');
        
        $sql = "UPDATE MISSIONE3_CONF_Domande 
                SET descrizione=?, help_breve=?
                WHERE codice=?";
        $rs = $db->Execute($sql, array($testo_domanda, $help_breve, $codice_domanda));
        
        $sql = "UPDATE WEB3_Elements 
                SET label1_code=?
                WHERE element_code=?";
        $rs = $db->Execute($sql, array($testo_domanda, $codice_domanda));
    }
    elseif ($tab_name == "risposte") {
        $codice_domanda = $request->param("codice_domanda", '');
        $codice_risposta = $request->param("codice_risposta", '');
        $testo_risposta = $request->param("testo_risposta", '');
        $ordinamento = $request->param("ordinamento", 99);
        $campo_aggiuntivo_tipo = $request->param("campo_aggiuntivo_tipo", '');
        $campo_aggiuntivo_label = $request->param("campo_aggiuntivo_label", '');
        
        $sql = "UPDATE MISSIONE3_CONF_PossibiliRisposte 
                SET testo_risposta=?, ordinamento=?, campo_aggiuntivo_tipo=?, campo_aggiuntivo_label=?
                WHERE codice_domanda=? AND codice_risposta=?";
        $rs = $db->Execute($sql, array($testo_risposta, $ordinamento, $campo_aggiuntivo_tipo, $campo_aggiuntivo_label, $codice_domanda, $codice_risposta));
        $session->log($sql);
        $session->log(array($testo_risposta, $ordinamento, $campo_aggiuntivo_tipo, $campo_aggiuntivo_label, $codice_domanda, $codice_risposta));
    }
    elseif ($tab_name == "regole") {
        $default_visibile = $request->param("default_visibile", 'N');
        $default_obbligatoria= $request->param("default_obbligatoria", 'N');
        $visibile = $request->param("visibile", 'N');
        $obbligatoria= $request->param("obbligatoria", 'N');
        
        $sql = "UPDATE MISSIONE3_CONF_Domande 
                SET default_visibile=?, default_obbligatoria=?
                WHERE codice=?";
        $rs = $db->Execute($sql, array($default_visibile, $default_obbligatoria, $codice_domanda));
                
        $sql = "MERGE MISSIONE3_CONF_Regole AS target  
                USING (SELECT ? as visibile, ? as obbligatoria, ? as codice_domanda, ? as sezione) as src   
                    ON (target.codice_domanda=src.codice_domanda)  
                WHEN MATCHED THEN
                    UPDATE SET visibile = src.visibile, regola_visibile='RU02', obbligatoria = src.obbligatoria, regola_obbligatoria='RU02'
                WHEN NOT MATCHED THEN  
                    INSERT (visibile, regola_visibile, obbligatoria, regola_obbligatoria, codice_domanda, sezione)  
                    VALUES (src.visibile, 'RU02', src.obbligatoria, 'RU02', src.codice_domanda, src.sezione);";
        $rs = $db->Execute($sql, array($visibile, $obbligatoria, $codice_domanda, $sezione));  
    }
    elseif ($tab_name == "faq") {
        $code = $request->param("code", '');
		if ($code == '-')
			$code = '';
        $categoria = $request->param("categoria", 'generale');
        $domanda = $request->param("domanda", '');
        $risposta = $request->faq_text_new;
        $sorting = $request->param("sorting", 90);
        $delete = $request->param("delete", '0');
        $session->log($request->params());
        $session->log($_POST);
        if (strlen($code) == 0) {
            $code = md5(microtime());
            $sql = "INSERT INTO MISSIONE3_CONF_Faq (code, categoria, domanda, risposta, sorting)
                    VALUES (?, ?, ?, ?, ?)"; 
            $rs = $db->Execute($sql, array($code, $categoria, $domanda, $risposta, $sorting));        
        }
        else {
            if ($delete == '1') {
                $sql = "DELETE FROM MISSIONE3_CONF_Faq 
                        WHERE code=?"; # AND anno
                $rs = $db->Execute($sql, array($code));
            }
            else {
                $sql = "UPDATE MISSIONE3_CONF_Faq SET categoria=?, domanda=?, risposta=?, sorting=? 
                        WHERE code=?"; # AND anno
                $rs = $db->Execute($sql, array($categoria, $domanda, $risposta, $sorting, $code));
            }
        }

        // if ($rs->recordCount() > 0) {
                // $result->setResult(false);
                // $result->setCode("KO");
                // $result->setDescription("Esiste già un help per la domanda {$codice_domanda} e il campo d'azione {$sezione}. Devi modificare quel record.");
                // $result->setLevel(Result::ERROR);
                // $result->toJson();
                // exit();
                
            // }
    }
    
    
    if ($rs !== FALSE)
        echo "OK";
    else
        echo "KO";
    exit();
});











