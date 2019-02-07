<?php

if ( ! class_exists( 'AIFC_Upload_File' ) ) {

    class AIFC_Upload_File{

        private $allowed_file_types = array(
            'text/comma-separated-values',
            'text/csv',
            'application/csv',
            'application/excel',
            'application/vnd.ms-excel',
            'application/octet-stream',
            'text/plain'
        );

        private $file               = null;
        private $upload_page_url    = null;

        function __construct(){
            $this->upload_page_url  = admin_url( 'admin.php?page=import_csv' );

            add_action( 'wp_ajax_upload_file_handler', array( $this, 'upload_file_handler' ) );
        }

        public function upload_file_handler(){
        	$data = $_FILES;
	        if ( $this->is_allowed_file_type() && $this->save_file() ) {
		        echo 'success';
	        } else{
		        echo 'unsuccess';

	        }
        	wp_die();
        }


        private function is_allowed_file_type(){
            return in_array( $_FILES['imports_csv']['type'], $this->allowed_file_types );
        }

        private function save_file(){
            if ( ! function_exists( 'wp_handle_upload' ) ) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
            }

            add_filter( 'upload_dir', array( $this, 'change_upload_dir' ), 10, 1 );

            $movefile = wp_handle_upload( $_FILES['imports_csv'], array( 'test_form' => false ) );

            remove_filter( 'upload_dir', array( $this, 'change_upload_dir' ), 10, 1 );

            if ( $movefile && empty( $movefile['error'] ) ) {
                $this->file = $movefile['file'];

                do_action( 'aifc_after_save_file', $this->file );

                return true;
            }

            return false;

        }

        public function change_upload_dir( $params ){
            $params['path']     = $params['basedir'] = rtrim( AIFC_TEMPDIR, DIRECTORY_SEPARATOR );
            $params['subdir']   = '';

            return $params;
        }

        public function get_file_path(){
            return $this->file;
        }

    }

}

return new AIFC_Upload_File();