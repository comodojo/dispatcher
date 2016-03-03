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
 | Load config, routes and plugins
 |--------------------------------
 |
 | Load configuration, routes and
 | plugins from yaml files
 |
 */
$configuration = file_get_contents($realpath."/config/comodojo-config.yml");
$routes = file_get_contents($realpath."/config/comodojo-routes.yml");
$plugins = file_get_contents($realpath."/config/comodojo-plugins.yml");

/*
 |--------------------------------
 | Init a dispatcher instance
 |--------------------------------
 |
 | Create the dispatcher instance
 |
 */
$dispatcher = new Dispatcher();

/*
 |--------------------------------
 | Import static configuration
 |--------------------------------
 |
 | Import static config in dispatcher
 | instance
 |
 */
$dispatcher->configuration()->loadFromYaml($configuration);
$dispatcher->router()->loadFromYaml($routes);
$dispatcher->events()->loadFromYaml($plugins);

/*
 |--------------------------------
 | Dispatch!
 |--------------------------------
 |
 | Handle request, dispatch result :)
 |
 */
exit( $dispatcher->dispatch() );
