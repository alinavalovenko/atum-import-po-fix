<?php

function init(){
    if ( ! file_exists( AIFC_TEMPDIR )) {
        add_action( 'admin_notices', 'aifc_create_folder_notice' );
        return false;
    }

    $f = fopen( AIFC_TEMPDIR . 'index.php','w' );
    fwrite( $f, '<?php' . PHP_EOL . '//Silence is golden' );
    fclose($f);

    return true;
}

function aifc_create_folder_notice() {
    ?>
    <div class="notice error">
        <p><b>WooCommerce ATUM Import From CSV</b></p>
        <p>Failed to create folder: <i><?php print AIFC_TEMPDIR; ?></i></p>
    </div>
    <?php
}