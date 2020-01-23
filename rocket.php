<?php
/**
 * Plugin Name: Rocket
 * Description: Modify pages using a visual editor
 * Version: 0.1
 * Author: Manuja DeSilva
 * 
 */

define('ROCKET_BASE_PATH', plugin_dir_path(__FILE__));
define('ROCKET_BASE_URL', plugin_dir_url(__FILE__));
define('BASENAME', 'rocket-editor-');
define('MENU_SLUG', 'rocket-editor');
define('ICON_URL', ROCKET_BASE_URL . "assets/images/rocket.svg");

include(ROCKET_BASE_PATH . "includes/RocketPageEditor.php");

$RocketPlugin = null;

class RocketPlugin
{

    public function __construct() {
        $this->activate_menus_and_buttons();
        $this->activate_filters();
    }

    public function activate() {
        return 0;
    }

    function deactivate() {
        return 0;
    }

    function uninstall() {
        return 0;
    }

    public function activate_menus_and_buttons() {
        add_action("admin_menu", array($this, 'build_menus'));
        if (!is_admin()) { //Only enable the quick link option on the front end
            add_action("admin_bar_menu", array($this, 'build_admin_quick_links_menu'));
        }
        add_action("media_buttons", array($this, 'build_media_button'));
    }

    public function activate_filters() {
        add_filter('page_row_actions', array($this, 'modify_page_row_actions'), 10, 2);
        add_filter('the_content', array($this, 'createTarget'));
    }

    /**
     * Create a wrapper before the_content so that we can identify elements that can be mutated within a page by Rocket Editor
     */
    public function createTarget($content){
        return "<div id='rocketEditorEditableArea'>" . $content . "</div>";
    }

    public function build_media_button() {
        ?>
        <a href='<?php echo $this->build_url() ?>'>
        <button type="button" style="padding-right: 10px;background-color: #f7f7f7;padding-left: 10px;border-radius: 4px;box-shadow: 0 1px 0 #ccc;padding-top: 4px;border-color: #ccc;">
        <img src='<?php echo ICON_URL; ?>' style='height:20px;'/>
        Open with Rocket Editor
        </button>
        </a>
        <?php
    }

    public function build_admin_quick_links_menu($admin_bar) {
        $title = "<p><img src='" . ICON_URL  ."' style='height:20px;padding:2px;'/> Rocket Editor </p>";
        
        $admin_bar->add_menu( array(
            'id' => 'rocketAdminMenuOption',
            'parent' => null,
            'group' => null,
            'title' => $title,
            'href' => $this->build_url()
        ));
    }

    /**
     * Create the menu page, but then remove it from the Admin Sidebar itself. The page can still be accessed. 
     */
    public function build_menus() {
        add_menu_page(
            'Rocket Page Editor',
            'Rocket Page Editor',
            'manage_options',
            MENU_SLUG,
            array($this, 'init_editor')
        );
        remove_menu_page('rocket-editor');
    }

    /**
     * Add a 'Rocket Editor' option to each entry in the Pages list.
     */
    public function modify_page_row_actions($actions, $post) {
        $actions['RocketEditor'] = '<a href="' . $this->build_url($post->ID) . '">Edit with Rocket Editor </a>';
        return $actions;
    }

    /**
     * Build the url to edit a specific post using Rocket Editor.
     */
    private function build_url($postId = null) {
        $baseUrl = get_admin_url() . "admin.php?page=" .  MENU_SLUG . "&page_id=";
        if ($postId) {
            return $baseUrl . $postId;
        }
        global $post;
        return $baseUrl . $post->ID;
    }

    public function init_editor() {
        $editor = new RocketPageEditor();
    }
}

/**
 * If user is logged in and can edit pages, initialize plugin.
 */
add_action('init', 'rocket_editor_before_init');

function rocket_editor_before_init() {
    if (current_user_can('edit_pages')) {
        $RocketPlugin = new RocketPlugin();
    }
}

if ($RocketPlugin) {
    //activation
    register_activation_hook(__FILE__, array($RocketPlugin, 'activate'));

    //de-activation
    register_deactivation_hook(__FILE__, array($RocketPlugin, 'deactivate'));
}



  

?>