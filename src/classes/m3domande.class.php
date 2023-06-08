<?php
//  
// Copyright (c) ASPIDE.NET. All rights reserved.  
// Licensed under the GPLv3 License. See LICENSE file in the project root for full license information.  
//  
// SPDX-License-Identifier: GPL-3.0-or-later
//

namespace web3;


/*
 * Le classi custom che gestiscono gli oggetti devono derivare da questa
 */
class M3Domande extends UIObject {
    protected $_risposte;
    protected $_rules;
    protected $_elements;
    protected $_anno;
    
    function init($context_name, $object_name, $action_name, $record_code=null) {
        
        parent::init($context_name, $object_name, $action_name, $record_code);
        error_log("M3Domande::init (record code = {$record_code})");
        
        $db = getDB();
        $session = getSession();

        # Priprietà dell'attività
        $sql = "SELECT * FROM MISSIONE3_Attivita WHERE code=?";
        $rs = $db->Execute($sql, array($record_code));
        $tipo_compilazione = $rs->Fields("tipo_compilazione");
        $campo_azione = $rs->Fields("campo_azione");
        $this->_anno = $rs->Fields("anno");

        $elements = $this->elements();
        $session->log("Elements");
        $session->log($elements);
        
        foreach($elements as $element) {
            $element_code = $element->get("element_code");
            #error_log($element_code);
            
            # Risposte
            $sql = "SELECT * FROM MISSIONE3_Risposte WHERE code_attivita=? AND codice_domanda=?";
            $rs = $db->Execute($sql, array($record_code, $element_code));
            if ($rs) {
                if ($rs->RecordCount()) {
                    while(!$rs->EOF) {
                        $row = $rs->GetRow();
                        $this->_risposte[$element_code] = $row;
                        //$this->set(array("value" => $rs->Fields("supplement1"))); // non serve a nulla e forse è sbagliato???
                        // TODO: una risposta vuota non è una risposta.
                    }
                }
            }
            
            # Integro le risposte a lista
            $elemclass = $element->getClass();
            $elemclass = (strlen($elemclass)) ? $elemclass : "\web3\UIElement";
            if ($element->get("list_objects")) {
                $uielem = new M3List($elemclass, $options["record_code"]);
                $risposte = $uielem->lista($record_code, $element_code); // codice_attivita e codice_domanda
                if ($risposte !== false)
                    $this->_risposte[$element_code] = $risposte;
            }
            
        }
        
        # Rules
        $this->rules($this->_anno, $campo_azione, $tipo_compilazione);
        
        //$session->log($this->_props);
        $session->log("M3Domande::init FINE (record code = {$record_code})");
    }

    protected function elements() {
        $db = getDB();
        $session = getSession();
        $session->log("M3Domande::elements()");
        $session->log("context_name = ".$this->_context_name);

        $this->_elements = array();

        $sql = "SELECT *, sezione as context_name, codice as element_code, descrizione as label1_code
                FROM MISSIONE3_CONF_Domande 
                WHERE anno=? AND sezione=? 
                ORDER BY sorting";
        $rs = $db->Execute($sql, array($this->_anno, $this->_context_name));
        while(!$rs->EOF) {
            $row = $rs->GetRow();
            //$session->log($row);
            $this->_elements[] = new WEB3Element($row);
        }
        return $this->_elements;
    }
    
