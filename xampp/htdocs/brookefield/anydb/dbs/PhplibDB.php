<?php
////////////////////////////////////////////////////////////////////////
/**
* Implementation for PHPLIB
*
* This class implements the db layer for the PHPLIB db abstraction layer.<br>
* Tested with version 7.4 beta of Phplib
* 
*
* @link http://www.phpclasses.org/anydb             Visit www.phpclasses.org for the latest version
* @link http://phplib.sourceforge.net/              PHPLIB website
*
* @author	    Lennart Groetzbach <lennartg@web.de>
* @copyright	Lennart Groetzbach <lennartg@web.de> - distributed under the LGPL
*
* @package      anydb
* @access       public
* @version      2003/09/02
*/
////////////////////////////////////////////////////////////////////////

class PhplibDB extends BasicDB {

////////////////////////////////////////////////////////////////////////
/*
    This library is free software; you can redistribute it and/or
    modify it under the terms of the GNU Lesser General Public
    License as published by the Free Software Foundation; either
    version 2.1 of the License, or (at your option) any later version.
    
    This library is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
    Lesser General Public License for more details.
    
    You should have received a copy of the GNU Lesser General Public
    License along with this library; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
////////////////////////////////////////////////////////////////////////

function PhplibDB($libraryPath, $dbType, $preferredResType = ANYDB_RES_ASSOC) {
	$par = get_parent_class($this);
	$this->$par($libraryPath, $dbType, $preferredResType);
    
    // include the required db file
    require_once $this->_path . "php/db_$dbType.inc";

    $this->_id = 'PHPLIB';
}

////////////////////////////////////////////////////////////////////////

function connect($host, $db, $user, $password, $persistent = false) {
    parent::connect($host, $db, $user, $password, $persistent);

    // include my extended class
    require_once 'phplib/myDB_Sql.php';
    $this->db = new myDB_Sql($host, $db, $user, $password);

    if ($persistent) {
        if (@isset($this->db->$PConnect)) {
            $this->db->$PConnect = 1;   
        } else {
            trigger_error('PHPLIB Version does not support persistent db connections', E_USER_WARNING);
        }
    }

    return (($this->db->Link_ID == false) || ($this->db->Errno == null));
}

////////////////////////////////////////////////////////////////////////

function disconnect() {
    parent::disconnect();
    $this->_afterDisconnect();
    return true;
}

////////////////////////////////////////////////////////////////////////

function query($query) {
    parent::query($query);

    if ($this->db != null) {
        // submit query
        @$this->db->query($query);
        // everything fine?
        if ($this->db->Errno == 0) {
            return true;
        // save error msg
        } else {
            $this->error = $this->db->Error;
            return false;
        }
    } else {
        return false;    
    }
}

////////////////////////////////////////////////////////////////////////

function getNext($resultType = ANYDB_PREDEFINED_VALUE) {
    if ($resultType == ANYDB_PREDEFINED_VALUE) {
    	$resultType = $this->prefResType;
    }
    // get next result set
    if ($this->db != null) {
        @$this->db->next_record();
        switch ($resultType) {
            case ANYDB_RES_ASSOC:
                return $this->_getAssociativeEntries($this->db->Record);
                break;
            case ANYDB_RES_BOTH:
                return $this->db->Record;
                break;
            case ANYDB_RES_NUM:
            default:
                return $this->_getNumericEntries($this->db->Record);
                break;
        }
    } else {
        return false;
    }
}

////////////////////////////////////////////////////////////////////////

function numRows() {
    if ($this->db != null) {
        return $this->db->num_rows();
    } else {
        return false;
    }
}

////////////////////////////////////////////////////////////////////////

function affectedRows() {
    if ($this->db != null) {
        return $this->db->affected_rows();
    } else {
        return false;
    }
}

////////////////////////////////////////////////////////////////////////

function getIdentifier() {
    return "PHPLIB";
}
////////////////////////////////////////////////////////////////////////
}
////////////////////////////////////////////////////////////////////////
?>