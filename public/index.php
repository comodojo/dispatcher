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
$files = [
    'configuration' => "$realpath/config/comodojo-configuration.yml",
    'routes' => "$realpath/config/comodojo-routes.yml",
    'plugins' => "$realpath/config/comodojo-plugins.yml"
];

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
$confdata = [];
foreach ($files as $config => $path) {
    try {
        $data = @file_get_contents($path);
        if ( $config == 'configuration' && $data === false) {
            throw new Exception("Error reading [$config] configuration file");
        }
        $confdata[$config] = $data !== false ? Yaml::parse($data) : [];
    } catch (ParseException $pe) {
        http_response_code(500);
        exit("Error parsing [$config] configuration file: ".$pe->getMessage());
    } catch (Exception $e) {
        http_response_code(500);
        exit($e->getMessage());
    }
}


/*
 |--------------------------------
 | Init a dispatcher instance
 |--------------------------------
 |
 | Create the dispatcher instance
 |
 */
try {
    $dispatcher = new Dispatcher($confdata['configuration']);
} catch (Exception $e) {
    http_response_code(500);
    exit("Dispatcher critical error, please check log: ".$e->getMessage());
}

/*
 |--------------------------------
 | Loading routes
 |--------------------------------
 |
 | Import routes (if route cache is
 | empty)
 |
 */
$table = $dispatcher->getRouter()->getTable();
if ( empty($table->getRoutes()) ) {
    $table->load($confdata['routes']);
}

/*
 |--------------------------------
 | Load  plugins
 |--------------------------------
 |
 | Load plugins
 | TODO: recode double foreach
 |
 */
 $plain_plugins = [];
 foreach ($confdata['plugins'] as $package => $plugins) {
     foreach ($plugins as $plugin) {
         $plain_plugins[] = $plugin;
     }
 }
 $dispatcher->getEvents()->load($plain_plugins);

/*
 |--------------------------------
 | Dispatch!
 |--------------------------------
 |
 | Handle request, dispatch result :)
 |
 */
exit( $dispatcher->dispatch() );
