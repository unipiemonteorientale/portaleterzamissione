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
class M3PersonaleCoinvolto extends UIObject {
    private $_record_list;
    
    public function display_list($options=array()) {
        $session = getSession();
        $smarty = $session->smarty();
        $db = getDB();
        $html = "";
        
        $list = array();
        // $sql = "SELECT pc.*, p.cognome, p.nome, p.nomestruttura
                // FROM MISSIONE3_PersonaleCoinvolto pc
				// LEFT JOIN CSA_STORICO_PERSONALEINSERVIZIO p ON pc.matricola=p.matricolacsa
                // WHERE pc.code_attivita=? AND pc.codice_domanda=?";
        $sql = "SELECT pc.*, p.cognome, p.nome, p.cod_ssd, p.des_ssd, p.des_aff_org as struttura, tc.label as coinvolgimento
                FROM MISSIONE3_PersonaleCoinvolto pc
				LEFT JOIN CSA_PERSONALE p ON pc.matricola=p.matricola_csa
                LEFT JOIN MISSIONE3_CONF_TipoCoinvolgimento tc ON tc.code=pc.codice_coinvolgimento
                WHERE pc.code_attivita=? AND pc.codice_domanda=?
                ORDER BY pc.codice_coinvolgimento";
        $rs = $db->Execute($sql, array($options["attivita_code"], $options["codice_domanda"]));
        $list = $rs->GetArray();
        
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
        $html .= $smarty->fetch("ui-list-personalecoinvolto.tpl");
        return $html;
    }
    
    
    public function input($options=array()) {
        $session = getSession();
        $db = getDB();
        error_log("M3PersonaleCoinvolto::input ({$this->_object_name})");
        
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
        error_log("M3PersonaleCoinvolto::store ({$this->_object_name})");
        
        $class = "\web3\TableManager";
        $table = $this->_object->get("dbtable"); 
        $manager = new $class($table);
        $model_key_name = $this->_object->get("dbkey");
        $record_action_name = $action_name = $options["action_name"]; #$this->_action_name;

        foreach($this->_record_list as $record) {
            $session->log($record);
            
            if (strlen($record->get("matricola")) == 0 || strlen($record->get("codice_coinvolgimento")) == 0) {
                $result = new Result();
                $result->setResult(false);
                $result->setCode("KO");
                $result->setDescription("E' necessario indicare il docente e il suo coinvolgimento nell'attività.");
                $result->setLevel(Result::ERROR);
                throw $result;
                // echo $result->toJson();
                // exit();
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