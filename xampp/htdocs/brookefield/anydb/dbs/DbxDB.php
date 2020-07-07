<?php
////////////////////////////////////////////////////////////////////////
/**
* Implementation for DBX
*
* This class implements the db interface for the DBX flat file system.<br>
* Tested with version 3.5 of ADODB
* 
* @link http://www.phpclasses.org/anydb     Visit www.phpclasses.org for the latest version
* @link http://dbx.idya.net/                XXXXX

* @author	    Lennart Groetzbach <lennartg@web.de>
* @copyright	Lennart Groetzbach <lennartg@web.de> - distributed under the LGPL
*
* @package      anydb
* @access       public
* @version      2003/09/02
*/
////////////////////////////////////////////////////////////////////////

class DbxDB extends BasicDB {

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

function DbxDB($libraryPath, $dbType, $preferredResType = ANYDB_RES_ASSOC) {
	$par = get_parent_class($this);
	$this->$par($libraryPath, $dbType, $preferredResType);
    
    $this->_id = 'DBX';

    global $DBXPATH;
    $DBXPATH = $this->_path;
    require_once $this->_path . 'dbx.inc.php';
}

////////////////////////////////////////////////////////////////////////

function connect($host, $db, $user, $password, $persistent = false) {
    parent::connect($host, $db, $user, $password, $persistent);
    $this->db = new dbx();
    return $this->query("SELECT DATABASE $db");    
}

////////////////////////////////////////////////////////////////////////

function disconnect() {
    return true;
}

////////////////////////////////////////////////////////////////////////

function query($query) {
    parent::query($query);
    if ($this->db != null) {
        $res = & $this->db->dbx_query($query);
        if (!$res) {
            $this->error = $this->db->dbx_error();
            $this->result = null;
            return false;
        }
        else {
            $this->result = $res;
            return true;
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
    switch ($resultType) {
        case ANYDB_RES_BOTH:
			$tmp = $this->db->dbx_fetch_array($this->result);
			return array_merge($tmp, array_values($tmp));
            break;
        case ANYDB_RES_ASSOC:
			return $this->db->dbx_fetch_array($this->result);
            break;
        case ANYDB_RES_NUM:
			return array_values($this->db->dbx_fetch_array($this->result));
            break;
	}

}

////////////////////////////////////////////////////////////////////////

function freeResults() {
    return true;
}

////////////////////////////////////////////////////////////////////////

function numRows() {
    if ($this->result != null) {
        return $this->db->dbx_num_rows();
    } else {
        return false;
    }
}

////////////////////////////////////////////////////////////////////////

function affectedRows() {
    return false;
}

////////////////////////////////////////////////////////////////////////
}
////////////////////////////////////////////////////////////////////////
?>