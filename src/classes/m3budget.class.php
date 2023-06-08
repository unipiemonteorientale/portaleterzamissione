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
class M3Budget extends M3Risposta {
    
    
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
        $this->_props["value"] = $schema["supplement1"];
        
        $smarty->assign('schema', $schema);
        $smarty->assign('props', $this->_props);
        $smarty->assign('action_name', $options["action_name"]);
        $smarty->assign('style', $options["style"]);
        $smarty->assign('operatore', $session->user());
        
        if ($options["action_name"] == "insert")
            $record_code = md5(microtime());
        
        $readonly = "";
        if ($options["action_name"] == "read")
            $readonly = " readonly='' ";
        
        if ($options["action_name"] == "print") {
            for($i=1; $i<=3; $i++)
                if (strlen(trim($schema["supplement".$i])) == 0)
                    $schema["supplement".$i] = 0;
            $html .=  <<< EOT
<p>Totale &euro; {$schema["supplement1"]}<br>
<ul>
    <li>di cui finanziamenti pubblici &euro; {$schema["supplement2"]}</li>
    <li>di cui finanziamenti privati &euro; {$schema["supplement3"]}</li>
</ul>
EOT;
        }
        else {
            $html .=  <<< EOT
<div class="ui segment container_domanda field" style=''>
    <input type="hidden" name="{$schema["pre"]}code{$schema["post"]}" value="{$options["code_risposta"]}" />
    <input type="hidden" name="{$schema["pre"]}code_attivita{$schema["post"]}" value="{$options["attivita_code"]}" />
    <input type="hidden" name="{$schema["pre"]}codice_domanda{$schema["post"]}" value="{$options["codice_domanda"]}" />
    
    <!--label>{$schema["title"]}</label-->

    <div class="ui stackable five column grid">
        <div class="column field">
            <label>Totale</label>
            <div class="ui labeled input">
                <div class="ui label"><i class="icon euro"></i></div>
                <input type="number" step="0.01" name="{$schema["pre"]}supplement1" value="{$schema["supplement1"]}" {$readonly}/>
            </div>
        </div>
        <div class="column field">
            <label>di cui finanziamenti pubblici</label>
            <div class="ui labeled input">
                <div class="ui label"><i class="icon euro"></i></div>
                <input type="number" step="0.01" name="{$schema["pre"]}supplement2" value="{$schema["supplement2"]}" {$readonly}/>
            </div>
        </div>
        <div class="column field">
            <label>di cui finanziamenti privati</label>
            <div class="ui labeled input">
                <div class="ui label"><i class="icon euro"></i></div>
                <input type="number" step="0.01" name="{$schema["pre"]}supplement3" value="{$schema["supplement3"]}" {$readonly}/>
            </div>
        </div>
    </div>
EOT;
        }
        // print_r($schema);
        // print_r($options);
        
        // if ($options["format_code"] == 'TEXTAREA')
            // $smarty->display("ui-field-textarea.tpl");
        // else
            // $smarty->display("ui-field-input.tpl");
        $html .= "</div>";
        return $html;
    }
    
    
    public function input($options=array()) {
        $session = getSession();
        error_log("M3Budget::input ({$this->_object_name})");
        
        $elements = $this->_object->elements();
        $pre = $options["pre"];
        foreach($elements as $element) {
            $field_name = $element->get("dbfield1");
            if (isset($options[$pre.$field_name])) {
                $field_value = $options[$pre.$field_name];
                $this->set(array($field_name => $field_value));
            }
        }
        
        $budget_totale = $this->get("supplement1");
        $budget_pubblico = $this->get("supplement2");
        $budget_privato = $this->get("supplement3");
        
        if ($budget_totale != $budget_pubblico + $budget_privato) {
            $result = new Result();
            $result->setResult(false);
            $result->setCode("KO");
            $result->setDescription("Il budget totale non corrisponde alla somma del budget pubblico e del budget privato.");
            $result->setLevel(Result::ERROR);
            echo $result->toJson();
            exit();
        }
    }
}