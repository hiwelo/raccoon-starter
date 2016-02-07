<?php
/**
 * Template core methods
 *
 * PHP version 5
 *
 * @category Core
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     ./docs/api/classes/Hwlo.Raccoon.Core.html
 */
namespace Hwlo\Raccoon;

use Symfony\Component\Debug\Debug;

/**
 * Template core methods
 *
 * PHP version 5
 *
 * @category Core
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     ./docs/api/classes/Hwlo.Raccoon.Core.html
 */
class Raccoon
{
    /**
     * Theme namespace, used mainly for translation methods (_e, __, _n, _x)
     *
     * @var string
     */
    public $namespace = 'raccoon';

    /**
     * Environment status (development, staging, production)
     *
     * @var string
     */
    public $environment = 'production';

    /**
     * Manifest informations for this theme, contain all theme configuration
     *
     * @var array
     */
    public $manifest = [];

    /**
     * Setup this theme with all informations available in the manifest, a JSON
     * configuration file
     *
     * @param string $file manifest filename
     *
     * @return void
     */
    public function __construct($file = '')
    {
        // load theme manifest and store it
        if (!$this->loadManifest($file)) {
            return;
        }
    }

    /**
     * Returned array used for object debug informations
     *
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'manifest' => $this->manifest,
        ];
    }

    /**
     * Load manifest.json content and store it
     *
     * @param string $file filename, must be at theme root folder
     *
     * @return boolean true if the file exists, false otherwise
     *
     * @link https://developer.wordpress.org/reference/functions/locate_template/
     * @uses Raccoon::$manifest
     */
    private function loadManifest($file = '')
    {
        if (empty($file)) {
            $file = 'manifest.json';
        }

        $file = locate_template($file);

        // verify if file exists
        if (!file_exists($file)) {
            return false;
        }

        $file = file_get_contents($file);

        // verify if file isn't empty
        if (empty($file)) {
            return false;
        }

        // parsing json file
        $this->manifest = json_decode($file, true);

        return true;
    }
}
