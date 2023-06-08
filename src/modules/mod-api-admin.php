<?php
//  
// Copyright (c) ASPIDE.NET. All rights reserved.  
// Licensed under the GPLv3 License. See LICENSE file in the project root for full license information.  
//  
// SPDX-License-Identifier: GPL-3.0-or-later
//

namespace web3;

/*******************************************************************************
*
* UTENTI
*
*******************************************************************************/



#
# Nuovo utente WEB3
#

$this->respond('POST', '/user/new', function ($request, $response, $service, $app) {
    GLOBAL $CONSOLIDAMENTO;
    $session = getSession();
    $db = GetDB();
    $uid = $request->param("uid");
    $result = new Result();
    
    # Check utente esistente
    $sql = "SELECT username
            FROM WEB3_Users
            where username=?";
    $rs = $db->Execute($sql, array($uid));
    if ($rs->RecordCount()>0) {
        $result->setResult(false);
        $result->setCode("KO");
        $result->setDescription("L'utente esiste già.");
        $result->setLevel(Result::ERROR);
        return $result->toJson();
    }
    
    $sql = "SELECT uid, matricola_csa as matricola, cognome, nome
            FROM {$CONSOLIDAMENTO}.CSA_PERSONALE 
            where [uid]=?";
    $rs = $db->Execute($sql, array($uid));
    if ($rs === false || $rs->RecordCount() == 0) {
        $result->setResult(false);
        $result->setCode("KO");
        $result->setDescription("Utente inesistente.");
        $result->setLevel(Result::ERROR);
        return $result->toJson();
    }
    
    $row = $rs->GetRow();
    
    $pwd_hash = password_hash("wcsita!!", PASSWORD_DEFAULT);
    
    try {
        $db->StartTrans();
    
        $sql = "INSERT INTO WEB3_Users(user_code, username, password, flag_active)
                VALUES(?, ?, ?, ?)";
        $db->Execute($sql, array($uid, $uid, $pwd_hash, 'S'));
        
        $sql = "INSERT INTO WEB3_Registries(registry_code, registry_type, person_surname, person_name)
                VALUES(?, ?, ?, ?)";
        $db->Execute($sql, array($row["matricola"], 'PERFIS', $row["cognome"], $row["nome"]));
        
        $sql = "INSERT INTO WEB3_RelUserRegistry(relation_code, master_code, slave_code, type_code, date_start, date_end)
                VALUES(?, ?, ?, ?, CONVERT(DATETIME, '2000-01-01', 102), CONVERT(DATETIME, '2100-12-31', 102))";
        $db->Execute($sql, array(md5(microtime()), $uid, $row["matricola"], 'NAME'));
    
    }
    catch(Result $ex) {
        $db->FailTrans();
        throw $ex;
    }
    finally {
        $db->CompleteTrans();
    }
    
    $result->setResult(true);
    $result->setCode("OK");
    $result->setDescription("Utente inserito con successo.");
    $result->setLevel(Result::INFO);
    return $result->toJson();
});




#
# PERSONALE
#

$this->respond('GET', '/personale/?[:query]?', function ($request, $response, $service, $app) {
    GLOBAL $DBMODULI;
    $session = getSession();
    $db = getDB();

    # PERSONALE
    $sql = "SELECT uid as value
            ,([COGNOME]+' '+[NOME]+' - '+ [RUOLO DESCR] + ' - SSD: ' + [COD_SSD]) as name -- ,[DES_SSD]
            , COD_SSD as ssd, [CODICE UO DI APPARTENENZA] as struttura
            FROM CSA_PERSONALE
            WHERE (cognome+' '+nome) LIKE ? OR (nome+' '+cognome) LIKE ? OR matricola_csa LIKE ? 
            ORDER BY cognome, nome";
    $rs = $db->Execute($sql, array("%".$request->query."%", "%".$request->query."%", "%".$request->query."%"));
    $personale = array();
    if ($rs) 
        $personale = $rs->GetArray();
    $result = array(
        "success" => true,
        "results" => $personale
    );
    ob_clean();
    header('Content-Type: application/json');
    echo_json($result);
    exit();
});


#
# INFO UTENTE UNIGE 
#
$this->respond('GET', '/html/[info:action]/users/[:uid]', function ($request, $response, $service, $app) {
    GLOBAL $CONSOLIDAMENTO;
    $session = getSession();
    $db = GetDB();
    $uid = $request->uid;
    
    $sql = "SELECT uid, matricola_csa as matricola, cognome, nome, mail as email, DES_AFF_ORG as decostruttura
            FROM CSA_PERSONALE
            where [uid]=?";
    $rs = $db->Execute($sql, array($uid));
    $row = $rs->GetRow();
    $session->smarty()->assign("info", $row);
    $session->smarty()->display("info-user-uniupo.tpl");
    exit();
});
