<?php
//  
// Copyright (c) ASPIDE.NET. All rights reserved.  
// Licensed under the GPLv3 License. See LICENSE file in the project root for full license information.  
//  
// SPDX-License-Identifier: GPL-3.0-or-later
//

namespace web3;

class UserUniUPO extends UIForm {    



    
    
    
    public function display($action_name="read") {
        $session = getSession();
        $smarty = $session->smarty();
        
        // $data = $this->_smarty->createData();
        // $data->assign('action_name', $action_name);
        // $session->smarty()->display($this->_template_name, $data);
        $smarty->assign('props', $this->_props);
        $smarty->assign('fields', $this->_elements);
        $smarty->assign('json_fields', encode_json($this->_elements));
        //$smarty->assign('json_params', encode_json($this->_props));
        $smarty->assign('rules', $rules);
        $smarty->assign('context_name', $this->_context_name);
        $smarty->assign('action_name', $this->_action_name);
        $smarty->assign('object_name', $this->_object_name);
        $smarty->assign('modal_id', $this->_props["modal_id"]);
        $smarty->assign('model_key_name', $this->_dbkey);
        $smarty->assign('model_key_value', $this->_record_code);
        
        $session->log("UserUniUPO::display({$action_name})");
        
        if ($action_name == "insert")
            $smarty->display('admin-form-user.tpl');
        else {
            //$smarty->display('ui-form-generic.tpl');
            
            // $tpl = 'modal-form.tpl';
            // $smarty->assign("header", "{$this->_action_name} {$this->_object_name}");
            // $smarty->assign('context_name', $this->_context_name);
            // #$smarty->assign('delete', $request->param("delete", 0));
            // $smarty->assign('json_params', json_encode($this->_props));
            // $smarty->assign('url', BASE_URL."/api/form/{$this->_context_name}/{$this->_object_name}/{$this->_action_name}/{$this->_record_code}/properties?style={$this->_props['style']}");
            // $smarty->display($tpl);
            $smarty->assign('json_params', encode_json($this->_props));
            $params = $this->_props;
            $action = $this->_action_name;
            $uiform = new UIForm($params["context_name"], $this->_object_name, $action, $params["record_code"]);
            $uiform->set($params);
            $uiform->display($action);
        }
    }
}