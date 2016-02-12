<?php
/**
 * Example widget
 *
 * PHP version 5
 *
 * @category Widget
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     https://codex.wordpress.org/Widgets_API
 */
namespace Hiwelo\Theme;

/**
 * Example widget class
 *
 * PHP version 5
 *
 * @category Widget
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     https://codex.wordpress.org/Widgets_API
 */
class WidgetExample extends \WP_Widget
{
    /**
     * Widget ID, alsa used for translation methods (_e, __, _n, _x)
     *
     * @var    string
     * @static
     */
    public static $widgetID = 'hwlo_widgetExample';

    /**
     * Widget Name
     *
     * @var    string
     * @static
     */
    public static $widgetName = 'Example Widget';

    /**
     * Widget Description
     *
     * @var    string
     * @static
     */
    public static $widgetDescription = 'Description.';

    /**
     * Widget Class name
     *
     * @var    string
     * @static
     */
    public static $widgetClassName = 'example_widget';

    /**
     * Set up widgets name, ID, description and classname
     *
     * @return void
     */
    public function __construct()
    {
        $widget_options = [
            'class_name' => __(self::$widgetClassName, self::$widgetID),
            'description' => __(self::$widgetDescription, self::$widgetID)
        ];
        parent::__construct(
            self::$widgetID,
            __(self::$widgetName, self::$widgetID),
            $widget_options
        );
    }

    /**
     * Outputs the content of the widget: front-end display of widget
     *
     * @param array $args     Widget arguments
     * @param array $instance Saved values from database
     *
     * @see    WP_Widget::widget()
     * @return void
     */
    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] .
                 apply_filters('widget_title', $instance['title']) .
                 $args['after_title'];
        }
        echo __('Hello World!', self::$widgetID);
        echo $args['after_widget'];
    }

    /**
     * Outputs the options form on admin: back-end widget form
     *
     * @param array $instance Previously saved values from database
     *
     * @see    WP_Widget::form()
     * @return void
     */
    public function form($instance)
    {
        if (!empty($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('New title', self::$widgetID);
        }

        echo '<p>' .
                 '<label for="' . $this->get_field_id('title') . '">' .
                     __('Title: ', self::$widgetID) .
                 '</label>' .
                 '<input class="widefat" ' .
                        'id="' . $this->get_field_id('id') . '" ' .
                        'name="' . $this->get_field_name('title') . '" ' .
                        'type="text" ' .
                        'value="' . esc_attr($title) . '">' .
             '</p>';
    }

    /**
     * Processing widget options on save: sanitize widget form values as they are
     * saved
     *
     * @param array $new_instance Values just sent to be saved
     * @param array $old_instance Previously saved values from database
     *
     * @see    WP_Widget::update()
     * @return array Updated safe values to be saved in database
     */
    public function update($new_instance, $old_instance)
    {
        $instance = [];

        if (!empty($new_instance['title'])) {
            $instance['title'] = strip_tags($new_instance['title']);
        } else {
            $instance['title'] = '';
        }

        return $instance;
    }
}
