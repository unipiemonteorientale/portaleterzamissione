<?php
//  
// Copyright (c) ASPIDE.NET. All rights reserved.  
// Licensed under the GPLv3 License. See LICENSE file in the project root for full license information.  
//  
// SPDX-License-Identifier: GPL-3.0-or-later
//

namespace web3;


# MODAL WIZARD NEW
$this->respond('GET', "/[insert|update|read:action]", function ($request, $response, $service, $app) {
    $session = getSession();
    
    // TODO
    // costruire l'ui object
    // ricavare le RULES
    
    $header_text = "Nuova attività <span id='header_tipo_campo_azione'></span>";
    if (in_array($request->action, array("update", "read"))) {
        $visibility = "";
        if ($request->action == "read")
            $visibility = "invisible";
        
        $header_text = <<<EOT
<center>
<div class="ui ordered mini steps" id="wizard_steps">
  <a class="active step" onclick="loadWizardPage(1, true);" id="wizard_step1">
    <!--i class="truck icon"></i-->
    <div class="content">
      <div class="title">Campo d'azione</div>
    </div>
  </a>
  <a class="step" onclick="loadWizardPage(2, true);" id="wizard_step2">
    <div class="content">
      <div class="title">Titolo</div>
    </div>
  </a>
  <a class="step" onclick="loadWizardPage(3, true);" id="wizard_step3">
    <div class="content">
      <div class="title">Domande di apertura</div>
    </div>
  </a>
  <a class="step" onclick="loadWizardPage(4, true);" id="wizard_step4">
    <div class="content">
      <div class="title">Domande caratteristiche</div>
    </div>
  </a>
  <a class="step" onclick="loadWizardPage(5, true);" id="wizard_step5">
    <div class="content">
      <div class="title">Domande di chiusura</div>
    </div>
  </a>
  <a class="step {$visibility}" onclick="loadWizardPage(6, true);" id="wizard_step6">
    <div class="content">
      <div class="title">Validazione</div>
    </div>
  </a>
</div> 
</center>   
EOT;
    }
    
    $session->smarty()->assign("header", $header_text);
    $session->smarty()->assign("modal_id", md5(microtime()));
    $session->smarty()->assign("action_name", $request->action);
    $session->smarty()->assign("record_code", $request->param("record_code", md5(microtime())));
    $session->smarty()->assign("context_name", "default");
    $session->smarty()->assign("object_name", "M3_ATTIVITA");
    $session->smarty()->assign("step", $request->param("step", "1"));
    $session->smarty->display("archivio-modal-wizard-container.tpl");
    exit();
});


