<?php
////////////////////////////////////////////////////////////////////////

require_once 'AbstractDB.php';

////////////////////////////////////////////////////////////////////////

define("ADB_VERSION", "1.0");

////////////////////////////////////////////////////////////////////////
/**
* Essential functionality for the db class
*
* This class implements the essential functions for all the implementing layers.<br>
* Don't call or instanciate this class.
*
* @link http://www.phpclasses.org/anydb Visit www.phpclasses.org for the latest version
* @author	    Lennart Groetzbach <lennartg@web.de>
* @copyright	Lennart Groetzbach <lennartg@web.de> - distributed under the LGPL
*
* @package      anydb
* @abstract 
* @access       public
* @version      2003/09/08
*/
////////////////////////////////////////////////////////////////////////

class BasicDB extends AbstractDB {

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
// private vars
////////////////////////////////////////////////////////////////////////

/**
* path to the db abstracion layer
* 
* @access   private
* @var     String
*/
var $_path;

/**
* db type
* 
* @access   private
* @var     String
*/
var $_dbType;

////////////////////////////////////////////////////////////////////////
// constructor
////////////////////////////////////////////////////////////////////////

function BasicDB($libraryPath, $dbType, $preferredResType = ANYDB_RES_ASSOC) {
    $this->prefResType = $preferredResType;
    $this->_dbType = $dbType;

    // remove blanks from path
    $this->_path = trim($libraryPath);
    // add trailing '/' if needed
    if ($this->_path != '') {
        if (substr($this->_path, -1) != "/") {
            $this->_path .= '/';
        }
    }
    $this->_id = 'ABSTRACT BASE CLASS';
}

////////////////////////////////////////////////////////////////////////
// public functions
////////////////////////////////////////////////////////////////////////

function connect($host, $db, $user, $password, $persistent = false) {
    // store all the values
    $this->host = $host;
    $this->database = $db;
    $this->user = $user;
    $this->password = $password;
    $this->persistent = $persistent;
    return true;
}

////////////////////////////////////////////////////////////////////////

function query($query, $cacheTime = ANYDB_PREDEFINED_VALUE) {
    // store query string
    $this->lastQuery = $query;
    $this->error = '';
    $this->free();
}

////////////////////////////////////////////////////////////////////////

function getAll($resultType = ANYDB_PREDEFINED_VALUE) {
    if ($resultType == ANYDB_PREDEFINED_VALUE) {
    	$resultType = $this->prefResType;
    }
    $res = array();
    // append all results in an array
    while ($col = $this->getNext($resultType)) {
        array_push($res, $col);
    }
    return $res;
}

////////////////////////////////////////////////////////////////////////

function getValue() {
	$res = $this->_fixType($this->getNext());
    if (is_array($res)) {
        return false;
    } else {
        return $res;
    }
}

////////////////////////////////////////////////////////////////////////

function execute($query, $resultType = ANYDB_PREDEFINED_VALUE) {
    if ($this->query($query)) {
        return $this->getAll($resultType);
    }
    return false;
}

////////////////////////////////////////////////////////////////////////

function getColumn($resultType = ANYDB_PREDEFINED_VALUE) {
    if ($resultType == ANYDB_PREDEFINED_VALUE) {
    	$resultType = $this->prefResType;
    }
    $array = $this->getAll($resultType);
    $res = array();
    for ($i=0; $i < sizeof($array); $i++) {
        $value = $this->_fixType($array[$i], $resultType);
        if ($value == false) {
        } else {
            $res[$i] = $value;
        }
    }
    return $res;
}

////////////////////////////////////////////////////////////////////////

function free() {
    return true;
}

////////////////////////////////////////////////////////////////////////

function getDbType() {
    return $this->_dbType;
}

////////////////////////////////////////////////////////////////////////

function getVersion() {
    return ADB_VERSION;
}

////////////////////////////////////////////////////////////////////////

function getIdentifier() {
    return $this->_id;
}

////////////////////////////////////////////////////////////////////////

function getTables() {
    switch ($this->getDBType()) {
        case 'dbx':
            $this->query("show tables from $this->database");
            break;
    // mysql
        case 'mysql':       // phplib // metabase // pear // adodb
        case 'mysqll':      // adodb
        case 'mysqlt':      // adodb
            $this->query('show tables');
            break;
    // postgresql
        case 'pgsql':       // phplib // metabase // pear
        case 'postgres':    // ado
        case 'postgres64':  // ado
        case 'postgres7':   // ado
            $this->query('select tablename from pg_tables where tableowner = current_user');
            break;
    // oracle
        case 'oracle':      // phplib // ado
        case 'oci':         // metabase
        case 'oci8':        // ado // pear // phplib
        case 'oci8po':      // ado
        case 'odbc_oracle': // ado
            $this->query('SELECT * FROM cat');
            break;
    // microsoft sql
        case 'mssql':       // metabase // pear // phplib // adodb
        case 'odbc_mssql':  // adodb
        case 'mssqlpo':     // adodb
            $this->query('sp_tables');
            break;
    // interbase
        case 'ibase':       // metabase // pear // adodb
            $this->query('show tables');
            break;
    // mini sql
        case 'msql':        // metabase // pear // phplib
            $this->query('show tables');
            break;
    // informix
        case 'ifx':         // metabase // pear
        case 'informix':    // adodb
            $this->query("SELECT tabname FROM systabnames WHERE dbsname = '" . $this->database . "'");
            break;
    // sybase
        case 'sybase':      // pear // phplib // adodb
            $this->query("select name from sysobjects where type='U'");
            break;
    // frontbase
        case 'frontbase':   // pear
        case 'fbsql':       // adodb

        
    // or error
        default:
            $this->error ='Unknown command!';
            return false;
    }
    return $this->getColumn();
}

////////////////////////////////////////////////////////////////////////

function escapeStr($str) {
    if (is_array($str)) {
        $res = array();
        foreach ($str as $key => $val) {
            $res[$key] = $this->escapeStr($val);
        }
        return $res;
    } else {
        switch ($this->_dbType) {
        // mysql
            case 'mysql':       // phplib // metabase // pear // adodb
            case 'mysqll':      // adodb
            case 'mysqlt':      // adodb
                return mysql_escape_string(stripslashes($str));				
        // postgresql
            case 'pgsql':       // phplib // metabase // pear
            case 'postgres':    // ado
            case 'postgres64':  // ado
            case 'postgres7':   // ado
                return pg_escape_string(stripslashes($str));
        // oracle
            case 'oracle':      // phplib // ado
            case 'oci':         // metabase
            case 'oci8':        // ado // pear // phplib
            case 'oci8po':      // ado
            case 'odbc_oracle': // ado
    			return str_replace("'","''",str_replace("''","'",stripslashes($str)));		
        // microsoft sql
            case 'mssql':       // metabase // pear // phplib // adodb
            case 'odbc_mssql':  // adodb
            case 'mssqlpo':     // adodb
    			$str = str_replace("'","''",str_replace("\'","'",$str));
    			$escape = array ( "\n"=>"\\\\012","\r"=>"\\\\015");
    			foreach ( $escape as $match => $replace ) {
    				$str = str_replace($match, $replace, $str);
    			}
    			return $str;
        // mini sql
            case 'msql':        // metabase // pear // phplib
                break;
        // interbase
            case 'ibase':       // metabase // pear // adodb
    			return str_replace("'","''",str_replace("''","'",stripslashes($str)));		
                break;
        // informix
            case 'ifx':         // metabase // pear
            case 'informix':    // adodb
                $str = str_replace ("'", "''", $str ); 
                $str = str_replace ("\r", "", $str ); 
                return $str;
        // sybase
            case 'sybase':      // pear // phplib // adodb
                break;
        // frontbase
            case 'frontbase':   // pear
            case 'fbsql':       // adodb
                break;
        }
        return '';
    }
}
////////////////////////////////////////////////////////////////////////
// private functions
////////////////////////////////////////////////////////////////////////
/**
* clean up function 
*
* @abstract 
* @access   private
*/
function _afterDisconnect() {
    // clear all the values
    $this->db = null;
    $this->host = '';
    $this->database = '';
    $this->user = '';
    $this->password = '';
    return true;

}

////////////////////////////////////////////////////////////////////////
/**
* removes an array depth
*
* @abstract 
* @access   private
*
* @param    Array	$array
* @param    Integer	$resultType
*
* @return   Mixed	1-dim array or String
*/
function _fixType($res, $resultType = ANYDB_RES_NUM) {
    // too much elements?
    $temp = @array_values($res);
    $count = 1;
    if ($resultType == ANYDB_RES_BOTH) {
        $count += 1;
    }
    if (sizeof($temp) == $count) {
        return $temp[0];
    } else {
        return $res;
    }
}

////////////////////////////////////////////////////////////////////////
/**
* Returns only the (renumbered) numeric entries of an array
*
* @abstract 
* @access   private
*
* @param    Array	$array
*
* @return   Array	
*/
function _getNumericEntries($array) {
    if ($array != null) {
	$res = array();
        $i = 0;
        foreach($array as $value) {
            $res[$i++] = $value;
        }
    }
    return $res;
}

/*
function _getNumericEntries($array) {
    if ($array != null) {
        $res = array();
        foreach ($array as $key => $value) {
            if (is_int($key)) {
                $res[$key] = $value;
            }
        }
        return $res;    
    }
}
*/
////////////////////////////////////////////////////////////////////////
/**
* Returns only the associative entries of an array
*
* @abstract 
* @access   private
*
* @param    Array	$array
*
* @return   Array	
*/
function _getAssociativeEntries($array) {
    if ($array != null) {
        $res = array();
        foreach ($array as $key => $value) {
            if (!is_int($key)) {
                $res[$key] = $value;
            }
        }
        return $res;
    }
}
////////////////////////////////////////////////////////////////////////
}
////////////////////////////////////////////////////////////////////////
?>