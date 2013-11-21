<?php

/**
 * Postgres query management.
 *
 * @category   Login / Registration
 * @package    Ideabile Framework
 * @author     Mauro Mandracchia <info@ideabile.com>
 * @copyright  2013 - 2014 Ideabile
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    Release: 0.1a
 * @link       http://www.ideabile.com
 * @see        -
 * @since      -
 * @deprecated -
 */
 
// @TODO Add the possibility when is GET the data to add pagination. (LIMIT 0 100)
// @TODO Add the possibility to GET the data by filters.


class Shell {
	public $host = "localhost";
	public $port = "3306";
	public $dbname = "links";
	public $user = "root";
	public $password = "root";
}
