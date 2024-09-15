<?php

function sbs_posting_plugin_activation() {
    if ( ! wp_next_scheduled( 'sbs_posting_cron_event' ) ) {
        wp_schedule_event( time(), 'hourly', 'sbs_posting_cron_event' );
    }
}

function sbs_posting_plugin_deactivation() {
    wp_clear_scheduled_hook( 'sbs_posting_cron_event' );
}