    protected function rules($anno, $campo_azione, $tipo_compilazione) {
        $db = getDB();
        $session = getSession();
        
        # Rules default
        $sql = "SELECT default_visibile, default_obbligatoria, d.sezione, d.codice as codice_domanda, d.descrizione as testo_domanda
                    , codice_risposta_padre, codice_padre
                FROM MISSIONE3_CONF_Domande d
                WHERE d.anno=? AND d.sezione in ('AP', 'CH', '{$campo_azione}')
                ORDER BY LEN(d.sezione) desc, sorting, codice_domanda";
        $rs = $db->Execute($sql, array($anno));
        $rows = $rs->GetArray();
        foreach($rows as $row) {
            $codice_domanda = $row["codice_domanda"];
            $testo_domanda = $row["testo_domanda"];
            $visibile = $row["default_visibile"];
            $obbligatoria = $row["default_obbligatoria"];
            
            $this->_rules[$codice_domanda] = array(
                "testo_domanda" => $testo_domanda,
                "sezione" => $row["sezione"],
                "visibile" => $visibile,
                "obbligatoria" => $obbligatoria,
                "codice_padre" => $row["codice_padre"],
                "codice_risposta_padre" => $row["codice_risposta_padre"],
            );
        }
        
        # Rules specifiche (solo AP e CH)
        $sql = "select * from MISSIONE3_CONF_Regole WHERE anno=? AND sezione in ('AP', 'CH')";
        $rs = $db->Execute($sql, array($anno));
        $rows = $rs->GetArray();
        foreach($rows as $row) {
            $codice_domanda = $row["codice_domanda"];
            
            if ($tipo_compilazione == 'V') {
                if ($row["regola_visibile"] == "RU02" || $row["regola_visibile"] == "ALL") 
                    $this->_rules[$codice_domanda]["visibile"] = $row["visibile"];
                if ($row["regola_obbligatoria"] == "RU02" || $row["regola_obbligatoria"] == "ALL") 
                    $this->_rules[$codice_domanda]["obbligatoria"] = $row["obbligatoria"];
            }
            elseif ($tipo_compilazione == 'B') {
                if ($row["regola_visibile"] == "RU01" || $row["regola_visibile"] == "ALL") 
                    $this->_rules[$codice_domanda]["visibile"] = $row["visibile"];
                if ($row["regola_obbligatoria"] == "RU01" || $row["regola_obbligatoria"] == "ALL") 
                    $this->_rules[$codice_domanda]["obbligatoria"] = $row["obbligatoria"];
            }
        }
        
        # Rules proprie del campo di azione
        $sql = "select * from MISSIONE3_CONF_Regole WHERE anno=? AND sezione=?";
        $rs = $db->Execute($sql, array($anno, $campo_azione));
        $rows = $rs->GetArray();
        foreach($rows as $row) {
            $codice_domanda = $row["codice_domanda"];
            
            if ($tipo_compilazione == 'V') {
                if ($row["regola_visibile"] == "RU02" || $row["regola_visibile"] == "ALL") 
                    $this->_rules[$codice_domanda]["visibile"] = $row["visibile"];
                if ($row["regola_obbligatoria"] == "RU02" || $row["regola_obbligatoria"] == "ALL") 
                    $this->_rules[$codice_domanda]["obbligatoria"] = $row["obbligatoria"];
            }
            elseif ($tipo_compilazione == 'B') {
                if ($row["regola_visibile"] == "RU01" || $row["regola_visibile"] == "ALL") 
                    $this->_rules[$codice_domanda]["visibile"] = $row["visibile"];
                if ($row["regola_obbligatoria"] == "RU01" || $row["regola_obbligatoria"] == "ALL") 
                    $this->_rules[$codice_domanda]["obbligatoria"] = $row["obbligatoria"];
            }
        }
        return $this->_rules;
    }
    
