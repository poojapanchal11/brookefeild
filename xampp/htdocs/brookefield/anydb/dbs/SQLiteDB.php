<?php
////////////////////////////////////////////////////////////////////////
/**
* Implementation for SQLite
*
* This class implements the db interface for SQLite<br>
* Tested with sqlite version 2.8.3
* 
* @link http://www.phpclasses.org/anydb     Visit www.phpclasses.org for the latest version
*
* @author	    Lennart Groetzbach <lennartg@web.de>
* @copyright	Lennart Groetzbach <lennartg@web.de> - distributed under the LGPL
*
* @package      anydb
* @access       public
* @version      2003/09/08
*/
////////////////////////////////////////////////////////////////////////

class SQLiteDB extends BasicDB {

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

function SQLiteDB($libraryPath, $dbType, $preferredResType = ANYDB_RES_ASSOC) {
	$par = get_parent_class($this);
	$this->$par($libraryPath, $dbType, $preferredResType);
    
    $this->_id = 'SQLITE';
}

////////////////////////////////////////////////////////////////////////

function connect($host, $db, $user, $password, $persistent = false) {
    parent::connect($host, $db, $user, $password, $persistent);
       
    if ($persistent) {
        $this->db = sqlite_open($db);
    } else {
        $this->db = sqlite_popen($db);
    }
    if ($this->db) {
        return true;
    }else {
        $this->error = "Error connecting to db!";
        return false;
    }
}

////////////////////////////////////////////////////////////////////////

function disconnect() {
    parent::disconnect();
    if ($this->db != null) {
        @sqlite_close($this->db);
        $this->_afterDisconnect();
        return true;
    } else {
        $this->error = "Not connected to a db!";
        return false;
    }

}

////////////////////////////////////////////////////////////////////////

function query($query) {
    parent::query($query);
    if ($this->db != null) {
        // submit query
        $this->result = @sqlite_unbuffered_query($this->db, $query);
        if ($this->result != false) {
            return true;
        // save error msg
        } else {
            $this->error = @sqlite_error_string(sqlite_last_error($this->db));
            $this->result = null;
            return false;
        }
    } else {
        return false;    
    }

}

////////////////////////////////////////////////////////////////////////

function getValue() {
        return @sqlite_fetch_string($this->result);
}

////////////////////////////////////////////////////////////////////////

function getNext($resultType = ANYDB_PREDEFINED_VALUE) {
    if ($resultType == ANYDB_PREDEFINED_VALUE) {
    	$resultType = $this->prefResType;
    }
    $res = false;
    // get next result set
    if ($this->result != null) {
        switch ($resultType) {
            case ANYDB_RES_ASSOC:
                $res =  @sqlite_fetch_array($this->result, SQLITE_ASSOC);
                break;
            case ANYDB_RES_NUM:
                $res =  @sqlite_fetch_array($this->result, SQLITE_NUM);
                break;
            case ANYDB_RES_BOTH:
                $res =  @sqlite_fetch_array($this->result, SQLITE_BOTH);
                break;
            default:
                $this->error = "Wrong result type!";
        }
    }
    return $res;
}

////////////////////////////////////////////////////////////////////////

function free() {
    return true;
}
////////////////////////////////////////////////////////////////////////

function numRows() {
    if ($this->result != null) {
        return @sqlite_num_rows($this->result);
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