<?php

class RocketPageEditor {

    /** 
     * Page id 
    */
    public $page_id;

    /** 
     * The real url of the page 
     */
    public $page_url;


    public function __construct(){
        //TODO: Redirect to proper page
        if (!current_user_can('edit_pages')) { //Editors and above. 
            wp_redirect(get_admin_url()); 
            exit;
        }

        if (!isset($_GET["page_id"])) {
            wp_redirect(get_admin_url());  
            exit; //If we don't manually exit here, code could continue to execute
        }

        $this->page_id = $_GET["page_id"];
        $this->page_url = get_permalink($this->page_id);
        $this->build();
    }

    private function enqueue_dependencies() {
        wp_enqueue_script(BASENAME . "vuejs", ROCKET_BASE_URL . "assets/js/vue.js");
        wp_enqueue_script(BASENAME . "app", ROCKET_BASE_URL . "assets/js/app.js"); //$ defined here 
        wp_enqueue_script(BASENAME . "handle-wp-elements", ROCKET_BASE_URL . "assets/js/handleWpElements.js");
        wp_enqueue_style(BASENAME . "bootstrap", ROCKET_BASE_URL .  "assets/css/bootstrap.min.css"); //TODO: verify that bootstrap hasn't already been loaded before loading bootstrap
        wp_enqueue_style(BASENAME . "styles", ROCKET_BASE_URL . "assets/css/rocket.css");
    }

    private function get_user_nonce(){
        return wp_create_nonce("wp_rest");
    }

    private function output_editor_html() {
        ?>
        <html>
            <body>
                <div id="app">
                    <div id="rocketEditor">
                        <iframe id="rocketEditorContent" src="<?php echo $this->page_url; ?>"></iframe>
                        <div id="toolsetContainer">
                            <h1> Editor </h1>
                            <p> Click on an element to modify it </p>
                            <textarea id="textTool" placeholder="Enter new content for element" v-model="textToolContent"></textarea>
                            <div id="drillbits">
                                <div id="toolsetButtons">
                                    <button @click="exportHtml(<?php echo $this->page_id ?>)" color="primary"> Save </button>
                                    <button> <a href="<?php echo get_admin_url(null, "/edit.php?post_type=page");?>"> Back  </a></button>
                                </div>
                                <input id="nonce" type="hidden" value="<?php echo $this->get_user_nonce();?>"> 
                                <input id="siteUrl" type="hidden" value="<?php echo get_site_url(); ?>">
                            </div>
                            <p v-if="showPageLink"> Page updated ! </br> Click <a href="<?php echo $this->page_url; ?>" target="_blank"> here </a> to view. </p>
                        </div>
                    </div>
                </div>
            </body>
        </html>
        <?php
    }

    public function build() {
        $this->enqueue_dependencies();
        $this->output_editor_html();
    }

}

?>