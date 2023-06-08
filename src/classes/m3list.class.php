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
class M3List extends UIList {
    // private $_class_name;
    // private $_parent;
    
    // function __construct($class_or_object_name, $parent=null) {
        // $this->_class_name = $class_or_object_name;
        // $this->_parent = $parent;
    // }
    
    
    public function display($options=array()) {
        $session = getSession();
        $smarty = $session->smarty();
        
        // print_r($options);
        // print_r($this);
        $html = "";
        
        $schema["pre"] = $options["pre"];
        $schema["post"] = "";
        
        $schema["title"] = $options["label1_code"];
        $schema["required"] = $options["required"];
        $schema["placeholder"] = $options["placeholder"];
        
        $smarty->assign('schema', $schema);
        $smarty->assign('options', $options);
        $smarty->assign('parent', $this->_parent);
        
        
        $html .= <<< EOT
        <div class="ui segment container_domanda" style=''>
            <input type="hidden" name="{$schema["pre"]}code{$schema["post"]}" value="{$options["code_risposta"]}" />
            <input type="hidden" name="{$schema["pre"]}code_attivita{$schema["post"]}" value="{$options["attivita_code"]}" />
            <input type="hidden" name="{$schema["pre"]}codice_domanda{$schema["post"]}" value="{$options["codice_domanda"]}" />
EOT;
        //$smarty->display("ui-domanda-list.tpl");
        
        $session->log("M3List::display::".$this->_class_name);
        $obj = new WEB3Object($this->_class_name);
        $class= $obj->getClass();
        $item = new $class();
        $html .= $item->display_list($options);
        $html .= "</div>";
        return $html;
    }
    
    public function lista($codice_attivita, $codice_domanda) {
        $session = getSession();
        $db = getDB();
        error_log("M3List::lista ({$this->_object_name})");
        $result = false;
        
        error_log($this->_class_name);
        
        
        $obj = new WEB3Object($this->_class_name);
        if (strlen($obj->get("dbtable"))) {
            $table = $obj->get("dbtable"); #$object_code;
            $sql = "SELECT * FROM {$table} WHERE code_attivita=? AND codice_domanda=?";
            error_log($sql);
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
        }

        return $result;
    }
    
}