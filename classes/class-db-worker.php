<?php

use Atum\Inc\Helpers;

if ( ! class_exists( 'AIFC_DB_Worker' ) ) {

    class AIFC_DB_Worker{

        private $insert_product_error   = array();

        private $added_product_count    = 0;

        private $po_id                  = null;

        function __construct(){

        }

        public function insert_purchase_orders( $data ){
            if ( ! $this->has_errors()  ) {
                $description = sprintf(
                    "Packing List (%s) %s \nTotal quantity: %d \nTotal SKUs: %d",
                    $data['filepath'],
                    date( 'd-m-Y' ),
                    $data['total_qty'],
                    $data['product_total']
                );

                $args = array(
                    'post_type'     => 'atum_purchase_order',
                    'post_status'   => 'atum_pending',
                    'post_content'  => $description
                );

                $po_post_id = wp_insert_post( $args, true );

                if ( ! is_wp_error( $po_post_id ) ) {
                    $this->po_id = $po_post_id;
                    $this->added_product_count++;

                    add_post_meta( $po_post_id, '_multiple_suppliers', 'yes', true );
                    add_post_meta( $po_post_id, '_status', 'pending', true );

                    $this->products_exists( $data['product_data'] );

                }

            }
        }

        private function products_exists( $product_data ){
            if ( empty( $product_data ) || ! is_array( $product_data ) ) {
                return false;
            }

            foreach ( $product_data as $sku => $value ) {
                $product_id = wc_get_product_id_by_sku( $sku );
                if ( ! $product_id ) {
                    $args = array(
                        'post_type'     => 'product',
                        'post_title'    =>  $value['title']
                    );

                    $product_id = wp_insert_post( $args, true );


                    if ( is_wp_error( $product_id )) {
                        $this->insert_product_error[] = $sku;
                        continue;
                    }else{
                        $this->added_product_count++;
                        add_post_meta( $product_id, '_sku', $sku, true );
                    }

                };

                $atum_order = Helpers::get_atum_order_model( $this->po_id );

                if ( is_wp_error($atum_order) ) {
                    continue;
                }

                $item    = $atum_order->add_product( wc_get_product( $product_id ), $value['qty'] );

                add_post_meta( $product_id, "_w8_atum_purchase_order_{$this->po_id}", $value['qty'], true );

            }

            do_action( 'aifc_done', $this->has_errors() );

        }


        public function has_errors(){
            return ! empty( $this->insert_product_error );
        }

    }

}

return new AIFC_DB_Worker();