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
class M3SelezioneMultipla extends M3Risposta {
    private $_record_list;
    
    public function display($options=array()) {
        $session = getSession();
        $smarty = $session->smarty();
        $db = getDB();
        $action_name = $options["action_name"];
        
        $risposte = array();
        
        if ($action_name == "search") {
            $sql = "SELECT 
                          cr.*
                        , '' as post
                        , '' as placeholder
                    FROM MISSIONE3_CONF_PossibiliRisposte cr
                    WHERE cr.codice_domanda=? AND cd.anno=?
                    ORDER BY ordinamento";
            $rs = $db->Execute($sql, array($options["element_code"], $options["anno"]));
        }
        else {
            $sql = "SELECT r.code as code_risposta
                        ,code_attivita
                        ,r.codice_risposta as risposta_selezionata
                        ,supplement1
                        ,supplement2
                        ,supplement3
                        ,supplement4
                        ,supplement5
                        , cr.*
                        , '' as post
                        , '' as placeholder
                    FROM MISSIONE3_CONF_PossibiliRisposte cr
                    LEFT JOIN MISSIONE3_Risposte r ON r.codice_domanda=cr.codice_domanda AND r.codice_risposta=cr.codice_risposta AND code_attivita=?
                    WHERE cr.codice_domanda=? AND cr.anno=?
                    ORDER BY ordinamento";
            $rs = $db->Execute($sql, array($options["attivita_code"], $options["codice_domanda"], $options["anno"]));
        }
        $rows = $rs->GetArray();
        foreach($rows as $row) {
            $codice_risposta = $row["codice_risposta"];
            $campo_aggiuntivo_obbligatorio = $row["campo_aggiuntivo_obbligatorio"];
            $row["campo_aggiuntivo_obbligatorio"] = (strlen($row["campo_aggiuntivo_obbligatorio"])) ? $row["campo_aggiuntivo_obbligatorio"] : 0;
            $risposte[$codice_risposta] = $row;
        }
        
        $schema = $this->_props;
        $schema["pre"] = $options["pre"];
        $schema["post"] = "";
        $schema["no_label"] = true;
        $schema["codice_domanda"] = $options["codice_domanda"];
        
        $schema["title"] = $options["label1_code"];
        $schema["file_tpl"] = @$options["file_tpl"];
        $schema["name"] = "codice_risposta";
        $schema["required"] = @$options["required"];
        $schema["placeholder"] = @$options["placeholder"];
        $this->_props["value"] = $schema["supplement1"];
        
        if ($action_name == "insert")
            $record_code = md5(microtime());
        
        $html .= <<< EOT
        <div class="ui segment container_domanda" style='background: #dedede;'>
            <input type="hidden" name="{$schema["pre"]}code_attivita{$schema["post"]}" value="{$options["attivita_code"]}" />
            <input type="hidden" name="{$schema["pre"]}codice_domanda{$schema["post"]}" value="{$options["codice_domanda"]}" />
EOT;
        $smarty->assign('schema', $schema);
        
        $smarty->assign('props', $this->_props);
        $smarty->assign('action_name', $action_name);
        $smarty->assign('style', $options["style"]);
        $smarty->assign('operatore', $session->user());
        $smarty->assign('risposte', $risposte);
        if (strlen($schema["file_tpl"]))
            $tpl = $schema["file_tpl"]; # $smarty->display($schema["file_tpl"]);
        else
            $tpl = "ui-selezionemultipla.tpl"; #$smarty->display("ui-selezionemultipla.tpl");
        
        $html .= $smarty->fetch($tpl);
        
        
        $html .= "</div>";
        return $html;
    }
    
    
    public function input($options=array()) {
        $session = getSession();
        $db = getDB();
        $session->log("M3SelezioneMultipla::input ({$this->_object_name})");
        
        $elements = $this->_object->elements();
        $this->_record_list = array();
        $pre = $options["pre"];
        
        $sql = "SELECT *, codice_risposta as code, testo_risposta as label
                FROM MISSIONE3_CONF_PossibiliRisposte 
                WHERE codice_domanda=? AND anno=?
                ORDER BY ordinamento";
        
        $session->log("Codice domanda ({$pre}codice_domanda): ".$options[$pre."codice_domanda"]);
        $rs = $db->Execute($sql, array($options[$pre."codice_domanda"], $options["anno"]));
        $lista = $rs->GetArray();
        
        foreach($lista as $row) {
            $codice_risposta = $row["codice_risposta"];
            $pre = $codice_risposta."_";
            $record = new Base();
            $record->set("code_attivita", $options[$options["pre"]."code_attivita"]);
            $record->set("codice_domanda", $options[$options["pre"]."codice_domanda"]);
        
            foreach($elements as $element) {
                $field_name = $element->get("dbfield1");
                #$session->log("Cerco ".$pre.$field_name);
                if (isset($options[$pre.$field_name])) {
                    $field_value = $options[$pre.$field_name];
                    #$session->log("Ho trovato ".$field_value);
                    $record->set($field_name, $field_value);
                }
            }
            
            $this->_record_list[] = $record;
        }
    }
    
    
    public function store($options=array()) {
        $session = getSession();
        $session->log("M3SelezioneMultipla::store ({$this->_object_name})");
        
        $class = "\web3\TableManager";
        $table = $this->_object->get("dbtable"); 
        $manager = new $class($table);
        $model_key_name = $this->_object->get("dbkey");

        foreach($this->_record_list as $record) {
            
            $record_action_name = $action_name = $options["action_name"]; #$this->_action_name;
        
            // $session->log("|||===========================================");
            // $session->log($record);
            $model_key_value = $record->get($model_key_name);
            if ((strlen($model_key_value) > 0) && ($record_action_name == "insert"))
                $record_action_name = "update";
            if ((strlen($model_key_value) == 0) && ($record_action_name == "update"))
                $record_action_name = "insert";
            if ((strlen($model_key_value) == 0) && ($record_action_name == "insert"))
                $record->set($model_key_name, md5(microtime()));
            if ((strlen($model_key_value) > 0) && (strlen($record->get("codice_risposta")) == 0))
                $record_action_name = "delete";
            if ((strlen($model_key_value) == 0) && (strlen($record->get("codice_risposta")) == 0))
                continue;
            
            # Check ma non dovrebbero esserci
            if ($record_action_name != "delete" &&
                strlen($record->get("codice_risposta")) == 0 &&
                strlen($record->get("supplement1")) == 0 &&
                strlen($record->get("supplement2")) == 0 &&
                strlen($record->get("supplement3")) == 0 &&
                strlen($record->get("supplement4")) == 0 &&
                strlen($record->get("supplement5")) == 0) {
                $session->log("Risposta alla voce non data, ignorare.");
                continue;
            }
            
            if (($record_action_name == "delete") || ($options["delete"] == 'S')) {
                $manager->delete($record, $model_key_name, $model_key_value);
            }
            elseif ($record_action_name == "insert") {
                $manager->check($record);
                $manager->insert($record);
            }
            elseif ($record_action_name == "update") {
                $manager->check($record);
                $manager->update($record, $model_key_name, $model_key_value);
            }
            elseif ($record_action_name == "clone") {
                $record->set($model_key_name, microtime());
                $manager->check($record);
                $manager->insert($record);
            }
            $session->log("===========================================|||");
        }        
    }
}