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
    // $session->assertLogin();
    
    $mobile = false;
    $detect = new \Detection\MobileDetect;
 
    // Any mobile device (phones or tablets).
    if ( $detect->isMobile() ) {
        $mobile = true;
    }
    
    $session->smarty->assign("BASE_URL", BASE_URL);
    $session->smarty->assign("STATIC_URL", STATIC_URL);
    $session->smarty->assign("APPNAME", APPNAME);
    $session->smarty->assign("TEST", TEST);
    $session->smarty->assign("RIBBON", RIBBON);
    $session->smarty->assign("MOBILE", $mobile);
    $session->smarty->assign("REQUEST_URI", str_ireplace(BASE_URL, "", $request->pathname()));
    $session->smarty->assign("REQUEST", $request);
    $session->smarty->assign("EMAIL_SUPPORT", EMAIL_SUPPORT);
    
    // $service->addValidator('SearchName', function ($str) {
        // return preg_match("/^([0-9a-z@. ])*(['])?([0-9a-z@. ])*(['?????])?$/i", $str);
    // });
    
    $session->smarty->assign("now", time());
    $session->smarty->assign("session", $session);
    $session->smarty->assign("operatore", $session->user());
    $session->save();
});





# INDEX
$this->respond('GET', "/home", function ($request, $response, $service, $app) {
    $session = getSession();
    $session->log("PLUGIN HOME");
    $session->assertLogin();
    $operatore = $session->user();
    
    $missione3_home_menu = array(
        "anno_corrente" => array("link" => BASE_URL."/archivio/list", "image" => "calendar", "title" => "Gestione Attività"),
        #"cerca" => array("link" => BASE_URL."", "image" => "search", "title" => "Cerca"),
        #"documentazione" => array("link" => BASE_URL."", "image" => "book", "title" => "Documentazione"),
    );
    if ($operatore->has("MISSIONE3ADMIN")){ 
        $missione3_home_menu[] = array("link" => BASE_URL."/configurazione/list", "image" => "tools", "title" => "Configurazioni");
        $missione3_home_menu[] = array("link" => BASE_URL."/reportistica", "image" => "pie chart", "title" => "Statistiche");
    }
    
    $missione3_home_menu[] = array("link" => BASE_URL."/documentazione/list", "image" => "book", "title" => "Documentazione");
    $missione3_home_menu[] = array("link" => BASE_URL."/faq/list", "image" => "comments", "title" => "FAQ");
    
    $session->smarty->assign("HOMEPAGE", true);
    $session->smarty->assign("operatore", $operatore);
    $session->smarty->assign("missione3_home_menu", $missione3_home_menu);
    
    $session->log("PLUGIN HOME:: pre display");
    $session->smarty->display("index.tpl");
    $session->log("PLUGIN HOME:: post display");
});


# LOGIN WM
$this->respond('GET', "/login-wm", function ($request, $response, $service, $app) {
    $session = getSession();
    $session->smarty->display("login-wm.tpl");
});




# INDEX
// $this->respond('POST', "/test/post", function ($request, $response, $service, $app) {
    
    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";
// });









$this->respond('GET', '/importa/2020', function ($request, $response, $service) {
    error_log("GET /importa");
    $session = getSession();
    $session->assertLogin();
    $db = getDB();
    
    $filename = LOG_DIR."/PE_2020.xlsx";
    
    $xlsx = new \SimpleXLSX($filename);
    if ($xlsx->success()) {
        #$session->log( $xlsx->rows() );
        
        $rows = $xlsx->rows();
        
        $nomi_colonne = array_merge(array(0 => "code"), $rows[0]);
        
        // echo "<table border=1>";
        // foreach($rows as $r => $item) {
            // echo "<tr>";
            // foreach($item as $c => $value) {
            
                // echo "<td>{$c}|{$value}";
                // echo "</td>";
            
            // }
            // echo "</tr>";
        // }
        // echo "</table>";
        
        foreach($rows as $r => $item) {
            if ($r < 2)
                continue;
            
            $params = array();
            $params[0] = md5(microtime());
            foreach($item as $k => $v) {
                if (strlen($nomi_colonne[$k+1]) > 0)
                    $params[$k+1] = $v;
            }
            
            $sql = "INSERT INTO WORK_PE_2020 VALUES(";
            foreach($params as $n)
                $sql .= "?,";
            $sql = substr($sql, 0, -1);
            $sql .= ")";
            $rs = $db->Execute($sql, $params);
        }

    } else {
        error_log( 'xlsx error: '.$xlsx->error());
    }
  
    exit('fine');
});



$this->respond('GET', '/importa/2022', function ($request, $response, $service) {
    error_log("GET /importa");
    $session = getSession();
    $session->assertLogin();
    $db = getDB();
    
    $filename = LOG_DIR."/PE_2022.xlsx";
    
    $xlsx = new \SimpleXLSX($filename);
    if ($xlsx->success()) {
        #$session->log( $xlsx->rows() );
        
        $rows = $xlsx->rows();
        
        $nomi_colonne = array_merge(array(0 => "code"), $rows[0]);
        
        // echo "<table border=1>";
        // foreach($rows as $r => $item) {
            // echo "<tr>";
            // foreach($item as $c => $value) {
            
                // echo "<td>{$c}|{$value}";
                // echo "</td>";
            
            // }
            // echo "</tr>";
        // }
        // echo "</table>";
        
        foreach($rows as $r => $item) {
            if ($r < 2)
                continue;
            
            $params = array();
            $params[0] = md5(microtime());
            foreach($item as $k => $v) {
                if (strlen($nomi_colonne[$k+1]) > 0)
                    $params[$k+1] = $v;
            }
            
            $sql = "INSERT INTO WORK_PE_2022 VALUES(";
            foreach($params as $n)
                $sql .= "?,";
            $sql = substr($sql, 0, -1);
            $sql .= ")";
            $rs = $db->Execute($sql, $params);
        }

    } else {
        error_log( 'xlsx error: '.$xlsx->error());
    }
  
    exit('fine');
});









$this->respond('GET', '/update/date', function ($request, $response, $service) {
    error_log("GET /update/date");
    $session = getSession();
    $session->assertLogin();
    $db = getDB();
    
    $sql = "SELECT * FROM webmanagement.dbo.MISSIONE3_Risposte where codice_domanda='G_2'";
    $rs = $db->Execute($sql);
    
    $mesi = array(
        'gen' => '01',
        'feb' => '02',
        'mar' => '03',
        'apr' => '04',
        'mag' => '05',
        'giu' => '06',
        'lug' => '07',
        'ago' => '08',
        'set' => '09',
        'ott' => '10',
        'nov' => '11',
        'dic' => '12',
    );
    
    while (!$rs->EOF) {
        $row = $rs->GetRow();
        $code = $row["code"];
        $dt_ini = $dt_fin = '';
        $g_2 = str_replace("  ", " 0", $row["supplement1"]);
        $g_3 = str_replace("  ", " 0", $row["supplement2"]);
        list($m1, $g1, $a1) = explode(" ", $g_2);
        list($m2, $g2, $a2) = explode(" ", $g_3);
        
        $dt_ini = "{$a1}-{$mesi[$m1]}-{$g1}";
        $dt_fin = "{$a2}-{$mesi[$m2]}-{$g2}";
        $params = array($dt_ini, $dt_fin, $code);
        #print_r($params);
        $sql = "UPDATE MISSIONE3_Risposte SET supplement1=?, supplement2=? WHERE code=?";
        $db->Execute($sql, $params);
    }

    
    exit('fine');
});


