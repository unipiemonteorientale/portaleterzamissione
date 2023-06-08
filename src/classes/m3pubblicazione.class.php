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
class M3Pubblicazione extends M3Allegato {
    //private $_record_list;
    
    function __construct() {
        $this->_object_name = "M3_PUBBLICAZIONE";
        $this->_tree_code = "M3_TIPO_PUBBLICAZIONE";
    }
    

}