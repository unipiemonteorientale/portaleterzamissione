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

    $MODULO_CODE = "M3DOC";
    #$applicazione->setModulo($MODULO_CODE);
    $APP = [
        "title" => "Documentazione",
        "url" => BASE_URL."/documentazione",
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
    $session->smarty->display("documentazione-container-list.tpl");
    exit();
});

# INNER LIST
$this->respond('GET', "/inner/list", function ($request, $response, $service, $app) {
    $session = getSession();
    $db = getDB();
    $operatore = $session->user();
    
    $risorse = array();
    $sql = "SELECT *
            FROM MISSIONE3_CONF_Documentazione
            ORDER BY sorting";
    $rs = $db->Execute($sql, array()); #echo $sql;
    while(!$rs->EOF) {
        $row = $rs->GetRow();
        
        $url = BASE_URL."/files/MISSIONE3/".base64_encode(FILES_DIR."/MISSIONE3/".$row["file_locazione"]);
        $row["file_url"] = $url;
        
        $risorse[] = $row;
    }
    
    
    
    $session->smarty()->assign("anno", $anno);
    $session->smarty()->assign("risorse", $risorse);
    $session->smarty->display("documentazione-inner-list.tpl");
});