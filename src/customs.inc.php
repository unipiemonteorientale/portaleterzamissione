<?php
//  
// Copyright (c) ASPIDE.NET. All rights reserved.  
// Licensed under the GPLv3 License. See LICENSE file in the project root for full license information.  
//  
// SPDX-License-Identifier: GPL-3.0-or-later
//

namespace web3;

#------------------------------------------------------------------------------
# CONSTANTS WEB3
#------------------------------------------------------------------------------
define("LOCALE", "it_IT.UTF-8");
define("BASE_NAME", "");
define("WEB3_DIR", "/var/www/web3.2");
define("BASE_DIR", "/var/www/web3.1-terzamissione");

define("DB_TYPE", "mssqlnative");
define("DB_HOST_SERVER", ""); 
define("DB_USERNAME", ""); 
define("DB_PASSWORD", ""); 
define("DB_NAME", "terzamissione");

define("APPNAME", "Terza Missione");

define("SECRET", '');
define("WEB3_URL", "https://".$_SERVER["SERVER_NAME"]);
define("API_URL", "/api");
define("SYNC_URL", ""); #"https://web3.aspide.net");
define("STATIC_URL", "/static/upo");
define("BASE_URL", "/terza-missione");

define("LOG_DIR", BASE_DIR."/logs");
define("STATIC_DIR", BASE_DIR."/html/static");
define("FILES_DIR", "/var/www/files");

define("INDEX", "");
define("TEST", false);
define("RIBBON", "");
define("EMAIL_SUPPORT", "");
define("PROXY", "");

define("MONEY_DECIMAL", 2);
define("MONEY_SIGN", "EUR");


define("MAINTENANCE", false);

// FILTRI DINAMICI
define("FILTRO_UTENTE", "{USERNAME}");
define("FILTRO_MATRICOLA", "{MATRICOLA}");
define("FILTRO_GRUPPO", "{GRUPPO}");
define("FILTRO_ESERCIZIO", "{ESERCIZIO}");

$modules = array(
    "api" => [
        "form",
        "json"
    ],
    // 'login',
    // 'sys',
    // #'static',
    // 'json',
    // 'list',
    // 'modal',
    'admin' => [
        "web3-trees",
        "web3-objects",
        "analisi"
    ],
    // 'wizard',
    // 'navigazione',
    'webonline',
    "files",
    "ui"
);

#------------------------------------------------------------------------------
# CONSTANTS TERZA MISSIONE
#------------------------------------------------------------------------------

define("PAGE_AFTER_LOGIN", BASE_URL."/home");

$custom_modules = array(
    'archivio' => array(
        "wizard"
    ),
    'api' => array(
        "admin", "wizard", "archivio"
    ),
    'sso',
    'faq',
    'documentazione',
	'configurazione',
    'reportistica'
);


error_reporting(E_ERROR | E_PARSE);