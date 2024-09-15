<?php

function sbs_posting_plugin_process_source_sites() {
    $options = get_option( 'sbs_posting_plugin_options' );
    $source_sites = isset( $options['source_sites'] ) ? json_decode( $options['source_sites'], true ) : array();
    if ( ! is_array( $source_sites ) ) {
        $source_sites = array();
    }
    return $source_sites;
}

function sbs_posting_plugin_process_destination_sites() {
    $options = get_option( 'sbs_posting_plugin_options' );
    $destination_sites = isset( $options['destination_sites'] ) ? json_decode( $options['destination_sites'], true ) : array();
    if ( ! is_array( $destination_sites ) ) {
        $destination_sites = array();
    }
    return $destination_sites;
}

function sbs_posting_cron_job() {
    $source_sites = sbs_posting_plugin_process_source_sites();
    $destination_sites = sbs_posting_plugin_process_destination_sites();
    foreach ( $source_sites as $source ) {
        $source_url = isset( $source['url'] ) ? esc_url( $source['url'] ) : '';
        $source_username = isset( $source['username'] ) ? sanitize_user( $source['username'] ) : '';
        $source_password = isset( $source['password'] ) ? sanitize_text_field( $source['password'] ) : '';
        foreach ( $destination_sites as $destination ) {
            $destination_url = isset( $destination['url'] ) ? esc_url( $destination['url'] ) : '';
            $destination_username = isset( $destination['username'] ) ? sanitize_user( $destination['username'] ) : '';
            $destination_password = isset( $destination['password'] ) ? sanitize_text_field( $destination['password'] ) : '';
            if ( ! empty( $source_url ) && ! empty( $destination_url ) && ! empty( $source_username ) && ! empty( $source_password ) && ! empty( $destination_username ) && ! empty( $destination_password ) ) {
                sbs_posting_cross_post( $source_url, $source_username, $source_password, $destination_url, $destination_username, $destination_password );
            }
        }
    }
}
