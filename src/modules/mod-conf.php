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

    $MODULO_CODE = "M3CONF";
    #$applicazione->setModulo($MODULO_CODE);
    $APP = [
        "title" => "M3CONF",
        "url" => BASE_URL."/conf",
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
    $session->smarty->display("conf-container-list.tpl");
    exit();
});


# INNER LIST TABULATOR
$this->respond('GET', "/inner/list", function ($request, $response, $service, $app) {
    $session = getSession();
    
    $tabulator = new UITabulator();
    $sql = "SELECT context_name as [sezione/campo di azione], element_code as [codice domanda], label1_code as domanda, sorting as ordinamento
			FROM WEB3_Elements
			WHERE object_code='M3_DOMANDE'";   
    $sqlparams = null;
    $tabulator->setSource($sql, $sqlparams, md5($sql), array("ident", "code"));
        
    $session->smarty()->assign("tabulator", $tabulator);
    $session->smarty()->assign("anno", $request->anno);
    $session->smarty->display("conf-inner-list-tabulator.tpl");
    exit();
});



# FORM 
$this->respond('GET', "/[:codice_domanda]/[:tab_name]?", function ($request, $response, $service, $app) {
    $session = getSession();
    $db = getDB();
    
    $codice_domanda = $request->codice_domanda;
    $tab_name = $request->tab_name;
    
    if ($tab_name == "properties" || strlen($tab_name) == 0) {
        
    }
    else {
        $sql = "SELECT * FROM WEB3_Relations WHERE relation_code=?";
        $rs = $db->Execute($sql, array($tab_name));
        $relation = $rs->GetRow();
        
        $url = $relation["url"];
        if (strlen($url)) {
            $url = str_replace("%CODE%", $record_code, $url);
            header("Location:".$url);
            exit();
        }
        
        $tabulator = new UITabulator();
        
        $sql = "SELECT * FROM WEB3_TreeNodes WHERE tree_code=?";
        $tabulator->setSource($sql, array($record_code), "RELATION".$record_code.md5(microtime()));
            
        /*$relation = new Relation($tabpage);
        $recordset_code = $relation->get("recordset_code");
    
        
        
        if (strlen($recordset_code) > 0) {
            
            $tabulator->setRecordset($recordset_code, "AND {$relation->dbfieldMaster()}=?", array($record_code));
            
        }
        else {
            $sql = "SELECT * FROM {$relation->dbview()} WHERE {$relation->dbfieldMaster()}=?";
            $tabulator->setSource($sql, array($record_code), "RELATION".$record_code.md5(microtime()));
        }*/
        $recordset_code = $relation["recordset_code"];
        if (strlen($recordset_code) > 0) {
            $tabulator->setRecordset($recordset_code, "AND {$relation["master_dbfield"]}=?", array($record_code));
        }
        else {
            $sql = "SELECT * FROM {$relation["dbview"]} WHERE {$relation["master_dbfield"]}=?";
            $tabulator->setSource($sql, array($record_code), "RELATION".$record_code.md5(microtime()));
        }
        
        $tabulator_name = "tab".md5(microtime());
        // $rs = $db->Execute($sql, array($record_code));
        // $columns = array();
        // while(!$rs->EOF) {
            // $row = $rs->GetRow();
            // $key = strtolower($row["dbcolumn"]);
            // $columns[$key] = $row;
        // }
        
        // 
        // #$tabulator->setTitle("Colonne configurate");

        $session->smarty()->assign("context_name", $context_name);
        $session->smarty()->assign("object_name", $object_name);
        $session->smarty()->assign("relation", $relation);
        $session->smarty()->assign("record_code", $record_code);
        $session->smarty()->assign("tabulator", $tabulator);
        $session->smarty()->assign("tabulator_name", $tabulator_name);
        
        $session->smarty()->display("object-relation-tab.tpl");
    }
});



