<?php

if ( ! class_exists( 'AIFC_Parse_CSV' ) ) {

    class AIFC_Parse_CSV{

        private $file_content   = array();

        private $filtered_data  = array();

        private $total_qty      = 0;
        private $product_total  = 0;

        function __construct(  ){

        }

        public function init( $path ){
            if ( $this->read_file( $path ) ) {
                $this->filter_data();
                unlink( $path );

                return true;
            }

            return false;
        }

        public function read_file( $path ){

            if ( ! file_exists( $path ) ) {
                return false;
            }

            $this->file_content = array_map( 'str_getcsv', file( $path ) );

            return true;

        }

        private function filter_data(){
            $last_key = null;

            foreach ( $this->file_content as $str_file ) {

                $this->total_qty += $str_file[2];

                if ( ! empty( $str_file[0] ) ) {
                    $last_key = $str_file[0];
                }

                if ( empty( $str_file[0] ) && ! empty( $this->filtered_data ) ) {

                    $this->filtered_data[$last_key]['qty'] += $str_file[2];

                    continue;
                }

                $this->product_total++;

                if ( in_array( $str_file[0], array_keys( $this->filtered_data ) ) ) {
                    $this->filtered_data[$str_file[0]]['qty'] += $str_file[2];
                    continue;
                }

                $this->filtered_data[$str_file[0]] = array(
                    'title' => empty( $str_file[1] ) ? $str_file[0] : $str_file[1],
                    'qty'   => $str_file[2]
                );

            }


        }

        public function get_data(){
            return $this->filtered_data;
        }

        public function get_total(){
            return $this->total_qty;
        }

        public function get_product_total(){
            return $this->product_total;
        }

    }

}

return new AIFC_Parse_CSV();