    public function display($options=array()) {
        $session = getSession();
        $db = getDB();
        $session->log("M3Domande::display()");
        $helps = array();
        $help_breve = array();
        $rules = array();
        $html = "";
        
        # Attività
        $sql = "SELECT * FROM MISSIONE3_Attivita WHERE code=?";
        $rs = $db->Execute($sql, array($options["record_code"]));
        $attivita = $rs->GetRow();
        $tipo_compilazione = $attivita["tipo_compilazione"];
        $campo_azione = $attivita["campo_azione"];
        $anno = $attivita["anno"];

        $elements = $this->_elements;
        
        $html .= "<div id='segnaposto_prima_domanda'></div><input type='hidden' name='code_attivita' value='{$options["record_code"]}' />";
        
        # Help
        $sql = "SELECT * FROM MISSIONE3_CONF_Help";
        $rs = $db->Execute($sql, array());
        $rows = $rs->GetArray();
        foreach($rows as $row) {
            $codice_domanda = $row["codice_domanda"];
            $helps[$codice_domanda] = true;
        }
        
        # Help breve
        $sql = "SELECT codice, help_breve FROM MISSIONE3_CONF_Domande WHERE anno=?";
        $rs = $db->Execute($sql, array($anno));
        $rows = $rs->GetArray();
        foreach($rows as $row) {
            $codice_domanda = $row["codice"];
            $help_breve[$codice_domanda] = $row["help_breve"];
        }
        // if ($session->user()->has("DEBUG")) {
            // echo "<pre>";
            // print_r($this->_elements);
            // echo "</pre>";
        // }
        foreach($elements as $element) {
            $session->log("--- DOMANDA {$element->get("element_code")} ----------------------------------------------------------------------------");
            $codice_domanda = $element->get("element_code");
            if (isset($options["SOLO_UNICA_DOMANDA"])) {
                if ($codice_domanda != $options["codice_unica_domanda"])
                    continue;
            }
            if ($this->_rules[$codice_domanda]["visibile"] != 'S')
                continue;
            
            
            $elemclass = $element->getClass();
            $elemclass = (strlen($elemclass)) ? $elemclass : "\web3\UIElement";
            if ($element->get("list_objects")) {
                $session->log("E' una lista");
                $uielem = new M3List($elemclass, $options["record_code"]);
            }
            elseif (class_exists($elemclass)) {
                $uielem = new $elemclass();
                $uielem->init($element->get("context_name"), $element->get("element_code"), $options["action_name"], @$this->_risposte[$element->get("element_code")]["code"]);
            }
            else {
                $uielem = new UIElement();
                $uielem->init($element->get("context_name"), $element->get("element_code"), $options["action_name"], $options["record_code"]);
            }
            $options["anno"] = $anno;
            $options["pre"] = $element->get("element_code")."_";
            $options["format_code"] = $element->get("format_code");
            $options["label1_code"] = $element->get("label1_code");
            $options["file_tpl"] = $element->get("file_tpl");
            
            $options["attivita_code"] = $options["record_code"];
            $options["codice_domanda"] = $codice_domanda;
            $options["code_risposta"] = @$this->_risposte[$element->get("element_code")]["code"];
            $options["codice_padre"] = $this->_rules[$codice_domanda]["codice_padre"];
            $options["codice_risposta_padre"] = $this->_rules[$codice_domanda]["codice_risposta_padre"];
            
            $help = "";
            if (@$helps[$codice_domanda])
                $help = "<span onclick=\"mostraHelp('{$element->get("element_code")}', '{$campo_azione}')\"><i class='question circle icon pointer'></i></span>";
            
            if ($this->_rules[$codice_domanda]["obbligatoria"] == 'S')
                $x = "<i class='red circle icon pointer' title='campo obbligatorio per la validazione'></i>";
            else
                $x = "<i class='green circle icon pointer' title='campo non obbligatorio per la validazione'></i>";
            
            $padre_invisible = "";
            if (strlen($options["codice_padre"]))
                $padre_invisible = " invisible ";
            $html .= "<div id='div_domanda_{$codice_domanda}' class='field div domanda padre {$options["codice_padre"]} {$padre_invisible}'><h4>".$element->get("element_code")." | ".$element->get("label1_code")." {$help} {$x}</h4>";
            
            if (isset($help_breve[$codice_domanda])) {
                $html .= "<em>{$help_breve[$codice_domanda]}</em>";
            }
            
            if (strlen($options["codice_padre"])) {
                $codice_padre = $options["codice_padre"];
                $codice_risposta_padre = $options["codice_risposta_padre"];
                $lista_codici_risposta_padre = "['".implode("','", explode(",", $options["codice_risposta_padre"]))."']";
                
            $html .= <<<EOT
<script>
$(document).ready(function() {
    console.log("{$codice_domanda} in ascolto per {$codice_padre} in attesa di {$codice_risposta_padre}");
    var lista_codici_risposta_padre = '{$codice_risposta_padre}';
    document.addEventListener("{$codice_padre}", function(ev) { 
            console.log("{$codice_domanda} ha ricevuto '"+ev.detail.risposta+"' da {$codice_padre} e aspetta {$codice_risposta_padre}");
            
            codice_risposta = ev.detail.risposta;
            //console.log("guardiamo se sono uguali: ", codice_risposta, codice_risposta_padre);
			if (codice_risposta.trim().length === 0)
				codice_risposta = 'xxxxxxxxxxxxxxxxxxxxx';
            
            var lista_codici_risposta_padre = '{$codice_risposta_padre}';
            if (lista_codici_risposta_padre.includes(codice_risposta)) {
                console.log("devo mostrare {$codice_domanda}");
                $("#div_domanda_{$codice_domanda}").removeClass("invisible");
            }
            else {
                $("#div_domanda_{$codice_domanda}").addClass("invisible");
                sendTrigger('{$codice_domanda}', '');
            }
    });
    var init_risposta_padre = {$codice_padre}; 
    if (init_risposta_padre.trim().length === 0)
		init_risposta_padre = 'xxxxxxxxxxxxxxxxxxxxx';
    if (lista_codici_risposta_padre.includes(init_risposta_padre)) {
        console.log("devo mostrare {$codice_domanda}");
        $("#div_domanda_{$codice_domanda}").removeClass("invisible");
    }
});
</script>
EOT;
            }
            $html .= $uielem->display($options); # $this->_props
            $html .= "</div>";
        }
        
        if ($options["action_name"] == "print")
            return $html;
        else
            echo $html;
    }
    
