<?php
//  
// Copyright (c) ASPIDE.NET. All rights reserved.  
// Licensed under the GPLv3 License. See LICENSE file in the project root for full license information.  
//  
// SPDX-License-Identifier: GPL-3.0-or-later
//

namespace web3;


class M3Risposta extends UIObject {
    
    function init($context_name, $object_name, $action_name, $record_code=null) {
        $session = getSession();
        $this->_props = array();
        #$this->_template_name = $template_name;
        $this->_context_name = "default";
        $this->_object_name = "M3_RISPOSTA";
        $this->_action_name = $action_name;
        $this->_record_code = $record_code;
        $this->_uielements = array();
        
        $this->_object = new WEB3Object($this->_object_name, $this->_context_name);
        
        $session->log("M3Risposta::init():record_code={$record_code}");
        if (strlen($record_code)) {
            // TODO: se è gestito da una classe, deve essere la classe che carica 
            if (strlen($this->_object->get("dbtable"))) {
                $table = $this->_object->get("dbtable"); #$object_code;
                $manager = new TableManager($table);
                $this->_props = $manager->read($record_code);
            }
        }
    }
    
    public function store($options=array()) {
        $session = getSession();
        error_log("M3Risposta::store ({$this->_object_name})");
        
        if (strlen($this->_props["code"]) == 0) {
            $this->set(array("code" => md5(microtime())));
            $options["action_name"] = "insert";
        }
        
        // TODO: capire se serve ed aggiustare!!!
        // if (strlen($this->_props["codice_risposta"]) == 0 &&
            // strlen($this->_props["supplement1"]) == 0 &&
            // strlen($this->_props["supplement2"]) == 0 &&
            // strlen($this->_props["supplement3"]) == 0 &&
            // strlen($this->_props["supplement4"]) == 0 &&
            // strlen($this->_props["supplement5"]) == 0) {
            // $session->log("Risposta non data, ignorare. ({$this->_props["codice_domanda"]})");
            // return;
        // }
        
        #$session->log($this->_props);
        return parent::store($options);
    }
    
    public function lista($codice_attivita, $codice_domanda) {
        $session = getSession();
        $db = getDB();
        error_log("M3Risposta::lista ({$this->_object_name})");
        $result = false;
        
        $sql = "SELECT * FROM MISSIONE3_Risposte WHERE code_attivita=? AND codice_domanda=?";
        $rs = $db->Execute($sql, array($codice_attivita, $codice_domanda));
        if ($rs) {
            if ($rs->RecordCount()) {
                while(!$rs->EOF) {
                    $code_risposta = $rs->Fields("code");
                    $result[] = $code_risposta;
                    $rs->MoveNext();
                }
            }
        }
        return $result;
    }
    
}