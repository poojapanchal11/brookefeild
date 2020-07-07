<?php
////////////////////////////////////////////////////////////////////////
/**
* Implementation for mysql
* Tested with version 4.x of PHP
*
* This class implements the db interface for mysql
*
* @link http://www.phpclasses.org/anyDB  Visit www.phpclasses.org for the latest version
*
* @author	    Lennart Groetzbach <lennartg@web.de>
* @copyright	Lennart Groetzbach <lennartg@web.de> - distributed under the LGPL
*
* @package      anydb
* @access       public
* @version      2003/09/02
*/
////////////////////////////////////////////////////////////////////////

class MysqlDB extends BasicDB {

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

function MysqlDB($libraryPath, $dbType, $preferredResType = ANYDB_RES_ASSOC) {
	$par = get_parent_class($this);
	$this->$par($libraryPath, $dbType, $preferredResType);
    
    $this->_id = 'NATIVE PHP';
}

////////////////////////////////////////////////////////////////////////

function connect($host, $db, $user, $password, $persistent = false) {
    // call parent
    parent::connect($host, $db, $user, $password, $persistent);
    // try to connect    
    if ($persistent) {
        $this->db = mysql_pconnect($host, $user, $password);
    } else {
        $this->db = mysql_connect($host, $user, $password);
    }
    if ($this->db) {
    	if (mysql_select_db($db, $this->db)) {
		    return true;    	
    	}
    	$this->error = @mysql_error($this->db);
    }
    $this->error = "Error connecting to db!";
    return false;
}

////////////////////////////////////////////////////////////////////////

function disconnect() {
    parent::disconnect();

    if ($this->db != null) {
        @mysql_close($this->db);
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
        $this->result = @mysql_query($query, $this->db);
        if ($this->result != false) {
//            $this->_updateCache($query, $cacheTime);
            return true;
        // save error msg
        } else {
            $this->error = @mysql_error($this->db);
        }
    }
//    $this->_cachedRes = null;
//    $this->fromCache = false;

    return false;    

}

////////////////////////////////////////////////////////////////////////

function getNext($resultType = ANYDB_PREDEFINED_VALUE) {
    if ($resultType == ANYDB_PREDEFINED_VALUE) {
    	$resultType = $this->prefResType;
    }
    // get next result set
    if ($this->result != null) {
        switch ($resultType) {
            case ANYDB_RES_ASSOC:
                return @mysql_fetch_array($this->result, MYSQL_ASSOC);
                break;
            case ANYDB_RES_NUM:
                return @mysql_fetch_array($this->result, MYSQL_NUM);
                break;
            case ANYDB_RES_BOTH:
                return @mysql_fetch_array($this->result, MYSQL_BOTH);
                break;
            default:
                $this->error = "Wrong result type!";
                return false;
        }
    } else {
        return false;
    }
}

////////////////////////////////////////////////////////////////////////

function free() {
    if ($this->result != null) {
        return @mysql_free_result($this->result);
    }
}
////////////////////////////////////////////////////////////////////////

function numRows() {
    if ($this->result != null) {
        return @mysql_num_rows($this->result);
    } else {
        return false;
    }
}

////////////////////////////////////////////////////////////////////////

function affectedRows() {
    if ($this->db != null) {
        return @mysql_affected_rows($this->db);
    } else {
        return false;
    }
}
////////////////////////////////////////////////////////////////////////
}
////////////////////////////////////////////////////////////////////////

?>