<?php
////////////////////////////////////////////////////////////////////////

require_once dirname(__FILE__) . '/../base/UtilityClass.php';

////////////////////////////////////////////////////////////////////////
/**
* Utility class for anyDB
*
* This class provides methods to display db results and functions for common sql queries.<br>
*
* Don't instanciate this class.<br>
* Use 'DBHelper::methodName()' instead.
*
* @link http://www.phpclasses.org/anydb Visit www.phpclasses.org for the latest version
* @author	    Lennart Groetzbach <lennartg@web.de>
* @copyright	Lennart Groetzbach <lennartg@web.de> - distributed under the LGPL
*
* @package      anydb
* @access       public
* @version      2003/09/02
*/
////////////////////////////////////////////////////////////////////////

class DBHelper extends UtilityClass {

////////////////////////////////////////////////////////////////////////
/*
	function selectBox($array, $selected=0, $name='', $size=1, $multiple=false, $additional='') 
	function dumpNext($array, $addHeader = false, $singleRow = true) 
    function dumpAll($array, $addHeader=true, $headerArray = null)
    function dumpColumn($array, $horizontal = true, $headerTitle = '')

    function getCount(& $db, $tableName) {
    function getMax(& $db, $tableName, column)
    function getMin(& $db, $tableName, $column)
*/
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
* Returns a html table for a 2-dimensional result set
*
* @access   public
* @static
*
* @param    Array       $array              the result set
* @param    Boolean     $addHeader          show the array keys as header
* @param    Boolean     $headerArray        alternative headers
*
* @returns  String      html source
*/
function dumpAll($array, $addHeader=true, $headerArray = null) {
    $res = '';
    if (@is_array($array) && @sizeof($array)) {
        $res .= "<table border=1>\n";
			// add header?
            if ($addHeader) {
                $res .= "<tr><th>" . @implode("</th><th>",  ($headerArray != null ? $headerArray : array_keys($array[0]))) . "</th></tr>\n";
            }
        foreach(@$array as $values) {
			// check if array is 2dim
			if (@!is_array($values)) {
				return false;
			}
            $res .= "<tr>\n";
            foreach($values as $key => $value) {
                $res .= "<td>" . ($value != '' ? $value : "&nbsp;") . "</td>";
            }
            $res .= "</tr>\n";
        }
        $res .= "</table>\n";
    }
    return $res;
}

////////////////////////////////////////////////////////////////////////
/**
* Returns a html table for a result column
*
* @access   public
* @static
*
* @param    Array       $array              the result set
* @param    Boolean     $horizontal         order the table horizontally?
* @param    Boolean     $headerTitle        alternative header?
*
* @returns  String      html source
*/
function dumpColumn($array, $horizontal = true, $headerTitle = '') {
    $res = '';
    if (@is_array($array) && @sizeof($array)) {
        $res .= "<table border=1>\n";
        $res .= "<tr>\n";
        if ($headerTitle != '') {
            $res .= "<th>$headerTitle</th>\n" . (!$horizontal ? "</tr>\n" : '');
        }
        foreach($array as $key => $value) {
            if (@is_array($value)) {
                return '';
            }
            $res .= (!$horizontal ? '<tr>' : '') . "<td>" . ($value != '' ? $value : "&nbsp;") . "</td>" . (!$horizontal ? "</tr>\n" : '');
        }
        if ($horizontal) {
            $res .= "</tr>\n";
        }
    $res .= "</table>\n";
    }
    return $res;
}
////////////////////////////////////////////////////////////////////////
/**
* Returns a html table for a result row
*
* @access   public
* @static
*
* @param    Array       $array              the result set
* @param    Boolean     $addHeader          display keys as header?
* @param    Boolean     $singleRow          append table open and close tags?
*
* @returns  String      html source
*/
function dumpNext($array, $addHeader = false, $singleRow = true) {
    $res = '';
    if (@is_array($array) && @sizeof($array)) {
        if ($singleRow) {
	        $res .= "<table border=1>\n";
	        foreach($array as $key => $value) {
	        	if (@is_array($value)) {
	        		return '';
	        	}
	            $res .= "<tr>\n\t" . ($addHeader ? "<th>$key</th>" : '') . "<td>" . ($value != '' ? $value : "&nbsp;") . "</td>\n</tr>\n";
	        }
	    $res .= "</table>\n";
		} else {
			if ($addHeader) {
                $res .= "\n<tr>\n<th>" . implode("</th><th>", array_keys($array)) . "</th>\n</tr><tr>\n";
			}
			$res .= '<td>' . implode($array, "</td><td>") .'</td>';
		}
	}
    return $res;
}
////////////////////////////////////////////////////////////////////////
/**
* Returns a html selectBox for a result column
*
* @access   public
* @static
*
* @param    Array       $array              the result set
* @param    Integer     $selected           the preselected value
* @param    Integer     $size               number of elements shown
* @param    Boolean     $multiple           multiple select?
* @param    String      $additional         additional html code for the select tag
*
* @returns  String      html source
*/
function selectBox($array, $selected=0, $name='', $size=1, $multiple=false, $additional='') {
    $res = '';
    static $count = 0;
    if (is_array($array)) {
        if ($name == '') {
            $name = 'selectBox' . ++$count;
        }
        $res .= "<select name=\"$name\" size=\"$size\"" . ($multiple==false ? '' : " multiple=\"multiple\"") . ($additional ? " $additional" : '') . ">\n";
        $i = 0;
        foreach($array as $value) {
        	if (@is_array($value)) {
        		return false;
        	}
            $res .= "<option" . ($selected == ++$i ? " selected=\"selected\"" : '') . ">$value</option>\n";
        }
        $res .="</select>\n";
    }
    return $res;
}

////////////////////////////////////////////////////////////////////////
/**
* Returns the min value for a given table column
*
* @access   public
* @static
*
* @param    AbstractDB  $db                 db resource
* @param    String      $tableName          table
* @param    String      $columnName         column
*
* @returns  String      value
*/
function getMin(& $db, $tableName, $columnName) {
    $db->query("SELECT min($columnName) FROM $tableName");
    return  $db->getValue();
}

////////////////////////////////////////////////////////////////////////
/**
* Returns the max value for a given table column
*
* @access   public
* @static
*
* @param    AbstractDB  $db                 db resource
* @param    String      $tableName          table
* @param    String      $columnName         column
*
* @returns  String      value
*/

function getMax(& $db, $tableName, $columnName) {
    $db->query("SELECT max($columnName) FROM $tableName");
    return  $db->getValue();
}

////////////////////////////////////////////////////////////////////////
/**
* Returns the number of elements in a column
*
* @access   public
* @static
*
* @param    AbstractDB  $db                 db resource
* @param    String      $tableName          table
*
* @returns  Integer     value
*/
function getCount(& $db, $tableName) {
    $db->query("SELECT count(*) FROM $tableName");
    return  $db->getValue();
}

////////////////////////////////////////////////////////////////////////
}
////////////////////////////////////////////////////////////////////////
?>

