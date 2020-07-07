<?php
////////////////////////////////////////////////////////////////////////
/**
* Extension of the PHPLIBs db class
*
* This class has a new constructor which copies the given parameters and calls the default constructor
*
* @link http://www.phpclasses.org/anydb            Visit www.phpclasses.org for the latest version
* @link http://phplib.sourceforge.net/                  PHPLIB website
*
* @author	    Lennart Groetzbach <lennartg@web.de>
* @copyright	Lennart Groetzbach <lennartg@web.de> - distributed under the LGPL
*
* @package      anydb
* @access       public
* @version      2003/09/02
*/

////////////////////////////////////////////////////////////////////////

class myDB_Sql extends DB_Sql {

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
* db host name
* 
* @access   public
* @var     String
*/
var	$Host;

/**
* db database name
* 
* @access   public
* @var     String
*/
var	$Database;

/**
* db user name
* 
* @access   public
* @var     String
*/
var	$User;

/**
* db user password
* 
* @access   public
* @var     String
*/
var	$Password;

/**
* db error flag
* 
* @access   public
* @var     String
*/
var	$Halt_On_Error = "no";

////////////////////////////////////////////////////////////////////////
/**
* Constructor
*
* Calls the parent constructor
*
* @access   public
*
* @param    String      $host           
* @param    String      $db             
* @param    String      $user           
* @param    String      $password
*/
function myDB_Sql($host, $db, $user, $password) {
    $this->Host = $host;
    $this->User = $user;
    $this->Database = $db;
    $this->Password = $password;

	$par = get_parent_class($this);
	$this->$par();
}
////////////////////////////////////////////////////////////////////////
}
////////////////////////////////////////////////////////////////////////
?>