<?php
/**
 * WordPress theme mess cleanup methods
 *
 * PHP version 5
 *
 * @category CleanUp
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     ./docs/api/classes/Hwlo.Raccoon.Core.html
 */
namespace Hwlo\Raccoon;

/**
 * WordPress theme mess cleanup methods
 *
 * PHP version 5
 *
 * @category CleanUp
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     ./docs/api/classes/Hwlo.Raccoon.Core.html
 */
class CleanUp
{
    /**
     * CleanUp configuration from the manifest
     * @var array
     */
    private $cleanUp = [];

    public $default = [
        "admin" => [
            "metaboxes" => [
                "dashboard_right_now",
                "dashboard_incoming_links",
                "dashboard_quick_press",
                "dashboard_plugins",
                "dashboard_recent_drafts",
                "dashboard_recent_comments",
                "dashboard_primary",
                "dashboard_secondary"
            ],
        ],
        "security" => [
            "wlwmanifest_link",
            "rsd_link",
            "index_rel_link",
            "parent_post_rel_link",
            "start_post_rel_link",
            "adjacent_posts_rel_link",
            "feed_links_extra",
            "adjacent_posts_rel_link_wp_head",
            "wp_generator",
            "wp_shortlink_wp_head",
        ],
    ];

    /**
     * Clean up class constructor, check for configuration or informations
     * in the manifest
     *
     * @param array $configuration cleanUp configuration
     * @return void
     *
     * @link https://codex.wordpress.org/Function_Reference/locate_template
     * @uses CleanUp::adminCleanUp()
     */
    public function __construct($configuration = [])
    {
        // load manifest with an empty configuration
        if (count($configuration) === 0) {
            $file = locate_template($file);
            $file = file_get_contents($file);
            $manifest = json_decode($file, true);

            if (array_key_exists('theme-features', $manifest)
                && array_key_exists('theme-features', $manifest['cleanup'])
            ) {
                $configuration = $manifest['cleanup'];
            }
        }

        if (is_array($configuration)) {
            $this->cleanUp = array_merge($this->default, $configuration);
        } else {
            Tools::parseBooleans($configuration);
            if ($configuration) {
                $this->cleanUp = $this->default;
            }
        }

        // we call admin clean up parts, if asked in the manifest
        $this->adminCleanUp();
    }

    /**
     * Clean Up WordPress Admin mess
     *
     * @return void
     */
    public function adminCleanUp()
    {
        if (is_array($this->cleanUp['admin'])) {
            $this->cleanUp['admin'] = array_merge(
                $this->default['admin'],
                $this->cleanUp['admin']
            );
        } else {
            Tools::parseBooleans($this->cleanUp['admin']);
            if ($this->cleanUp['admin']) {
                $this->cleanUp['admin'] = $this->default['admin'];
            }
        }

        if (array_key_exists('metaboxes', $this->cleanUp['admin'])
            && is_array($this->cleanUp['admin']['metaboxes'])
            && count($this->cleanUp['admin']['metaboxes'])
        ) {
            $metaboxes = $this->cleanUp['admin']['metaboxes'];

            foreach ($metaboxes as $metabox) {
                Core::$metaBoxToRemove[] = [
                    $metabox,
                    'dashboard',
                    'core'
                ];
            }
        }
    }

    /**
     * Clean up WordPress wp_head mess for more security
     *
     * @return void
     */
    public function securityCleanUp()
    {
        if (is_array($this->cleanUp['security'])) {
            $this->cleanUp['security'] = array_merge(
                $this->default['security'],
                $this->cleanUp['security']
            );
        } else {
            Tools::parseBooleans($this->cleanUp['security']);
            if ($this->cleanUp['security']) {
                $this->cleanUp['security'] = $this->default['security'];
            }
        }

        if (count($this->cleanUp['security'])) {
            foreach ($this->cleanUp['security'] as $action) {
                remove_action('wp_head', $action);
            }
        }
    }
}
