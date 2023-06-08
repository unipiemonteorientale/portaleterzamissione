<?php
//  
// Copyright (c) ASPIDE.NET. All rights reserved.  
// Licensed under the GPLv3 License. See LICENSE file in the project root for full license information.  
//  
// SPDX-License-Identifier: GPL-3.0-or-later
//

namespace web3;

$this->respond(array('GET', 'POST'), '*', function ($request, $response, $service, $app) {
    $session = getSession();
    $session->assertLogin();
});

#
# PERSONALE
#

$this->respond('GET', '/personale/?[:query]?', function ($request, $response, $service, $app) {
    GLOBAL $DBMODULI;
    $session = getSession();
    $db = getDB();

    # PERSONALE
    $sql = "SELECT [MATRICOLA_CSA] as value
            ,([COGNOME]+' '+[NOME]+' - '+ [RUOLO DESCR] + ' - SSD: ' + [COD_SSD]) as name -- ,[DES_SSD]
            , COD_SSD as ssd, [CODICE UO DI APPARTENENZA] as struttura
            FROM CSA_PERSONALE
            WHERE cognome LIKE ? OR matricola_csa LIKE ? 
            ORDER BY cognome, nome";
    $rs = $db->Execute($sql, array("%".$request->query."%", "%".$request->query."%"));
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
# M3 Coinvolgimento docente x campo d'azione
#

$this->respond('GET', '/coinvolgimento-docente/[:attivita]', function ($request, $response, $service, $app) {
    GLOBAL $DBMODULI;
    $session = getSession();
    $db = getDB();
    

    # ATTIVITA
    $sql = "SELECT campo_azione
            FROM MISSIONE3_Attivita
            WHERE code=?";
    $rs = $db->Execute($sql, array($request->attivita));
    if ($rs->RecordCount() == 0) {
        http_response_code(404);
        exit();
    }
    $attivita = $rs->GetRow();
    $campo_azione = $attivita["campo_azione"];
    

    # VOCI
    $sql = "SELECT code as value, label as name
            FROM MISSIONE3_CONF_TipoCoinvolgimento
            WHERE sezioni LIKE ?
            ORDER BY code";
    $rs = $db->Execute($sql, array("%".$campo_azione."%"));
    if ($rs) 
        $voci = $rs->GetArray();
    $result = array(
        "success" => true,
        "results" => $voci
    );
    ob_clean();
    header('Content-Type: application/json');
    echo_json($result);
    exit();
});


#
# Annulla validazione: attività nuovamente in bozza
#

$this->respond('GET', '/annulla-validazione/[:attivita]', function ($request, $response, $service, $app) {
    GLOBAL $DBMODULI;
    $session = getSession();
    $db = getDB();
    $operatore = $session->user();
    if (!$operatore->has("ATENEO") && !$operatore->has("DIPARTIMENTO")) {
        http_response_code(403);
        exit('KO');
    }

    # ATTIVITA
    $sql = "UPDATE MISSIONE3_Attivita SET stato='B'
            WHERE stato='V' AND code=?";
    $rs = $db->Execute($sql, array($request->attivita));

    exit('OK');
});