# INNER WIZARD NEW - STEPS
$this->respond('GET', "/[insert|update|read:action_name]/step-[i:step]", function ($request, $response, $service, $app) {
    $session = getSession();
    $step = $request->step;
    $action_name = $request->action_name;
    $session->log($action_name.$step);
    $db = getDB();
    
    # Dati basilari attività
    if ($step == '1') {
        $context_name = $request->param("context_name", "default");
        $action_name = $request->param("action_name");
        $object_name = $request->param("object_name", "M3_ATTIVITA");
        $record_code = $request->param("record_code", false);
        $style = $request->param("style", "");
        $options = array(
            "context_name" => $context_name,
            "object_name" => $object_name,
            "action_name" => $action_name,
            "record_code" => $record_code,
            "hidden" => array("titolo", "tipo_compilazione", "docente_matricola", "docente_profilo")
        );

        $uiobj = new UIObject();
        $uiobj->init($context_name, $object_name, $action_name, $record_code);
        $class = $uiobj->getClass();
        if (strlen($class) > 0) {
            $uiobj = new $class();
            $uiobj->init($context_name, $object_name, $action_name, $record_code);
        }
        $_SESSION["CURRENT_YEAR"] = $uiobj->get("anno");
        $uiobj->set(array("style" => $style, "code" => $record_code));
        $uiobj->display($options);
        exit();
    }
    # campi di apertura
    elseif ($step == '2') {
        $context_name = $request->param("context_name", "default");
        if ($action_name == "insert")
            $action_name = "update";
        $object_name = $request->param("object_name", "M3_ATTIVITA");
        $record_code = $request->param("record_code", false);
        $style = $request->param("style", "");
        $options = array(
            "context_name" => $context_name,
            "object_name" => $object_name,
            "action_name" => $action_name,
            "record_code" => $record_code,
            "hidden" => array("anno", "campo_azione", "docente_matricola", "docente_profilo", "ti")
        );

        $uiobj = new UIObject();
        $uiobj->init($context_name, $object_name, $action_name, $record_code);
        $class = $uiobj->getClass();
        if (strlen($class) > 0) {
            $uiobj = new $class();
            $uiobj->init($context_name, $object_name, $action_name, $record_code);
        }
        $uiobj->set(array("style" => $style));
        $uiobj->display($options);
        exit();
    }
    
    # campi di apertura
    elseif (in_array($step, array('3', '4', '5'))) {
        $object_name = "M3_DOMANDE";
        $action_name = $request->param("action_name");
        $record_code = $request->param("record_code", false);
        $style = $request->param("style", "");
        
        if ($step == '3')
            $context_name = "AP";
        if ($step == '4') {
            $sql = "SELECT * FROM MISSIONE3_Attivita WHERE code=?";
            $rs = $db->Execute($sql, array($record_code));
            $context_name = strtoupper($rs->Fields("campo_azione"));
        }
        if ($step == '5')
            $context_name = "CH";
        $options = array(
            "context_name" => $context_name,
            "object_name" => $object_name,
            "action_name" => $action_name,
            "record_code" => $record_code,
            "style" => $style
        );

        $uiobj = new UIObject();
        $uiobj->init($context_name, $object_name, $action_name, $record_code);
        $class = $uiobj->getClass();
        if (strlen($class) > 0) 
            if (class_exists($class)) {
                $uiobj = new $class();
                $uiobj->init($context_name, $object_name, $action_name, $record_code);
            }
        
        $uiobj->set(array("style" => $style));
        $uiobj->display($options);
        exit();
    }
    
    $record_code = $request->param("record_code", false);
    $sql = "SELECT * FROM MISSIONE3_Attivita WHERE code=?";
    $rs = $db->Execute($sql, array($record_code));
    $tipo_compilazione = strtoupper($rs->Fields("tipo_compilazione"));
    
    $session->smarty()->assign("action_name", $request->action_name);
    $session->smarty()->assign("step", $step);
    $session->smarty()->assign("tipo_compilazione", $tipo_compilazione);
    $session->smarty()->assign("record_code", $record_code);
    $session->smarty->display("archivio-inner-wizard-step99.tpl");
    exit();
});


