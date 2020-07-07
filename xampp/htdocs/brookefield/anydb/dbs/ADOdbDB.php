<?php
////////////////////////////////////////////////////////////////////////
/**
* Implementation for ADODB
*
* This class implements the db interface for the ADODB abstraction layer.<br>
* Tested with version 3.5 of ADODB
* 
*
* @link http://www.phpclasses.org/anydb         Visit www.phpclasses.org for the latest version
* @link http://php.weblogs.com/ADODB            ADODB website

* @author	    Lennart Groetzbach <lennartg@web.de>
* @copyright	Lennart Groetzbach <lennartg@web.de> - distributed under the LGPL
*
* @package      anydb
* @access       public
* @version      2003/09/02
*/
////////////////////////////////////////////////////////////////////////

class ADOdbDB extends BasicDB {

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
/**
* row count for getNext()
* 
* @access   private
* @var     Integer
*/
var $_count = 0;

/**
* previous restult type
* 
* @access   private
* @var     String
*/
var $_last = ANYDB_PREDEFINED_VALUE;

////////////////////////////////////////////////////////////////////////

function ADOdbDB($libraryPath, $dbType, $preferredResType = ANYDB_RES_ASSOC) {
	$par = get_parent_class($this);
	$this->$par($libraryPath, $dbType, $preferredResType);
    
    $this->_id = 'ADODB';

    require_once $this->_path . 'adodb.inc.php';
}

////////////////////////////////////////////////////////////////////////

function connect($host, $db, $user, $password, $persistent = false) {
    parent::connect($host, $db, $user, $password, $persistent);

    $this->db = &ADONewConnection($this->_dbType);
    if ($persistent) {
        $res = $this->db->PConnect($host, $user, $password, $db);
    } else {
        $res = $this->db->Connect($host, $user, $password, $db);
    }
    return $res;    
}

////////////////////////////////////////////////////////////////////////

function disconnect() {
    if ($this->db != null) {
        $this->db->Close();
        $this->_afterDisconnect();
        return true;
    } else {
        return false;
    }
}

////////////////////////////////////////////////////////////////////////

function query($query) {
    parent::query($query);
	$this->_count = 0;
    if ($this->db != null) {
        $res = & $this->db->Execute($query);
        if (!$res) {
            $this->error = $this->db->ErrorMsg();
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
	$count = $this->_count;
	// did the result type change?
	if ($this->_last != $resultType) {
		$this->_last = $resultType;
		switch ($resultType) {
            case ANYDB_RES_BOTH:
				$this->db->SetFetchMode(ADODB_FETCH_BOTH);
                break;
            case ANYDB_RES_ASSOC:
				$this->db->SetFetchMode(ADODB_FETCH_ASSOC);
                break;
            case ANYDB_RES_NUM:
				$this->db->SetFetchMode(ADODB_FETCH_NUM);
                break;
		}
		$this->query($this->lastQuery);
		@$this->result->Move($count);
	}

	$res = @$this->result->FetchRow();
	$this->_count = $count + 1;
    return $res;
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
				$this->db->SetFetchMode(ADODB_FETCH_BOTH);
                $res = $this->db->getAll($this->lastQuery);
                break;
            case ANYDB_RES_ASSOC:
				$this->db->SetFetchMode(ADODB_FETCH_ASSOC);

                $res = $this->db->getAll($this->lastQuery);
                break;
            case ANYDB_RES_NUM:
				$this->db->SetFetchMode(ADODB_FETCH_NUM);
                $res = $this->db->getAll($this->lastQuery);
                break;
        }
        // was the result an error?
        if (is_a($res, "DB_ERROR")) {
            $this->error = $res->message;
            return false;
        } else {
            // return it
            return $res;
        }
    } else {
        return false;
    }
}

////////////////////////////////////////////////////////////////////////

function getColumn($resultType = ANYDB_PREDEFINED_VALUE) {
    if ($resultType == ANYDB_PREDEFINED_VALUE) {
    	$resultType = $this->prefResType;
    }
    $res = null;
    if ($this->db != null) {
        switch ($resultType) {
            case ANYDB_RES_BOTH:
				$this->db->SetFetchMode(ADODB_FETCH_BOTH);
                $res = $this->db->getCol($this->lastQuery);
                break;
            case ANYDB_RES_ASSOC:
				$this->db->SetFetchMode(ADODB_FETCH_ASSOC);
                $res = $this->db->getCol($this->lastQuery);
                break;
            case ANYDB_RES_NUM:
				$this->db->SetFetchMode(ADODB_FETCH_NUM);
                $res = $this->db->getCol($this->lastQuery);
                break;
        }
        if (is_a($res, "DB_ERROR")) {
            $this->error = $res->message;
            return false;
        } else {
            // return it
            return $res;
        }
    } else {
        return false;
    }
}
////////////////////////////////////////////////////////////////////////

function getValue() {
    $res = null;
    if ($this->db != null) {
        $res = $this->db->getOne($this->lastQuery);
        if (is_a($res, "DB_ERROR")) {
            $this->error = $res->message;
            return false;
        } else {
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
        $res = $this->result->Close();
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
        return $this->result->RecordCount();
    } else {
        return false;
    }
}

////////////////////////////////////////////////////////////////////////

function affectedRows() {
    if ($this->db != null) {
        return $this->db->Affected_Rows();
    } else {
        return false;
    }
}
////////////////////////////////////////////////////////////////////////
}
////////////////////////////////////////////////////////////////////////
?>