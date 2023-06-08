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
class M3TitolareDirittoPatrimoniale extends UIObject {
    private $_record_list;
    
    public function display_list($options=array()) {
        $session = getSession();
        $smarty = $session->smarty();
        $db = getDB();
        
        $html = "";
        $list = array();
        $sql = "SELECT pc.*
                FROM MISSIONE3_TitolariDirittoPatrimoniale pc
				WHERE pc.code_attivita=? AND pc.codice_domanda=?
                ORDER BY pc.classif_titolare";
        $rs = $db->Execute($sql, array($options["attivita_code"], $options["codice_domanda"]));
        $list = $rs->GetArray();
        
        $tot_perc = 0;
        foreach($list as $row) {
            $tot_perc += $row["percentuale"];
        }
        
        $schema = $this->_props;
        $schema["pre"] = $options["pre"];
        $schema["post"] = "";
        $schema["code_attivita"] = $options["attivita_code"];
        $schema["codice_domanda"] = $options["codice_domanda"];
        
        $schema["title"] = $options["label1_code"];
        $schema["name"] = "codice_risposta";
        $schema["required"] = @$options["required"];
        $schema["placeholder"] = @$options["placeholder"];
        $this->_props["value"] = $schema["supplement1"];
        
        if ($options["action_name"] == "insert")
            $record_code = md5(microtime());
        
        $smarty->assign('schema', $schema);
        $smarty->assign('props', $this->_props);
        $smarty->assign('action_name', $options["action_name"]);
        $smarty->assign('current_id', microtime());
        $smarty->assign('operatore', $session->user());
        $smarty->assign('list', $list);
        $smarty->assign('tot_perc', $tot_perc);
        $html .= $smarty->fetch("ui-list-titolaredirittopatrimoniale.tpl");
        return $html;
    }
    
    
    public function input($options=array()) {
        $session = getSession();
        $db = getDB();
        $session->log("M3TitolareDirittoPatrimoniale::input ({$this->_object_name})");
        
        $elements = $this->_object->elements();
        $this->_record_list = array();
        
        $record = new Base();
        
        foreach($elements as $element) {
            for($i=1; $i<=3; $i++) {
                $field_name = $element->get("dbfield{$i}");
                #$session->log("Cerco ".$pre.$field_name);
                if (isset($options[$field_name])) {
                    $field_value = $options[$field_name];
                    #$session->log("Ho trovato ".$field_value);
                    $record->set($field_name, $field_value);
                }
            }
        }
        
        $this->_record_list[] = $record;
    }
    
    
    public function store($options=array()) {
        $session = getSession();
        $db = getDB();
        $session->log("M3TitolareDirittoPatrimoniale::store ({$this->_object_name})");
        
        $class = "\web3\TableManager";
        $table = $this->_object->get("dbtable"); 
        $manager = new $class($table);
        $model_key_name = $this->_object->get("dbkey");
        $record_action_name = $action_name = $options["action_name"]; #$this->_action_name;

        foreach($this->_record_list as $record) {
            $session->log($record);
            
            if (strlen($record->get("classif_titolare")) == 0) {
                $result = new Result();
                $result->setResult(false);
                $result->setCode("KO");
                $result->setDescription("E' necessario indicare il tipo di titolare.");
                $result->setLevel(Result::ERROR);
                throw $result;
            }
            
            if ($record->get("classif_titolare") != "UPO" && strlen($record->get("titolare")) == 0) {
                $result = new Result();
                $result->setResult(false);
                $result->setCode("KO");
                $result->setDescription("E' necessario indicare il titolare se tipologia diversa da UPO.");
                $result->setLevel(Result::ERROR);
                throw $result;
            }
            
            if (strlen($record->get("percentuale")) == 0) {
                $result = new Result();
                $result->setResult(false);
                $result->setCode("KO");
                $result->setDescription("E' necessario indicare la percentuale di titolarità.");
                $result->setLevel(Result::ERROR);
                throw $result;
            }
            
            if (!is_numeric(to_number($record->get("percentuale")))) {
                $result = new Result();
                $result->setResult(false);
                $result->setCode("KO");
                $result->setDescription("E' necessario indicare un numero come percentuale.");
                $result->setLevel(Result::ERROR);
                throw $result;
            }
            
            $sql = "SELECT sum(percentuale) as tot_perc
                    FROM MISSIONE3_TitolariDirittoPatrimoniale
                    WHERE code_attivita=? AND codice_domanda=? AND code<>?";
            $rs = $db->Execute($sql, array($record->get("code_attivita"), $record->get("codice_domanda"), $record->get("code")));
            $row = $rs->GetRow();
            if ($row["tot_perc"] + to_number($record->get("percentuale")) > 100) {
                $result = new Result();
                $result->setResult(false);
                $result->setCode("KO");
                $result->setDescription("La somma delle percentuali supera il 100%.");
                $result->setLevel(Result::ERROR);
                throw $result;
            }
            
            //$model_key_value = $record->get($model_key_name);
            $model_key_value = $options["model_key_value"];#$record->get($model_key_name);
            if (strlen($model_key_value) && $record_action_name == "insert")
                $record_action_name = "update";
            if (strlen($model_key_value) == 0 && $record_action_name == "update")
                $record_action_name = "insert";
            if (strlen($model_key_value) == 0 && $record_action_name == "insert" && strlen($options[$model_key_name]) == 0)
                $record->set($model_key_name, md5(microtime()));
            // if (strlen($model_key_value) && strlen($record->get("codice_risposta")) == 0)
                // $record_action_name = "delete";
            // if (strlen($model_key_value) == 0 && strlen($record->get("codice_risposta")) == 0)
                // continue;
            
            if ($record_action_name == "delete" || $options["delete"] == 'S') {
                $manager->delete($record, $model_key_name, $model_key_value);
            }
            elseif ($record_action_name == "insert") {
                $session->log('check:');
                $manager->check($record);
                $session->log('check ok');
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
        }        
    }
}