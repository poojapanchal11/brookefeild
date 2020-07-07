<?php
////////////////////////////////////////////////////////////////////////
/**
* "Interface" class for the anyDB
*
* This class defines the public methods and constants for anyDB.
* Don't call or instanciate this class.
*
* @link http://www.phpclasses.org/anydb Visit www.phpclasses.org for the latest version
* @author	    Lennart Groetzbach <lennartg@web.de>
* @copyright	Lennart Groetzbach <lennartg@web.de> - distributed under the LGPL
*
* @package      anydb
* @abstract 
* @access       public
* @version      2003/09/02
*/
////////////////////////////////////////////////////////////////////////

define('ANYDB_PREDEFINED_VALUE', -1);

define('ANYDB_RES_ASSOC', 1);
define('ANYDB_RES_NUM', 2);
define('ANYDB_RES_BOTH', ANYDB_RES_NUM + ANYDB_RES_ASSOC);

////////////////////////////////////////////////////////////////////////
/**
* @deprecated   use ANYDB_PREDEFINED_VALUE instead
*/
define("PREDEFINED_VALUE", 0);
/**
* @deprecated   use ANYDB_RES_ASSOC instead
*/
define("ASSOCIATIVE_ARRAY", 1);
/**
* @deprecated   use ANYDB_RES_NUM instead
*/
define("NUMERIC_ARRAY", 2);
/**
* @deprecated   use ANYDB_RES_BOTH instead
*/
define("BOTH", NUMERIC_ARRAY + ASSOCIATIVE_ARRAY);

////////////////////////////////////////////////////////////////////////
/**
* Abstract base class for db access
*
* This class defines the interface for all the implementing layers.
*
* Don't call or instanciate this class.
*
* @link http://www.phpclasses.org/anydb Visit www.phpclasses.org for the latest version
* @author	    Lennart Groetzbach <lennartg@web.de>
* @copyright	Lennart Groetzbach <lennartg@web.de> - distributed under the LGPL
* @version 	    1.0  - 2003/09/09
*
* @package      anydb
* @abstract 
* @access       public
*/
////////////////////////////////////////////////////////////////////////