# INNER WIZARD NEW - STEPS
$this->respond('POST', "/[validate:action_name]", function ($request, $response, $service, $app) {
    $session = getSession();
    $db = getDB();
    $object_name = "M3_DOMANDE";
    $action_name = $request->param("action_name");
    $record_code = $request->param("record_code", false);
    
    $sql = "SELECT * FROM MISSIONE3_Attivita WHERE code=?";
    $rs = $db->Execute($sql, array($record_code));
    $attivita = $rs->GetRow();
    $campo_azione = strtoupper($attivita["campo_azione"]);
    $contexts = array('AP', $campo_azione, 'CH');
    $res = true;
    $risultati = array();
    
    $risposta_ok = true;
    if (strlen($attivita["anno"]) == 0) {
        $risposta_ok = false;
        $res = false;
    }
    $risultati[] = array(
        "codice_domanda" => "-",
        "testo_domanda" => "Anno",
        "obbligatoria" => true,
        "risposta_ok" => $risposta_ok,
    );
    
    $risposta_ok = true;
    if (strlen($attivita["campo_azione"]) == 0) {
        $risposta_ok = false;
        $res = false;
    }
    $risultati[] = array(
        "codice_domanda" => "-",
        "testo_domanda" => "Campo d'azione",
        "obbligatoria" => true,
        "risposta_ok" => $risposta_ok,
    );
    
    $risposta_ok = true;
    if (strlen($attivita["tipo_compilazione"]) == 0) {
        $risposta_ok = false;
        $res = false;
    }
    $risultati[] = array(
        "codice_domanda" => "-",
        "testo_domanda" => "Tipo compilazione",
        "obbligatoria" => true,
        "risposta_ok" => $risposta_ok,
    );
    
    $risposta_ok = true;
    if (strlen($attivita["titolo"]) == 0) {
        $risposta_ok = false;
        $res = false;
    }
    $risultati[] = array(
        "codice_domanda" => "-",
        "testo_domanda" => "Titolo",
        "obbligatoria" => true,
        "risposta_ok" => $risposta_ok,
    );
    
    if (!$res) {
        $step_sezione = 1; 
        $session->smarty()->assign("step_sezione", $step_sezione);
        $session->smarty()->assign("sezione", "Campo d'azione e titolo");
        $session->smarty()->assign("risultati", $risultati);
        $session->smarty->display("ui-domanda-validazione.tpl");
    }
    
    foreach($contexts as $context_name) {
        $options = array(
            "context_name" => $context_name,
            "object_name" => $object_name,
            "action_name" => $action_name,
            "record_code" => $record_code
        );

        $uiobj = new UIObject();
        $uiobj->init($context_name, $object_name, $action_name, $record_code);
        $class = $uiobj->getClass();
        if (strlen($class) > 0) 
            if (class_exists($class)) {
                $uiobj = new $class();
                $uiobj->init($context_name, $object_name, $action_name, $record_code);
            }
        
        $res &= $uiobj->validate($options);
    }
    if ($res == true) {
        $sql = "UPDATE MISSIONE3_Attivita SET stato='V' WHERE stato='B' AND code=?";
        $db->Execute($sql, array($record_code));
        exit('OK');
    }
    exit();
});



# DISEGNA SINGOLA DOMANDA
$this->respond('GET', "/[insert|update|read:action_name]/domanda-[:codice_domanda]", function ($request, $response, $service, $app) {
    $session = getSession();
    $object_name = "M3_DOMANDE";
    $action_name = $request->action_name;
    $codice_domanda = $request->codice_domanda;
    $x = explode("_", $codice_domanda);
    $context_name = $x[0];
    $record_code = $request->param("record_code", false);
    
    $options = array(
        "context_name" => $context_name,
        "object_name" => $object_name,
        "action_name" => $action_name,
        "record_code" => $record_code,
        "SOLO_UNICA_DOMANDA" => '1',
        "codice_unica_domanda" => $codice_domanda
    );
    
    $domande = new M3Domande();
    $domande->init($context_name, $object_name, $action_name, $record_code);
    $html = $domande->display($options);
    echo $html;
});



