<?php
//  
// Copyright (c) ASPIDE.NET. All rights reserved.  
// Licensed under the GPLv3 License. See LICENSE file in the project root for full license information.  
//  
// SPDX-License-Identifier: GPL-3.0-or-later
//

namespace web3;

ob_start();
session_start();


require_once("../src/common.inc.php");

try {
    
    $klein = new \Klein\Klein();

    // Using range behaviors via if/else
    // $klein->onHttpError(function ($code, $router) {
        // $session = getSession();
        
        // if ($code >= 400 && $code < 500) {
            // $session->smarty->display("404.tpl"); 
            // exit();
        // } 
        // elseif ($code >= 500 && $code <= 599) {
            // error_log('uhhh, something bad happened');
            // $session->smarty->display("500.tpl"); 
        // }
    // });
   
    $klein->onError(function ($router, $message, $type, $exception) {
        // error_log("onError");
        // #echo "<br>URI: ".$router->;
        // #print_r($router);
        // echo "<br>Message: ".$message;
        // echo "<br>type: ".$type;
        // echo "<br>EX: ".get_class($exception);
        
        /*
        DEBUG - detailed debug information
        INFO - interesting events
        NOTICE - normal but significant events
        WARNING - exceptional occurrences that are not errors
        ERROR - runtime errors that do not require immediate action
        CRITICAL - critical conditions
        ALERT - events for which action must be taken immediately
        EMERGENCY - emergency events
        */
        
        // $log = new \Monolog\Logger('');  # Channel name
        // $log->pushHandler(new \Monolog\Handler\StreamHandler(LOG_DIR.'/myapp.log', \Monolog\Logger::DEBUG));    # DEBUG, INFO, WARNING, ERROR
        // $log->warning('primo messaggio');
        // $log->info('This is a log! ^_^ ');
        // $log->warning('This is a log warning! ^_^ ');
        // $log->error('This is a log error! ^_^ ', ['username' => "sconosciuto"]);
        
        $session = getSession();
        // if (get_class($exception) == "web3\\Result")
            // throw $exception;
            
        $format = $session->get("RESPONSE-FORMAT");
        error_log("onError -> ".$format);
        switch($format) {
            case "JSON":
                throw new JsonException($exception);
                break;
                
            case "MODAL": # TODO
            case "INNERHTML": # TODO
            case "HTML":
            default:
                throw $exception;
                break;
        }
    });
    
    
    

    #error_log("INDEX: ".$_SERVER['REQUEST_URI']);
    
    # ROOT / INDEX
	if (file_exists(BASE_DIR."/src/modules/mod-root.php"))
		$klein->with(BASE_URL, BASE_DIR."/src/modules/mod-root.php");
    if (file_exists(WEB3_DIR."/src/modules/mod-root.php"))
		$klein->with(BASE_URL, WEB3_DIR."/src/modules/mod-root.php");

    # CUSTOM MODULES
    if (isset($custom_modules)) {
        foreach($custom_modules as $key =>$modulo) {
            if (is_array($modulo)) {
                $klein->with(BASE_URL."/{$key}", BASE_DIR."/src/modules/mod-{$key}.php");
                foreach($modulo as $submodulo) {
                    $klein->with(BASE_URL."/{$key}/{$submodulo}", BASE_DIR."/src/modules/mod-{$key}-{$submodulo}.php");
                }
            }
            else {
                $klein->with(BASE_URL."/{$modulo}", BASE_DIR."/src/modules/mod-{$modulo}.php");
            }
        }
    }

    # WEB3 STANDARD MODULES
    foreach($modules as $key =>$modulo) {
        if (is_array($modulo)) {
            $klein->with(BASE_URL."/{$key}", WEB3_DIR."/src/modules/mod-{$key}.php");
            foreach($modulo as $submodulo) {
                $klein->with(BASE_URL."/{$key}/{$submodulo}", WEB3_DIR."/src/modules/mod-{$key}-{$submodulo}.php");
            }
        }
        else {
            $klein->with(BASE_URL."/{$modulo}", WEB3_DIR."/src/modules/mod-{$modulo}.php");
        }
    }

    #error_log("ora faccio il dispatch");
    $klein->dispatch();
    #error_log("dispatch fatto");
}
/*catch(Result $ex) {
    echo $ex->toJson();
    exit();
    // echo "JSON! ".$ex->getMessage()."<br>";
    // echo $ex->getTraceAsString();
}*/
catch(JsonException $ex) {
    error_log($ex->getMessage());
    $result = new Result();
    $result->setResult(false);
    $result->setCode("KO");
    $result->setDescription("Errore inaspettato!");
    $result->setLevel(Result::ERROR);
    echo $result->toJson();
    exit();
    // echo "JSON! ".$ex->getMessage()."<br>";
    // echo $ex->getTraceAsString();
}
catch(\Exception $ex) {
    error_log('errore 500');
    $session = getSession();
    $session->log($ex->getMessage());
    $session->log($ex->getTraceAsString());
    $session->smarty->display("500.tpl"); 
    exit();
}
