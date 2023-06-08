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
class M3TestoLibero extends M3Risposta {
    
    
    public function display($options=array()) {
        $session = getSession();
        $smarty = $session->smarty();
        $html = "";
        
        $schema = $this->_props;
        $schema["pre"] = $options["pre"];
        $schema["post"] = "";
        $schema["no_label"] = true;
        
        $schema["title"] = $options["label1_code"];
        $schema["name"] = "supplement1";
        $schema["required"] = $options["required"];
        $schema["placeholder"] = $options["placeholder"];
        $schema["format_code"] = $options["format_code"];
        $schema["dbfield1"] = "supplement1";
        $schema["dbfield2"] = "supplement2";
        $schema["label1_code"] = "Data inizio";
        $schema["label2_code"] = "Data fine";
        $this->_props["value"] = $schema["supplement1"];
        $this->_props["value2"] = $schema["supplement2"];
        
        $smarty->assign('schema', $schema);
        $smarty->assign('props', $this->_props);
        $smarty->assign('action_name', $options["action_name"]);
        $smarty->assign('style', $options["style"]);
        $smarty->assign('operatore', $session->user());
        
        if ($options["action_name"] == "insert")
            $record_code = md5(microtime());
        
        
        $html .= <<< EOT
        <div class="ui segment container_domanda" style=''>
            <input type="hidden" name="{$schema["pre"]}code{$schema["post"]}" value="{$options["code_risposta"]}" />
            <input type="hidden" name="{$schema["pre"]}code_attivita{$schema["post"]}" value="{$options["attivita_code"]}" />
            <input type="hidden" name="{$schema["pre"]}codice_domanda{$schema["post"]}" value="{$options["codice_domanda"]}" />
            <input type="hidden" name="{$schema["pre"]}format_code{$schema["post"]}" value="{$options["format_code"]}" />
EOT;
      
        // print_r($schema);
        // print_r($options);
        
        if ($options["format_code"] == 'TEXTAREA')
            $tpl = "ui-field-textarea.tpl"; #$smarty->display("ui-field-textarea.tpl");
        elseif ($options["format_code"] == 'DATE')
            $tpl = "ui-field-date.tpl"; #$smarty->display("ui-field-date.tpl");
        elseif ($options["format_code"] == 'DATERANGE')
            $tpl = "ui-field-daterange.tpl"; #$smarty->display("ui-field-daterange.tpl");
        elseif ($options["format_code"] == 'INT')
            $tpl = "ui-field-input.tpl"; #$smarty->display("ui-field-input.tpl");
        else
            $tpl = "ui-field-input.tpl"; #$smarty->display("ui-field-input.tpl");
        
        $html .= $smarty->fetch($tpl);
        $html .= "</div>";
        
        return $html;
    }
    
    
    public function input($options=array()) {
        $session = getSession();
        $session->log("M3TestoLibero::input ({$this->_object_name})");
        
        $elements = $this->_object->elements();
        $pre = $options["pre"];
        foreach($elements as $element) {
            $field_name = $element->get("dbfield1");
            $session->log("Cerco ".$pre.$field_name);
            if (isset($options[$pre.$field_name])) {
                $field_value = $options[$pre.$field_name];
                
                // i campi supplementari seguono le regole del formato
                if (stripos($field_name, "supplement") !== false) {
                    $format_code = $options[$pre."format_code"];
                    $session->log($format_code);
                    switch($format_code) {
                        case "DATE":
                        case "DATERANGE":
                            $field_value = date_translate($field_value, $format_input='it', $format_output='us');
                            break;
                        default:
                            break;
                    }
                }
                
                $session->log("Ho trovato '".$field_value."'");
                $this->set(array($field_name => $field_value));
            }
        }
        $session->log($this->_props);
    }
}