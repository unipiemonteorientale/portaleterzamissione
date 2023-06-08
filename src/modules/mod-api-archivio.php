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


# FILTRI
$this->respond('POST', "/filter", function ($request, $response, $service, $app) {
    $session = getSession();
	
    if (isset($_SESSION["M3FILTER"]))
        unset($_SESSION["M3FILTER"]);
	$_SESSION["M3FILTER"] = $request->params();
	
	/*
	[anno] => 2022
    [campo_azione] => g
    [struttura] => Array
        (
            [0] => AP_5_R2
        )

    [matricola] => 002788
    [AP_9_supplement1] => a
    [AP_9_supplement2] => b
    [AP_9_supplement3] => c
    [AP_9_supplement4] => d
    [AP_9_supplement5] => e
    [agenda2030] => Array
        (
            [0] => AP_7_R1
        )

    [ssd] => Array
        (
            [0] => BIO/01
        )

    [categoria_pe] => Array
        (
            [0] => G_6_R1
        )
	*/
     
    //print_r($request->params());
});


# FILTRI
$this->respond('POST', "/filter/[:filter_name]", function ($request, $response, $service, $app) {
    $session = getSession();
    // $session->log($request->params());
    // $session->log($_POST);
	$nome_filtro = $request->filter_name;
    $valore_filtro = $request->param('filter_value', '');
    
    if (strlen($valore_filtro))
        $_SESSION["M3FILTER"][$nome_filtro] = $valore_filtro;
    else
        unset($_SESSION["M3FILTER"][$nome_filtro]);

    exit('OK');
});