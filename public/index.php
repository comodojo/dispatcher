<?php

use Comodojo\Dispatcher\Dispatcher;
use \Symfony\Component\Yaml\Yaml;

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

$realpath = realpath(dirname(__FILE__)."/../");
date_default_timezone_set(@date_default_timezone_get());

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
 | Configuration, routes and plugins
 | yaml files.
 |
 */
$configuration_file = $realpath."/config/comodojo-config.yml";
$routes_file = $realpath."/config/comodojo-routes.yml";
$plugins_file = $realpath."/config/comodojo-plugins.yml";

/*
 |--------------------------------
 | Import static configuration
 |--------------------------------
 |
 | Import static config in dispatcher
 | instance
 |
 */
$configuration = file_get_contents($configuration_file);
$static_configuration = Yaml::parse($configuration);

/*
 |--------------------------------
 | Init a dispatcher instance
 |--------------------------------
 |
 | Create the dispatcher instance
 |
 */
$dispatcher = new Dispatcher($static_configuration);

/*
 |--------------------------------
 | Loading routes
 |--------------------------------
 |
 | Import static configuration
 | to initialize the router
 |
 */
if (is_null($dispatcher->router()->loadFromCache())) {
 
    $routes = file_get_contents($routes_file);
    $static_routes = Yaml::parse($routes);
    
    $dispatcher->router()->loadRoutes($static_routes);
    
}

/*
 |--------------------------------
 | Load  plugins
 |--------------------------------
 |
 | Load plugins from yaml file
 |
 */
$plugins = file_get_contents($plugins_file);
$static_plugins = Yaml::parse($plugins);

$dispatcher->events()->loadPlugins($static_plugins);

/*
 |--------------------------------
 | Dispatch!
 |--------------------------------
 |
 | Handle request, dispatch result :)
 |
 */
exit( $dispatcher->dispatch() );
