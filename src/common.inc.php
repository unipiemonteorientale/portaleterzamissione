<?php
//  
// Copyright (c) ASPIDE.NET. All rights reserved.  
// Licensed under the GPLv3 License. See LICENSE file in the project root for full license information.  
//  
// SPDX-License-Identifier: GPL-3.0-or-later
//

namespace web3;

setlocale(LC_ALL, "en_US.utf8");

require_once "../src/customs.inc.php";
require_once WEB3_DIR."/src/functions-web3.inc.php";
require_once WEB3_DIR."/src/functions-date.inc.php";
require_once WEB3_DIR."/src/functions-files.inc.php";


define('ADODB_ASSOC_CASE', 0);
require_once BASE_DIR."/vendor/autoload.php";
// #require_once '../vendor/adodb/adodb-php/adodb-errorhandler.inc.php';
require_once BASE_DIR.'/vendor/adodb/adodb-php/adodb-exceptions.inc.php';
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

// use Monolog\Logger;  
// use Monolog\Handler\StreamHandler;    

function my_autoload ($pClassName) {
    //error_log("Cerco ".$pClassName);
    $pClassName = str_replace("web3\\", "", $pClassName);
    $filename = BASE_DIR."/src/classes/".strtolower($pClassName).".class.php";
    if (file_exists($filename)) {
        require_once($filename);
    }
    else {
        $filename = WEB3_DIR."/src/classes/".strtolower($pClassName).".class.php";
        if (file_exists($filename)) {
            require_once($filename);
        }
    }
}
spl_autoload_register("\web3\my_autoload");

// $session = getSession();
// $session->log(spl_autoload_functions());

$db = null; # oggetto DB


// if (file_exists(PLUGIN_DIR."/src/customs.inc.php")) {
    // require_once(PLUGIN_DIR."/src/customs.inc.php");
// }


//error_log('Load common;');

if (isset($_GET["saml_diogene_login_token"])) {
    error_log("sento il token");
    $saml_diogene_login_token = $_GET["saml_diogene_login_token"];
    $db = getDB();
    $sql = "SELECT * FROM TerzaMissione.dbo.WEB3_Sessions
            WHERE token=?";
    $rs = $db->Execute($sql, array($saml_diogene_login_token));
    if ($rs->RecordCount() == 1) {
        $row = $rs->GetRow();
        $_SESSION["SSO"] = json_decode($row["sso"], true);
        
        $sql = "UPDATE TerzaMissione.dbo.WEB3_Sessions
                SET deleted=1
                WHERE token=?";
        $rs = $db->Execute($sql, array($saml_diogene_login_token));
        header("Location:".WEB3_URL.BASE_URL);
        exit();
    }
}


$session_id = session_id();
#error_log("session_id 1: ".$session_id);
error_log($_SERVER['REQUEST_URI']);


function assertSSO($returnTo="https://diogene.uniupo.it/terza-missione/sso/status") { # WEB3_URL.BASE_URL."/sso/status"
    $session = getSession();
    if (!$session->checkLogin() && !isset($_SESSION["SSO"])) {
        error_log("NO user, NO SSO");
        # SSO login
        #
        require_once('/var/www/simplesamlphp/lib/_autoload.php');
        $as = new \SimpleSAML\Auth\Simple('default-sp');
        
        $as->requireAuth(array(
            'ReturnTo' => $returnTo
        ));
    }
}