class AbstractDB extends UtilityClass {

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
// public variables
////////////////////////////////////////////////////////////////////////
/**
* db host name
* 
* @access   public
* @var     String
*/
var $host = '';
/**
* db name
* 
* @access   public
* @var      String
*/
var $database = '';

/**
* user name
* 
* @access   public
* @var      String
*/
var $user = '';

/**
* user password
* 
* @access   public
* @var      String
*/
var $password = '';

/**
* database resource
* 
* @access   public
* @var      Mixed
*/
var $db = null;

/**
* result resource
* 
* @access   public
* @var      Mixed
*/
var $result = null;

/**
* the preferred result type
* 
* @access   public
* @var      Integer
*/
var $prefResType;

/**
* the last submitted query
* 
* @access   public
* @var      String
*/
var $lastQuery = '';

/**
* use a persistent db connection?
* 
* @access   public
* @var     Boolean
*/
var $persistent;

/**
* error string of the last error that occured
* 
* @access   public
* @var      String
*/
var $error = '';

////////////////////////////////////////////////////////////////////////
// constructor
////////////////////////////////////////////////////////////////////////
/**
* Constructor
*
* @abstract 
* @access   public
*
* @param    String      $libraryPath        path to the database abstraction layer
* @param    String      $dbType             identifier of the db type
* @param    Integer     $preferredResType   the preferred result type
*/
function AbstractDB($libraryPath, $dbType, $preferredResType = ANYDB_RES_ASSOC) {
}

////////////////////////////////////////////////////////////////////////
// public functions
////////////////////////////////////////////////////////////////////////

/**
* Connects to the db
*
* @abstract 
* @access   public
*
* @param    String      $host           
* @param    String      $db             
* @param    String      $user           
* @param    String      $password
* @param    Boolean     $persistent
*
* @return   Boolean     TRUE, if successful
*/
function connect($host, $db, $user, $password, $persistent = false) {
}

////////////////////////////////////////////////////////////////////////
/**
* Closes the db connection
*
* @abstract 
* @access   public     
*
* @return   Boolean     TRUE, if successful
*/
function disconnect() {
}

////////////////////////////////////////////////////////////////////////
/**
* Submits an sql statement to the db
*
*
* @abstract 
* @access   public
*
* @param    String      $query
*
* @return   Boolean     TRUE, if successful
*/
function query($query, $cacheTime = ANYDB_PREDEFINED_VALUE) {
}

////////////////////////////////////////////////////////////////////////
/**
* Returns the next row in an array
*
* @abstract 
* @access   public
*
* @param    Integer     $resultType         should the array have numeric, associative keys, or both
*
* @return   Mixed       1-dimensional array or FALSE
*/
function getNext($resultType = ANYDB_PREDEFINED_VALUE) {
}

////////////////////////////////////////////////////////////////////////
/**
* Returns the resulting table in an 2-dimensional array
*
* @abstract 
* @access   public
*
* @param    Integer     $resultType         should the array have numeric, associative keys, or both
*
* @return   Mixed       2-dimensional array or FALSE
*/
function getAll($resultType = ANYDB_PREDEFINED_VALUE) {
}

////////////////////////////////////////////////////////////////////////
/**
* Returns a single value after an apropriate sql statement
*
* @abstract 
* @access   public
*
* @return   Mixed       String or FALSE
*/
function getValue() {
}

////////////////////////////////////////////////////////////////////////
/**
* Returns a result column after an apropriate sql statement
*
* @abstract 
* @access   public
*
* @param    Integer     $resultType         should the array have numeric, associative keys, or both
*
* @return   Mixed       1-dimensional array or FALSE
*/
function getColumn($resultType = ANYDB_PREDEFINED_VALUE) {
}

////////////////////////////////////////////////////////////////////////
/**
* Executes a statement and returns the result table
*
* @abstract 
* @access   public
*
* @param    String      $query              a sql statement
* @param    Integer     $resultType         should the array have numeric, associative keys, or both
*
* @return   Mixed       2-dimensional array or FALSE
*/
function execute($query, $resultType = ANYDB_PREDEFINED_VALUE) {
}

////////////////////////////////////////////////////////////////////////
/**
* Frees the memory from the result set
*
* @abstract 
* @access   public
*
* @return   Mixed       TRUE, if successful
*/
function free() {
}

////////////////////////////////////////////////////////////////////////
/**
* Returns how many rows are in the result set
*
* @abstract 
* @access   public
*
* @return   Mixed       TRUE, if successful
*/
function numRows() {
}

////////////////////////////////////////////////////////////////////////
/**
* Returns how many rows were affected by the last statement
*
* @abstract 
* @access   public
*
* @return   Mixed       Integer or FALSE
*/
function affectedRows() {
}

////////////////////////////////////////////////////////////////////////
/**
* Returns how many rows were affected by the last statement
*
* @abstract 
* @access   public
*
* @return   Mixed       String or FALSE
*/
function getIdentifier() {
}

////////////////////////////////////////////////////////////////////////
/**
* Returns the db type identifier
*
* @abstract 
* @access   public
*
* @return   String
*/
function getDbType() {
}

////////////////////////////////////////////////////////////////////////
/**
* Returns the current version
*
* @abstract 
* @access   public
*
* @return   Integer
*/
function getVersion() {
}

////////////////////////////////////////////////////////////////////////
/**
* Modifies a string to make it secure to add to the db
*
* @abstract 
* @access   public
*
* @param    Mixed       $str        a string
*
* @return   Mixed       String or FALSE
*/
function escapeStr($str) {
}

////////////////////////////////////////////////////////////////////////
/**
* Returns the table names of the current db
*
* @abstract 
* @access   public
*
* @return   Mixed       Array or FALSE
*/
function getTables() {
}

////////////////////////////////////////////////////////////////////////
}
////////////////////////////////////////////////////////////////////////

?>