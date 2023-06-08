<?php

namespace web3;


$this->respond(array('GET', 'POST'), '*', function ($request, $response, $service, $app) {
    // $session = getSession();
    // $session->assertAdmin();
    //assertSSO();
});

#
# INDEX
#

$this->respond('GET', '/?', function ($request, $response, $service, $app) {
    assertSSO();
    // echo session_id();
    // echo "SSO INDEX<br>";
    // print_r($_SESSION);
});

#
# SSO STATUS
#

$this->respond('GET', '/status', function ($request, $response, $service, $app) {
    GLOBAL $session_id;
    $db = getDB();
    $studente = $personale = false;
    #echo "Sessio ID: {$session_id}<br>";
    if (isset($_SESSION["SSO"])) {
        // echo "SSO OK<br>";
        // print_r($_SESSION["SSO"]);
        // $filename = "/var/www/files/sso/".$_SESSION["SSO"]["uid"][0]."_".date("Ymd-His")."_".md5(microtime()).".txt";
        // file_put_contents($filename, var_export($_SESSION["SSO"], true));
    }
    else
        echo "SSO NOT SET<br>";
    //print_r($_SESSION);
    
    if (!isset($_SESSION["SSO"]))
        return;
    //exit();
    
    /* Eccezione di Marcello */
    if ($_SESSION["SSO"]['uid'][0] == "marcello.trucco") {
        $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.4'] = array(0 => 'Personale');  # studente,Personale
        $_SESSION["SSO"]['uid'] = array(0 => 'mauro.botta'); # alessandro.barbero Alessandro Barbero
        $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.3'] = array(0 => '000093'); #000247
        $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.241'] = array(0 => 'Mauro Botta');
    }
    
    if (isset($_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.3'])) { # employeeNumber (matricola)
        $_SESSION["SSO"]["matricola"] = $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.3'][0];
    }
    
    if (isset($_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.4'])) { # employeeType
        $employeeTypes = $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.4'];
        foreach($employeeTypes as $item) {
            if (strtolower($item) == "studente") 
                $studente = true;
            if (strtolower($item) == "personale") 
                $personale = true;
        }
    }
    
    if (isset($_SESSION["SSO"]['urn:oid:1.3.6.1.4.1.5923.1.1.1.1'])) { # eduPersonAffiliation
        $eduPersonAffiliation = $_SESSION["SSO"]['urn:oid:1.3.6.1.4.1.5923.1.1.1.1'];
        $member = $staff = false;
        foreach($eduPersonAffiliation as $item) {
            if (strtolower($item) == "member") 
                $member = true;
            if (strtolower($item) == "staff") 
                $staff = true;
        }
        if ($member && $staff)
            $personale = true;
    }
    
    if ($studente) {
        $session = getSession();
        $session->smarty()->display("403.tpl");
        http_response_code(403);
        exit();
    }
    
    if ($personale) {
        $user = new User();
        try {
            $user->exists($_SESSION["SSO"]['uid'][0]);
            $user->load($_SESSION["SSO"]['uid'][0]);  
        }
        catch(\Exception $ex) {
            
            
            
            
            
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
            
            header("Location:".BASE_URL);
            exit();
            
            
            //echo "403 Forbidden!";
            $session = getSession();
            $session->smarty()->display("403.tpl");
            http_response_code(403);
            exit();
        }
        if ($user->has("ADMINSIMNOVA"))
            $_SESSION["GESTIONALE"] = true;
        elseif ($user->has("OPERATORESIMNOVA"))
            $_SESSION["GESTIONALE"] = true;
        else
            $_SESSION["PERSONALE"] = true;
        header("Location:".BASE_URL);
        exit();
    }
    
    echo "Ruolo non riconosciuto, contattare gli amministratori del servizio.";
    exit();
        
    // if (isset($_SESSION["SSO"])) {
        // print_r($_SESSION["SSO"]['urn:oid:1.3.6.1.4.1.5923.1.1.1.1']);
        // print_r($_SESSION["SSO"]);
    // }
});



#
# SSO STATUS
#

$this->respond('GET', '/prod', function ($request, $response, $service, $app) {
    GLOBAL $session_id;
    $db = getDB();
    $studente = $personale = false;
    #echo "Sessio ID: {$session_id}<br>";
    if (isset($_SESSION["SSO"])) {
        #echo "SSO OK<br>";
        #print_r($_SESSION["SSO"]);
        // $filename = "/var/www/files/sso/".$_SESSION["SSO"]["uid"][0]."_".date("Ymd-His")."_".md5(microtime()).".txt";
        // file_put_contents($filename, var_export($_SESSION["SSO"], true));
    }
    // else
        // echo "SSO NOT SET<br>";
    
    if (!isset($_SESSION["SSO"]))
        return;
	
	// $filename = "/var/www/web3.1-terzamissione/logs/sso/".$_SESSION["SSO"]["uid"][0]."_".date("Ymd-His")."_".md5(microtime()).".txt";
    // file_put_contents($filename, var_export($_SESSION["SSO"], true));
    
    /* Eccezione di Marcello */
    if ($_SESSION["SSO"]['uid'][0] == "marcello.trucco") {
        // $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.4'] = array(0 => 'Personale'); # studente,Personale
        // $_SESSION["SSO"]['uid'] = array(0 => 'mauro.botta'); # alessandro.barbero
        // $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.3'] = array(0 => '000093'); #000247 Alessandro Barbero
        // $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.241'] = array(0 => 'Mauro Botta');
        
        // $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.4'] = array(0 => 'Personale'); # studente,Personale
        // $_SESSION["SSO"]['uid'] = array(0 => 'fiorella.croce'); # alessandro.barbero
        // $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.3'] = array(0 => '004837'); #000247 Alessandro Barbero
        // $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.241'] = array(0 => 'Fiorella Croce');
    }
    
    if (isset($_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.3'])) { # employeeNumber (matricola)
        $_SESSION["SSO"]["matricola"] = $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.3'][0];
    }
    
    if (isset($_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.4'])) { # employeeType
        $employeeTypes = $_SESSION["SSO"]['urn:oid:2.16.840.1.113730.3.1.4'];
        foreach($employeeTypes as $item) {
            if (strtolower($item) == "studente") 
                $studente = true;
            if (strtolower($item) == "personale") 
                $personale = true;
        }
    }
    
    if (isset($_SESSION["SSO"]['urn:oid:1.3.6.1.4.1.5923.1.1.1.1'])) { # eduPersonAffiliation
        $eduPersonAffiliation = $_SESSION["SSO"]['urn:oid:1.3.6.1.4.1.5923.1.1.1.1'];
        $member = $staff = false;
        foreach($eduPersonAffiliation as $item) {
            if (strtolower($item) == "member") 
                $member = true;
            if (strtolower($item) == "staff") 
                $staff = true;
        }
        if ($member && $staff)
            $personale = true;
    }
    
    if ($studente) {
        $session = getSession();
        $session->smarty()->display("403.tpl");
        http_response_code(403);
        exit();
    }
    
    if ($personale) {
        $token = md5(microtime());
        $sql = "INSERT INTO TerzaMissione.dbo.WEB3_Sessions
                   ([token]
                   ,[sso]
                   ,[uid])
                VALUES
                   (?, ?, ?)";
        $rs = $db->Execute($sql, array($token, json_encode($_SESSION["SSO"]), $_SESSION["SSO"]['uid'][0]));
                   
        header("Location:https://terzamissione.uniupo.it/?saml_diogene_login_token={$token}");
        exit();
    }
    
    echo "Ruolo non riconosciuto, contattare gli amministratori del servizio.";
    exit();
        
    // if (isset($_SESSION["SSO"])) {
        // print_r($_SESSION["SSO"]['urn:oid:1.3.6.1.4.1.5923.1.1.1.1']);
        // print_r($_SESSION["SSO"]);
    // }
});