# FORM POST UI
$this->respond('POST', "/ui/[:context_name]/[:object_name]/[read|insert|update|delete|clone:action_name]", function ($request, $response, $service, $app) {
    $session = getSession();
    $session->log("POST WIZARD UI FORM");
    $result = new Result();
    $db = getDB();
    
    $context_name = $request->context_name;
    $object_name = $request->object_name;
    $action_name = $request->action_name;
    $record_code = $request->param("code_attivita", $request->param("record_code", $request->param("code", false)));
    // error_log("code_attivita = ".$request->param("code_attivita"));
    // error_log("record_code = ".$request->param("record_code"));
    // error_log("record_code = ".$record_code);
    $delete = $request->delete;
    $step = $request->param("step", '1');
    $ultimo_step = '5';
    
    if ($step == '3') {
        $context_name = "AP";
        $object_name = "M3_DOMANDE";
    }  
    if ($step == '4') {
        $context_name = "G";
        $object_name = "M3_DOMANDE";
    }   
    if ($step == '5') {
        $context_name = "CH";
        $object_name = "M3_DOMANDE";
    }  
    if ($step != '1') {
        $action_name = "update";
    }
    if (($step == '1' || $step == '4') && strlen($record_code)) {
        $sql = "SELECT * FROM MISSIONE3_Attivita WHERE code=?";
        $rs = $db->Execute($sql, array($record_code));
        if ($rs->RecordCount()) {
            $action_name = "update";
            $context_name = strtoupper($rs->Fields("campo_azione"));
        }
        else
            $action_name = "insert";
    }
    
    $options = array(
        "context_name" => $context_name,
        "object_name" => $object_name,
        "action_name" => $action_name,
        "delete" => $delete,
        "record_code" => $record_code,
        #"code" => $record_code  # code non va bene perché fa casino con il campo 'code' delle singole domande
    );
    // $session->log($options);
    // $session->log("Request params");
    // $session->log($_POST);
    
    if ($action_name == "insert") {
        $options["ui"] = $session->user()->username();
        #$options["ti"] = "getdate()";
    }
    elseif ($action_name == "update") {
        // $options["uu"] = $session->user()->username();
        // $options["tu"] = "getdate()";
    }
    
    $uiobj = new UIObject();
    $uiobj->init($context_name, $object_name, $action_name, $record_code);
    
    if (strlen($record_code) == 0) {
        $model_key_name = $uiobj->getDBKey();#$request->param("model_key_name");
        // $session->log("model_key_name = ".$model_key_name);
        $record_code = $model_key_value = $request->param($model_key_name);
        // $session->log("Record code = ".$record_code);
        // exit('KO');
        $options["record_code"] = $record_code;
    }
    
    $class = $uiobj->getClass();
    #$session->log("class = ".$class);
    if (strlen($class) > 0) {
        $uiobj = new $class($context_name, $object_name, $action_name, $record_code);
        $uiobj->init($context_name, $object_name, $action_name, $record_code);
    }
    
    $options = array_merge($request->params(), $options); # options dopo perché deve sovrascrivere eventualmente action_name.
    
    
    // $session->log("#");
    // $session->log("# INPUT =============================================================================");
    // $session->log("#");
    // $session->log($options);
    
    $uiobj->input($options);
    
    # Check
    if ($step == '1') {
        if (strlen($uiobj->get("anno")) == 0) {
            throw new Result(false, "KO", "L'anno è obbligatorio ma non valorizzato.", Result::ERROR);
        }
        else
            $_SESSION["CURRENT_YEAR"] = $uiobj->get("anno");
    }
    
    // $session->log("#");
    // $session->log("# STORE =============================================================================");
    // $session->log("#");
    $uiobj->store($options);
    
    $sql = "UPDATE MISSIONE3_Attivita SET uu=?, tu=getdate() WHERE code=?";
    $rs = $db->Execute($sql, array($session->user()->username(), $record_code));
    
    if ($step == $ultimo_step)
        exit('FINE');
    elseif ($step == '1' && $action_name == 'insert') {
        $result->setResult(true);
        $result->setCode("OK");
        //$result->setDescription("Dati salvati, proseguiamo con lo step successivo.");
        $result->setLevel(Result::INFO);
        $result->setCustoms(array(
            "record_code" => $uiobj->get("code"),
            "campo_azione" => $request->campo_azione
        ));
        $result->toJson();
        exit();
    }
    else
        exit('OK');
});    
    
    
    
# MODAL HELP
$this->respond('GET', "/help/[:domanda]/?[:sezione]?", function ($request, $response, $service, $app) {
    $session = getSession();
    $db = getDB();
    
    $domanda = $request->domanda;
    $sezione = $request->sezione;
    
    if ($domanda == 'x') {
        $sql = "SELECT * FROM MISSIONE3_CONF_Help WHERE sezione=? and codice_domanda='descrizione'";
        $rs = $db->Execute($sql, array($sezione));
    }
    else {
        $sql = "SELECT * FROM MISSIONE3_CONF_Help WHERE sezione=? and codice_domanda=?";
        $rs = $db->Execute($sql, array($sezione, $domanda));
		
		if ($rs->RecordCount() == 0) {
			$sql = "SELECT * FROM MISSIONE3_CONF_Help WHERE codice_domanda=?";
			$rs = $db->Execute($sql, array($domanda));
		}
    }
    $testo_help = $rs->Fields("testo_help");
    
    $session->smarty()->assign("testo_help", $testo_help);
    $session->smarty->display("archivio-modal-wizard-help.tpl");
});
    