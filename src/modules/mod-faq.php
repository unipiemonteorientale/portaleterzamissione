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

    $MODULO_CODE = "FAQ";
    #$applicazione->setModulo($MODULO_CODE);
    $APP = [
        "title" => "FAQ",
        "url" => BASE_URL."/faq",
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
    $session->smarty->display("faq-container-list.tpl");
    exit();
});

# INNER LIST
$this->respond('GET', "/inner/list", function ($request, $response, $service, $app) {
    $session = getSession();
    $db = getDB();
    $operatore = $session->user();
    
    $faqs = array();
    $sql = "SELECT *
            FROM MISSIONE3_CONF_FAQ
            ORDER BY sorting";
    $rs = $db->Execute($sql, array()); #echo $sql;
    while(!$rs->EOF) {
        $row = $rs->GetRow();
        $faqs[] = $row;
    }
    
    
    
    $session->smarty()->assign("anno", $anno);
    $session->smarty()->assign("faqs", $faqs);
    $session->smarty->display("faq-inner-list.tpl");
});