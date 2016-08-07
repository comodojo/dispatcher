<?php

use \Comodojo\Dispatcher\Dispatcher;
use \Symfony\Component\Yaml\Yaml;
use \Symfony\Component\Yaml\Exception\ParseException;

/**
 * Comodojo dispatcher - REST services microframework
 *
 * @package     Comodojo dispatcher
 * @author      Marco Giovinazzi <marco.giovinazzi@comodojo.org>
 * @license     GPL-3.0+
 *
 * LICENSE:
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/*
 |--------------------------------
 | Configuration
 |--------------------------------
 |
 | Retrieve real path and declare
 | configuration files.
 |
*/
$realpath = realpath(dirname(__FILE__)."/../");
$configuration_file = $realpath."/config/comodojo-config.yml";
$routes_file = $realpath."/config/comodojo-routes.yml";
$plugins_file = $realpath."/config/comodojo-plugins.yml";

/*
 |--------------------------------
 | Autoloader
 |--------------------------------
 |
 | Register the autoloader, located in vendor
 | directory. In a composer installation, this
 | will be handled directly with composer.
 |
 */
require $realpath.'/vendor/autoload.php';

/*
 |--------------------------------
 | Configuration files
 |--------------------------------
 |
 | Read and parse configuration,
 | routes and plugins yaml files.
 |
 */
try {

    $configuration = Yaml::parse(file_get_contents($configuration_file));
    $routes = Yaml::parse(file_get_contents($routes_file));
    $plugins = Yaml::parse(file_get_contents($plugins_file));

} catch (ParseException $pe) {

    exit("<h1>Error reading configuration files</h1>");

}

/*
 |--------------------------------
 | Init a dispatcher instance
 |--------------------------------
 |
 | Create the dispatcher instance
 |
 */
$dispatcher = new Dispatcher($configuration);

/*
 |--------------------------------
 | Loading routes
 |--------------------------------
 |
 | Import routes (if route cache is
 | empty)
 |
 */
if ( empty($dispatcher->router->table->routes) ) {
    $dispatcher->router->table->load($routes);
}

/*
 |--------------------------------
 | Load  plugins
 |--------------------------------
 |
 | Load plugins
 |
 */
$dispatcher->events->load($plugins);

/*
 |--------------------------------
 | Dispatch!
 |--------------------------------
 |
 | Handle request, dispatch result :)
 |
 */
exit( $dispatcher->dispatch() );