if (!isset($NOSSOLOGIN)) {
    #
    # SSO login
    #
    if (!isset($_SESSION["SSO"])) {
        #error_log("session_id 2: ".session_id());
        error_log("SSO non settato");
        require_once('/var/www/simplesamlphp/lib/_autoload.php');
        $as = new \SimpleSAML\Auth\Simple('default-sp');
        
        error_log("session_id 3 pre: ".session_id(). " ({$session_id})");
        if ($as->isAuthenticated()) {
            // error_log("************************************************************");
            // error_log("session_id($session_id) 4: ".session_id());
            # SimpleSAML rigenera una sessione, così reimposto quella precedente!
            //session_id($session_id);
            
            // $saml_session = \SimpleSAML_Session::getSessionFromRequest();
            // $saml_session->cleanup();
            // session_start();
            // $session = getSession();
            
            error_log("session_id 5: ".session_id());
            $attributes = $as->getAttributes();
            if (isset($attributes['urn:oid:0.9.2342.19200300.100.1.1']))
                $attributes["uid"] = $attributes['urn:oid:0.9.2342.19200300.100.1.1'];
    
            if (count($attributes) > 0)
                $_SESSION["SSO"] = $attributes;
            
            $filename = "/var/www/web3.1-terzamissione/logs/sso/".$_SESSION["SSO"]["uid"][0]."_".date("Ymd-His")."_".md5(microtime()).".txt";
            file_put_contents($filename, var_export($_SESSION["SSO"], true));
            
            if ($_SESSION["SSO"]['uid'][0] == "marcello.trucco") {
                $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.4'] = array(0 => 'Personale');  # studente,Personale
                $_SESSION["SSO"]['uid'] = array(0 => 'mauro.botta'); # alessandro.barbero Alessandro Barbero
                $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.3'] = array(0 => '000093'); #000247
                $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.241'] = array(0 => 'Mauro Botta');
            }
            
            if (isset($_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.3'])) { # employeeNumber (matricola)
                $_SESSION["SSO"]["matricola"] = $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.3'][0];
            }
            
            // $session->log("==========================================================================");
            // $session->log($attributes);
            // error_log(count($attributes));
            // $session->log("==========================================================================");
            // $user = new User();
            // $user->load($attributes['urn:oid:0.9.2342.19200300.100.1.1'][0]); #uid
            
            #$_SESSION['USER'] = serialize($user);
            #print_r($user);
            
            if ($_SERVER['REQUEST_URI'] != '/terza-missione/sso/prod') {
            
            
                $user = new User();
                try {
                    $user->exists($_SESSION["SSO"]['uid'][0]);
                    $user->load($_SESSION["SSO"]['uid'][0]);  
                }
                catch(\Exception $ex) {
                    
                    $uid = $_SESSION["SSO"]['uid'][0];
                    $matricola = $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.3'][0];
                    error_log("Utente non configurato. Matricola={$matricola}, uid={$uid}");
                    
                    $db = getDB();
                    $sql = "SELECT * FROM CSA_PERSONALE
                            WHERE COD_RUOLO IN ('PO', 'PA', 'PF', 'RU', 'RD', 'AR', 'DR')
                            AND (uid=? OR matricola_csa=?)";
                    $rs = $db->Execute($sql, array($uid, $matricola));
                    if ($rs->RecordCount() == 0) {
                        $session = getSession();
                        $session->smarty()->assign("messaggio_extra", "Per informazioni o richieste scrivere a <a href='mailto:helpTM@uniupo.it'>helpTM@uniupo.it</a>");
                        $session->smarty()->display("403.tpl");
                        http_response_code(403);
                        exit();
                    } 
                    
                    
                    $user->set("id", 0);
                    $user->set("user_code", $_SESSION["SSO"]['uid'][0]);
                    $user->set("username", $_SESSION["SSO"]['uid'][0]);
                    $user->set("label", $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.241'][0]);
                    $user->set("nome", $_SESSION["SSO"]['urn:oid:2.5.4.42'][0]);
                    $user->set("cognome", $_SESSION["SSO"]['urn:oid:2.5.4.4'][0]);
                    $user->set("matricola", $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.3'][0]);

                    $actions = array();
                    $actions[] = "DOCENTE";
                    $user->set("actions", $actions);
                    $_SESSION['USER'] = serialize($user);
                    error_log('utente caricato');
                    
                    // header("Location:".PAGE_AFTER_LOGIN);
                    // exit();
                    
                }
                $_SESSION["PERSONALE"] = true;
                // header("Location:".PAGE_AFTER_LOGIN);
                // exit();
            }
        }
        #$session->log($_SESSION);
        #$session->log($attributes);
    }
    else {
        error_log("SSO e' settato");
        if (!isset($_SESSION['USER']) && !isset($_SESSION["STUDENTE"])) {
            error_log("ma non l'utente");
            /*$user = new User();
            try {
                $user->exists($_SESSION["SSO"]['uid'][0]);
                $user->load($_SESSION["SSO"]['uid'][0]);  
            }
            catch(Exception $ex) {
                echo "403 Forbidden!";
                http_response_code(403);
                exit();
            }
            */
            if ($_SERVER['REQUEST_URI'] != '/terza-missione/sso/prod') {
            
            
                $user = new User();
                try {
                    $user->exists($_SESSION["SSO"]['uid'][0]);
                    $user->load($_SESSION["SSO"]['uid'][0]);  
                }
                catch(\Exception $ex) {
                    
                    /* TODO verifico che sia docente */
                    $uid = $_SESSION["SSO"]['uid'][0];
                    $matricola = $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.3'][0];
                    
                    $db = getDB();
                    $sql = "SELECT * FROM CSA_PERSONALE
                            WHERE COD_RUOLO IN ('PO', 'PA', 'PF', 'RU', 'RD', 'AR', 'DR')
                            AND (uid=? OR matricola_csa=?)";
                    $rs = $db->Execute($sql, array($uid, $matricola));
                    if ($rs->RecordCount() == 0) {
                        $session = getSession();
                        $session->smarty()->display("403.tpl");
                        http_response_code(403);
                        exit();
                    } 
                    
                    error_log("imposto l'utente dinamicamente");
                    
                    $user->set("id", 0);
                    $user->set("user_code", $_SESSION["SSO"]['uid'][0]);
                    $user->set("username", $_SESSION["SSO"]['uid'][0]);
                    $user->set("label", $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.241'][0]);
                    $user->set("nome", $_SESSION["SSO"]['urn:oid:2.5.4.42'][0]);
                    $user->set("cognome", $_SESSION["SSO"]['urn:oid:2.5.4.4'][0]);
                    $user->set("matricola", $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.3'][0]);

                    $actions = array();
                    $actions[] = "DOCENTE";
                    $user->set("actions", $actions);
                    $_SESSION['USER'] = serialize($user);
                    
                    // header("Location:".PAGE_AFTER_LOGIN);
                    // exit();
                    
                    
                    //echo "403 Forbidden!";
                    $session = getSession();
                    $session->smarty()->display("403.tpl");
                    http_response_code(403);
                    exit();
                }
                $_SESSION["PERSONALE"] = true;
                // header("Location:".PAGE_AFTER_LOGIN);
                // exit();
            
            }
            
            
            
        }
    }



    #phpinfo();exit();   

    $session = getSession();
}