    public function input($options=array()) {
        $session = getSession();
        error_log("M3Domande::input ({$this->_object_name})");
        //return $this->_object->input($action_name, $params, $options, $record_code);
        $elements = $this->_elements;
        
        $action_name = $options["action_name"];
        $record_code = $options["record_code"];
        
        foreach($elements as $element) {
            $codice_domanda = $element->get("element_code");
            if ($element->get("list_objects")) {
                continue;
            }
            if ($this->_rules[$codice_domanda]["visibile"] != 'S')
                continue;
            
            $elemclass = $element->getClass();
            $elemclass = (strlen($elemclass)) ? $elemclass : "\web3\UIElement";
            $uielem = new $elemclass();
            #$uielem->copyFromWeb3Element($element);
            #$uielem->setValues(array($this->_props[$element->dbfield()]));
            
            $uielem->init($element->get("context_name"), $element->get("element_code"), $action_name, $record_code);
            
            $options["pre"] = $element->get("element_code")."_";
            
            
            $result = $uielem->input($options); 
            
            if (strlen($element->dbfield())) {
                foreach($result as $field_name => $field_value)
                    if ($element->dbfield() == $field_name)
                        $this->set($field_name, $field_value);
            }   
            $this->_uielements[] = $uielem;
        }
    }
    
    public function store($options=array()) {
        $session = getSession();
        error_log("M3Domande::store ({$this->_object_name})");
        foreach($this->_uielements as $uielem) {
            $uielem->store($options);
        }
    }

    public function validate($options=array()) {
        $session = getSession();
        $session->log("M3Domande::validate ({$this->_object_name})");
        $ok = true;
        $risultati = array();
        
        // echo "<h1>Context name: {$options['context_name']}</h1>";
        
        // echo "<br><br>Risposte:<br><code>";
        // print_r($this->_risposte);
        // echo "</code><br><br>Regole:<br>";
        // print_r($this->_rules);
        
        foreach($this->_rules as $domanda => $rule) {
            if (strtoupper($rule["sezione"]) != strtoupper($options['context_name']))
                continue;
            $risposta_ok = true;
            
            // se la domanda è figlia di un padre
            if (strlen($rule["codice_padre"]) > 0) {
                $codice_domanda_padre = $rule["codice_padre"];
                $risposta_domanda_padre = $this->_risposte[$codice_domanda_padre];
                $session->log("Domanda: {$domanda}, padre: {$codice_domanda_padre}, codice risposta: {$risposta_domanda_padre["codice_risposta"]}");
                // se la domanda padre è stata risposta come si aspetta la domanda figlia
                if ($risposta_domanda_padre["codice_risposta"] == $rule["codice_risposta_padre"]) {
                    ;
                }
                else {
                    $rule["obbligatoria"] = 'N';
                    $rule["visibile"] = 'N';
                }
            }
            
            
            
            
            if ($rule["obbligatoria"] == 'S') {
                // se la domanda è stata risposta
                if (isset($this->_risposte[$domanda])) {
                    $risposta = $this->_risposte[$domanda];
                    $session->log($risposta);
                    if (!isset($risposta["code"]) && is_array($risposta)) // se la risposta è una lista di risposte, ok
                        ;
                    else {
                        if (strlen($risposta["codice_risposta"]) == 0 && strlen($risposta["supplement1"]) == 0) {
                            $ok = false;
                            $risposta_ok = false;
                        }
                    }
                }
                // se la domanda non è stata risposta ma era obbligatoria
                else {
                    #echo "KO<br>";
                    $ok = false;
                    $risposta_ok = false;
                }
                
            }
            else {
                if (!isset($this->_risposte[$domanda]))
                    $risposta_ok = false;
            }
            if ($rule["visibile"] == 'S') {
                $risultati[] = array(
                    "codice_domanda" => $domanda,
                    "testo_domanda" => $rule["testo_domanda"],
                    "obbligatoria" => $rule["obbligatoria"],
                    "risposta_ok" => $risposta_ok,
                );
            }
        }
        
        if ($ok === false) {
            switch($options['context_name']) {
                case 'AP': $step_sezione = 3; break;
                case 'CH': $step_sezione = 5; break;
                default: $step_sezione = 4; break;
            }
            $session->smarty()->assign("step_sezione", $step_sezione);
            $session->smarty()->assign("sezione", $options['context_name']);
            $session->smarty()->assign("risultati", $risultati);
            $session->smarty->display("ui-domanda-validazione.tpl");
        }
        else 
            return true;
        return false;
    }
}