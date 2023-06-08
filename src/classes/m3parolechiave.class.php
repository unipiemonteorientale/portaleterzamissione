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
class M3ParoleChiave extends M3Risposta {
    
    
    public function display($options=array()) {
        $session = getSession();
        $smarty = $session->smarty();
        
        $schema = $this->_props;
        $schema["pre"] = $options["pre"];
        $schema["post"] = "";
        $schema["no_label"] = true;
        
        $schema["title"] = $options["label1_code"];
        $schema["name"] = "supplement1";
        $schema["required"] = $options["required"];
        $schema["placeholder"] = $options["placeholder"];
        $this->_props["value"] = $schema["supplement1"];
        
        $smarty->assign('schema', $schema);
        $smarty->assign('props', $this->_props);
        $smarty->assign('action_name', $options["action_name"]);
        $smarty->assign('style', $options["style"]);
        $smarty->assign('operatore', $session->user());
        $html = "";
        
        if ($options["action_name"] == "insert")
            $record_code = md5(microtime());
        
        if ($options["action_name"] == "read" || $options["action_name"] == "print")
			$html .= <<< EOT
        <div class="ui segment container_domanda field" style=''>
            <div class="ui stackable five column grid">
                <div class="column field"><label>Parola #1</label> {$schema["supplement1"]}</div>
                <div class="column field"><label>Parola #2</label> {$schema["supplement2"]}</div>
                <div class="column field"><label>Parola #3</label> {$schema["supplement3"]}</div>
                <div class="column field"><label>Parola #4</label> {$schema["supplement4"]}</div>
                <div class="column field"><label>Parola #5</label> {$schema["supplement5"]}</div>
            </div>
		</div>
EOT;
		else
			$html .= <<< EOT
        <div class="ui segment container_domanda field" style=''>
            <input type="hidden" name="{$schema["pre"]}code{$schema["post"]}" value="{$options["code_risposta"]}" />
            <input type="hidden" name="{$schema["pre"]}code_attivita{$schema["post"]}" value="{$options["attivita_code"]}" />
            <input type="hidden" name="{$schema["pre"]}codice_domanda{$schema["post"]}" value="{$options["codice_domanda"]}" />
            
            <!--label>{$schema["title"]}</label-->
    
            <div class="ui stackable five column grid">
                <div class="column field">
                    <label>Parola #1</label>
                    <div class="ui labeled input">
                        <div class="ui label"><i class="icon pen"></i></div>
                        <input type="text" name="{$schema["pre"]}supplement1" value="{$schema["supplement1"]}" />
                    </div>
                </div>
                <div class="column field">
                    <label>Parola #2</label>
                    <div class="ui labeled input">
                        <div class="ui label"><i class="icon pen"></i></div>
                        <input type="text" name="{$schema["pre"]}supplement2" value="{$schema["supplement2"]}" />
                    </div>
                </div>
                <div class="column field">
                    <label>Parola #3</label>
                    <div class="ui labeled input">
                        <div class="ui label"><i class="icon pen"></i></div>
                        <input type="text" name="{$schema["pre"]}supplement3" value="{$schema["supplement3"]}" />
                    </div>
                </div>
                <div class="column field">
                    <label>Parola #4</label>
                    <div class="ui labeled input">
                        <div class="ui label"><i class="icon pen"></i></div>
                        <input type="text" name="{$schema["pre"]}supplement4" value="{$schema["supplement4"]}" />
                    </div>
                </div>
                <div class="column field">
                    <label>Parola #5</label>
                    <div class="ui labeled input">
                        <div class="ui label"><i class="icon pen"></i></div>
                        <input type="text" name="{$schema["pre"]}supplement5" value="{$schema["supplement5"]}" />
                    </div>
                </div>
            </div>
		</div>
EOT;
      
        // print_r($schema);
        // print_r($options);
        
        // if ($options["format_code"] == 'TEXTAREA')
            // $smarty->display("ui-field-textarea.tpl");
        // else
            // $smarty->display("ui-field-input.tpl");
        // echo "";
        return $html;
    }
    
    
    public function input($options=array()) {
        $session = getSession();
        error_log("M3ParoleChiave::input ({$this->_object_name})");
        
        $elements = $this->_object->elements(); # Object = M3Risposta.
        
        $pre = $options["pre"];
        foreach($elements as $element) {
            $field_name = $element->get("dbfield1");
            if (isset($options[$pre.$field_name])) {
                $field_value = $options[$pre.$field_name];
                $this->set(array($field_name => $field_value));
            }
        }
    }
}