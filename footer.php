<?php
/**
 * Footer template file
 *
 * PHP version 5
 *
 * @category Theme
 * @package  Raccoon
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     https://codex.wordpress.org/Template_Hierarchy
 */
namespace Hwlo\Raccoon;
?>
        </main><!-- .content -->

        <footer class="footer">
            <a href="<?php echo esc_url(home_url('/')); ?>">
                <?php bloginfo('name'); ?>
            </a>
        </footer><!-- .footer -->
    </div><!-- .page -->

    <?php wp_footer(); ?>
</body>
</html>
