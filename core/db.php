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

include_once(MAIN."/core/ext/rb.php");
// R::useWriterCache(true);
R::setup('pgsql:host=localhost;dbname=main', 'postgres','postgres');
R::addDatabase('main','pgsql:host=localhost;dbname=main','postgres','postgres');
R::addDatabase('shell','pgsql:host=localhost;dbname=shell','postgres','postgres');

