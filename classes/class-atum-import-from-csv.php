<?php

if ( ! class_exists( 'ATUM_Import_From_CSV' ) ) {

    class ATUM_Import_From_CSV{
        private $uploader   = null;
        private $parser     = null;
        private $db_worker  = null;

        function __construct(){

            $this->uploader     = require_once AIFC_CLASSESDIR . 'class-aifc-upload-file.php';
            $this->parser       = require_once AIFC_CLASSESDIR . 'class-aifc-parse-csv.php';
            $this->db_worker    = require_once AIFC_CLASSESDIR . 'class-db-worker.php';

            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
            add_action( 'admin_menu', array( $this, 'create_submenu_item' ) );
            add_action( 'aifc_after_save_file', array( $this, 'init' ), 10, 1 );
            add_action( 'aifc_done', array( $this, 'done' ), 10, 1 );

        }

        public function enqueue_scripts(){
            if ( preg_match( '/.*edit\.php\?.*post_type=atum_purchase_order.*/', $_SERVER['REQUEST_URI'] ) ) {
                wp_enqueue_script( 'aifc-script', plugins_url( 'marko-import_po/assets/aifc.js'), array( 'jquery' ), null, true );

                wp_localize_script( 'aifc-script', 'aifc', array(
                    'link' => admin_url( 'admin.php?page=import_csv' )
                ) );
            }
        }

        public function create_submenu_item(){
            add_submenu_page( 'atum-dashboard', 'Import from CSV', 'Import from CSV', 'manage_options', 'import_csv', array( $this, 'render_page' ) );
        }

        public function render_page(){
            if ( isset( $_GET['aifc_status'] ) ) {
                switch ( $_GET['aifc_status'] ) {
                    case 1:
                        echo '<div class="notice notice-warning" style="padding:10px 15px;">Invalid file. Please try again.</div>';
                        break;
                    case 2:
                        echo '<div class="notice notice-warning" style="padding:10px 15px;">There was a problem writing data to the database. Please try again.</div>';
                        break;
                }
            }

            ?>
            <div class="wrap">
                <h2><?php echo get_admin_page_title(); ?></h2>

                <form enctype="multipart/form-data" action="<?php echo get_option( 'siteurl' ); ?>/aifc-upload-csv" method="POST">
                    <?php wp_nonce_field( 'imports_csv', 'fileup_nonce' ); ?>
                    <input name="imports_csv" class="" type="file" required/>
                    <input type="submit" class="button button-primary button-large" name="upload" value="Upload" />
                </form>
            </div>
            <?php
        }

        public function done( $status ){
            $redirect_url = $status ? $this->uploader->upload_page_url . '&aifc_status=2' : admin_url( 'edit.php?post_type=atum_purchase_order' ) ;

            wp_redirect( admin_url( $redirect_url ) );
        }

        public function init( $path ){
            if ( $this->parser->init( $path ) ) {
                $data = array(
                        'filepath'      => basename( $this->uploader->get_file_path(), '.csv' ),
                        'total_qty'     => $this->parser->get_total(),
                        'product_total' => $this->parser->get_product_total(),
                        'product_data'  => $this->parser->get_data()
                );
                $this->db_worker->insert_purchase_orders( $data );
            };
        }

    }

}

return new ATUM_Import_From_CSV();