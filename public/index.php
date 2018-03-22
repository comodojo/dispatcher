<?php

use \Comodojo\Dispatcher\Dispatcher;
use \Comodojo\Dispatcher\Components\RoutesLoader;
use \Comodojo\Dispatcher\Components\PluginsLoader;
use \Comodojo\Foundation\Base\ConfigurationLoader;

/**
 * @package     Comodojo Dispatcher
 * @author      Marco Giovinazzi <marco.giovinazzi@comodojo.org>
 * @author      Marco Castiello <marco.castiello@gmail.com>
 * @license     MIT
 *
 * LICENSE:
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/*
 |--------------------------------
 | Configuration
 |--------------------------------
 |
 | Retrieve real path, init autoloader
 | and declare configuration files.
 |
*/
$realpath = realpath(dirname(__FILE__)."/../");
$autoloader = "$realpath/vendor/autoload.php";
$configuration_file = "$realpath/config/comodojo-configuration.yml";
$routes_file = "$realpath/config/comodojo-routes.yml";
$plugins_file = "$realpath/config/comodojo-plugins.yml";

require_once $autoloader;

/*
 |--------------------------------
 | Main Configuration
 |--------------------------------
 |
 | Read and parse main configuration.
 |
 */
try {
    $configuration = ConfigurationLoader::load($configuration_file);
} catch (Exception $e) {
    http_response_code(500);
    exit($e->getMessage());
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
    $dispatcher = new Dispatcher($configuration->get());
} catch (Exception $e) {
    http_response_code(500);
    exit("Dispatcher critical error, please check log: ".$e->getMessage());
}

/*
 |--------------------------------
 | Routes
 |--------------------------------
 |
 | Read and parse routes
 |
 */
if (
    file_exists($routes_file) &&
    empty($dispatcher->getRouter()->getTable()->getRoutes())
) {
    try {
        $routes = RoutesLoader::load($routes_file);
        $dispatcher->getRouter()->getTable()->load($routes);
    } catch (Exception $e) {
        http_response_code(500);
        exit("Unable to process routes, please check log: ".$e->getMessage());
    }
}

/*
 |--------------------------------
 | Load  plugins
 |--------------------------------
 |
 | Load plugins
 |
 */
 if ( file_exists($plugins_file) ) {
     try {
         $plugins = PluginsLoader::load($plugins_file);
         $dispatcher->getEvents()->load($plugins);
     } catch (Exception $e) {
         http_response_code(500);
         exit("Unable to process plugins, please check log: ".$e->getMessage());
     }
 }

/*
 |--------------------------------
 | Dispatch!
 |--------------------------------
 |
 | Handle request, dispatch result :)
 |
 */
exit( $dispatcher->dispatch() );
