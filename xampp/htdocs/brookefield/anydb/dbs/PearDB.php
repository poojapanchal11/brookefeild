<?php
////////////////////////////////////////////////////////////////////////
/**
* Implementation for PEAR
*
* This class implements the db layer for the PEAR db abstraction layer.<br>
* Tested with version 1.20 of PEAR::DB
* 
*
* @link http://www.phpclasses.org/anyDB            Visit www.phpclasses.org for the latest version
* @link http://pear.php.net/package-info.php?pacid=46   PEAR::DB website
*
* @author	    Lennart Groetzbach <lennartg@web.de>
* @copyright	Lennart Groetzbach <lennartg@web.de> - distributed under the LGPL
*
* @package      anydb
* @access       public
* @version      2003/09/02
*/
////////////////////////////////////////////////////////////////////////

class PearDB extends BasicDB {

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

function PearDB($libraryPath, $dbType, $preferredResType = ANYDB_RES_ASSOC) {
	$par = get_parent_class($this);
	$this->$par($libraryPath, $dbType, $preferredResType);

    $this->_id = 'PEAR';
    require_once $this->_path . "DB.php";

}
////////////////////////////////////////////////////////////////////////

function connect($host, $db, $user, $password, $persistent = false) {
    parent::connect($host, $db, $user, $password, $persistent);

    $dns = $this->getDbType() . "://$user:$password@$host/$db";
    $this->db = DB::connect($dns, $persistent);
    if (is_a($this->db, 'DB_Error')) {
        $this->error = $this->db->message;
        return false;
    } else {
        return true;
    } 
}

////////////////////////////////////////////////////////////////////////

function disconnect() {
    if ($this->db != null) {
        $this->db->disconnect();
        $this->_afterDisconnect();
        return true;
    } else {
        return false;
    }
}

////////////////////////////////////////////////////////////////////////

function query($query) {
    parent::query($query);

    if ($this->db != null) {
        $res = $this->db->query($query);
        if (is_a($res , "DB_Error")) {
            $this->error = $res->message;
    //        $this->error .= " / CODE(" .$res->code . ") / MODE(" . $res->mode . ")";
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
    if ($this->result != null) {
        switch ($resultType) {
            case ANYDB_RES_ASSOC:
                return $this->result->fetchRow(DB_FETCHMODE_ASSOC);
                break;
            default:
            case ANYDB_RES_NUM:
                return $this->result->fetchRow(DB_FETCHMODE_ORDERED);
                break;
            case ANYDB_RES_BOTH:
                // get the result as an associative array
                $res = $this->result->fetchRow(DB_FETCHMODE_ASSOC);
                // also add in the result numeric entries
                $res = array_merge($res, $this->_getNumericentries($res));
                // return it
                return $res;
                break;
        }
    } else {
        return false;
    }
}

////////////////////////////////////////////////////////////////////////

function getAll($resultType = ANYDB_PREDEFINED_VALUE) {
    if ($resultType == ANYDB_PREDEFINED_VALUE) {
    	$resultType = $this->prefResType;
    }
    $res = null;
    if ($this->db != null) {
        switch ($resultType) {
            case ANYDB_RES_BOTH:
            case ANYDB_RES_ASSOC:
                $res = $this->db->getAll($this->lastQuery, null, DB_FETCHMODE_ASSOC);
                break;
            case ANYDB_RES_NUM:
                $res = $this->db->getAll($this->lastQuery, null, DB_FETCHMODE_ORDERED);
                break;
        }
        // was the result an error?
        if (is_a($res, "DB_ERROR")) {
            $this->error = $res->message;
            return false;
        } else {
            if ($resultType == ANYDB_RES_BOTH) {
                // also add in the result numeric entries
                for ($i=0; $i<sizeof($res); $i++) {
                    $res[$i] = array_merge($res[$i], $this->_getNumericentries($res[$i]));
                }
            }
            // return it
            return $res;
        }
    } else {
        return false;
    }
}

////////////////////////////////////////////////////////////////////////

function freeResults() {
    if ($this->result != null) {
        $res = $this->result->free();
        if ($res == true) {
            $this->result = null;
            return true;
        } else {
            $this->error = $res->message;
            return false;
        }
    }    
}

////////////////////////////////////////////////////////////////////////

function numRows() {
    if ($this->result != null) {
        return $this->result->numRows();
    } else {
        return false;
    }
}

////////////////////////////////////////////////////////////////////////

function affectedRows() {
    if ($this->db != null) {
        return $this->db->affectedRows();
    } else {
        return false;
    }
}

////////////////////////////////////////////////////////////////////////
}
////////////////////////////////////////////////////////////////////////
?>