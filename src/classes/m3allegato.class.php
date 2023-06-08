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
class M3Allegato extends UIObject {
    protected $_record_list;
    protected $_tree_code;
    
    function __construct() {
        $this->_object_name = "M3_ALLEGATO";
        $this->_tree_code = "M3_TIPO_ALLEGATO";
    }
    
    
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
        $sql = "SELECT pc.*, n.label_code as tipologia_allegato
                FROM MISSIONE3_Allegati pc
                LEFT JOIN WEB3_TreeNodes n ON n.node_value=pc.tipologia AND n.tree_code='{$this->_tree_code}'
                WHERE pc.code_attivita=? AND pc.codice_domanda=?";
        $rs = $db->Execute($sql, array($options["attivita_code"], $options["codice_domanda"]));
        $list = $rs->GetArray();
        foreach($list as $k => $item) {
            $url = BASE_URL."/files/MISSIONE3/".base64_encode(FILES_DIR."/MISSIONE3/".$item["file_locazione"]);
            $list[$k]["file_url"] = $url;
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
        $smarty->assign('object_name', $this->_object_name);
        $smarty->assign('current_id', microtime());
        $smarty->assign('operatore', $session->user());
        $smarty->assign('list', $list);
        $html .= $smarty->fetch("ui-list-allegati.tpl");
        return $html;
    }
    
    
    public function input($options=array()) {
        $session = getSession();
        $db = getDB();
        error_log("M3Allegato::input ({$this->_object_name})");
        
        $elements = $this->_object->elements();
        $this->_record_list = array();
        
        $model_key_name = $options["model_key_name"];
        $model_key_value = $options["model_key_value"];
        if (strlen($model_key_name) && strlen($model_key_value)) {
            $record = new Base("MISSIONE3_Allegati", $model_key_name, $model_key_value);
        }
        else
            $record = new Base();
        
        foreach($elements as $element) {
            $field_name = $element->get("dbfield1");
            #$session->log("Cerco ".$pre.$field_name);
            if (isset($options[$field_name])) {
                if ($field_name == "file_locazione") {
                    $prefix = date("Y")."_".$options["code_attivita"]."_".$options["codice_domanda"]."_";
                    $result = web3_upload_file($MODULO_CODE="MISSIONE3", $field_name, $ext_enabled=array('PDF', 'JPG', 'JPEG', 'PNG'/*, 'TIF', 'BMP'*/, 'DOC', 'DOCX', 'XLS', 'XLSX'), $prefix, $max_size=8);
                    if ($result !== false) {
                        $record->set("file_locazione", $result["originale"]);
                        $record->set("file_tipo", $result["tipo"]);
                    }
                }
                else {
                    $field_value = $options[$field_name];
                    #$session->log("Ho trovato ".$field_value);
                    $record->set($field_name, $field_value);
                }
            }
        }
        
        $this->_record_list[] = $record;
        
        // $session->log($record);
        // $session->log($_FILES);
    }
    
    
    public function store($options=array()) {
        $session = getSession();
        error_log("M3Allegato::store ({$this->_object_name})");
        
        $class = "\web3\TableManager";
        $table = $this->_object->get("dbtable"); 
        $manager = new $class($table);
        $model_key_name = $this->_object->get("dbkey");
        $record_action_name = $action_name = $options["action_name"]; #$this->_action_name;
        
        $model_key_value = $options["model_key_value"];#$record->get($model_key_name);
        $record = $this->_record_list[0];
        
        if (strlen($model_key_value) && $record_action_name == "insert")
            $record_action_name = "update";
        if (strlen($model_key_value) == 0 && $record_action_name == "update")
            $record_action_name = "insert";
        if (strlen($model_key_value) == 0 && $record_action_name == "insert" && strlen($options[$model_key_name]) == 0)
            $record->set($model_key_name, md5(microtime()));
         
        if (strlen($record->get("tipologia")) == 0) {
            $result = new Result();
            $result->setResult(false);
            $result->setCode("KO");
            $result->setDescription("E' necessario indicare il tipo di documento che si intende allegare.");
            $result->setLevel(Result::ERROR);
            throw $result;
            // echo $result->toJson();
            // exit();
        }
        
        $session->log($record_action_name);
        $session->log($record);
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
    
    public function store2($options=array()) {
        $session = getSession();
        error_log("M3Allegato::store ({$this->_object_name})");
        
        $class = "\web3\TableManager";
        $table = $this->_object->get("dbtable"); 
        $manager = new $class($table);
        $model_key_name = $this->_object->get("dbkey");
        $record_action_name = $action_name = $options["action_name"]; #$this->_action_name;

        foreach($this->_record_list as $record) {
            $session->log($record);
            
            $model_key_value = $record->get($model_key_name);
            if (strlen($model_key_value) && $record_action_name == "insert")
                $record_action_name = "update";
            if (strlen($model_key_value) == 0 && $record_action_name == "update")
                $record_action_name = "insert";
            if (strlen($model_key_value) == 0 && $record_action_name == "insert")
                $record->set($model_key_name, md5(microtime()));
            if (strlen($model_key_value) && strlen($record->get("codice_risposta")) == 0)
                $record_action_name = "delete";
            if (strlen($model_key_value) == 0 && strlen($record->get("codice_risposta")) == 0)
                continue;
            
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


    
    
    public function delete($options=array()) {
        error_log("M3Allegato::delete ({$this->_object_name})");
        
        $filename = FILES_DIR."/MISSIONE3/".$this->get("file_locazione");
        error_log($filename);
        parent::delete($options);
        if (file_exists($filename))
            unlink($filename);
    }
}