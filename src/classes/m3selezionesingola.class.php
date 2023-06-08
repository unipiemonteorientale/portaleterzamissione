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
class M3SelezioneSingola extends M3Risposta {
    
    
    public function display($options=array()) {
        $session = getSession();
        $smarty = $session->smarty();
        $db = getDB();
        
        $supplementare_tipo = false;
        $supplementare_obbligatorio = false;
        $supplementare_label = "";
        
        $sql = "SELECT *, codice_risposta as code, testo_risposta as label
                FROM MISSIONE3_CONF_PossibiliRisposte 
                WHERE codice_domanda=? AND anno=?
                ORDER BY ordinamento";
        $rs = $db->Execute($sql, array($options["codice_domanda"], $options["anno"]));
        $list = $rs->GetArray();
        foreach($list as $item) {
            if (strlen($item["campo_aggiuntivo_tipo"]))
                $supplementare_tipo = true;
            if (strlen($item["campo_aggiuntivo_obbligatorio"]))
                $supplementare_obbligatorio = true;
            if (strlen($item["campo_aggiuntivo_label"]))
                $supplementare_label = $item["campo_aggiuntivo_label"];
        }
        
        $schema = $this->_props;
        $schema["pre"] = $options["pre"];
        $schema["post"] = "";
        $schema["no_label"] = true;
        
        $schema["title"] = $options["label1_code"];
        $schema["name"] = "codice_risposta";
        $schema["required"] = @$options["required"];
        $schema["placeholder"] = @$options["placeholder"];
        $schema["format_code"] = @$options["format_code"];
        $schema["codice_domanda"] = @$options["codice_domanda"];
        $schema["codice_padre"] = @$options["codice_padre"];
        $schema["codice_risposta_padre"] = @$options["codice_risposta_padre"];
        $this->_props["value"] = $schema["codice_risposta"];
        
        if ($supplementare_tipo) {
            $schema2 = $this->_props;
            $schema2["pre"] = $options["pre"];
            $schema2["post"] = "";
            
            $schema2["title"] = $supplementare_label;
            $schema2["name"] = "supplement1";
            $schema2["required"] = $supplementare_obbligatorio;
            $schema2["placeholder"] = "specificare, se utile o richiesto";
            $smarty->assign('schema2', $schema2);
        }
        
        if ($options["action_name"] == "insert")
            $record_code = md5(microtime());
        
        // echo "<pre>";
        // print_r($schema);
        // echo "</pre>";
        
        $html .= <<< EOT
        <div class="ui segment container_domanda {$options["codice_domanda"]}" style=''>
            <input type="hidden" name="{$schema["pre"]}code{$schema["post"]}" value="{$options["code_risposta"]}" />
            <input type="hidden" name="{$schema["pre"]}code_attivita{$schema["post"]}" value="{$options["attivita_code"]}" />
            <input type="hidden" name="{$schema["pre"]}codice_domanda{$schema["post"]}" value="{$options["codice_domanda"]}" />
EOT;
        $smarty->assign('schema', $schema);
        
        $smarty->assign('props', $this->_props);
        $smarty->assign('action_name', $options["action_name"]);
        $smarty->assign('style', $options["style"]);
        $smarty->assign('operatore', $session->user());
        $smarty->assign('list', $list);
        $smarty->assign('supplementare_tipo', $supplementare_tipo);
        $smarty->assign('supplementare_obbligatorio', $supplementare_obbligatorio);
        $smarty->assign('supplementare_label', $supplementare_label);
        $html .= $smarty->fetch("ui-selezionesingola.tpl");
        $html .= "</div>";
        return $html;
    }
    
    
    public function input($options=array()) {
        $session = getSession();
        error_log("M3SelezioneSingola::input ({$this->_object_name})");
        
        $elements = $this->_object->elements();
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