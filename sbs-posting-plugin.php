<?php
/*
Plugin Name: SBS Posting Plugin
Description: A plugin for cross-posting content between WordPress sites.
Version: 1.0
Author: SBS India
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

include plugin_dir_path( __FILE__ ) . 'sbs-posting-plugin-functions.php';
include plugin_dir_path( __FILE__ ) . 'sbs-posting-plugin-cross-posting.php';
include plugin_dir_path( __FILE__ ) . 'sbs-posting-plugin-activation-deactivation.php';

// Register the settings
add_action( 'admin_init', 'sbs_posting_plugin_settings_init' );
function sbs_posting_plugin_settings_init() {
    register_setting( 'sbs_posting_plugin_options', 'sbs_posting_plugin_options', 'sbs_posting_plugin_options_validate' );

    add_settings_section(
        'sbs_posting_plugin_settings_section',
        'SBS Posting Plugin Settings',
        'sbs_posting_plugin_settings_section_callback',
        'general'
    );

    add_settings_field(
        'sbs_posting_plugin_source_sites',
        'Source Sites',
        'sbs_posting_plugin_source_sites_input',
        'general',
        'sbs_posting_plugin_settings_section'
    );

    add_settings_field(
        'sbs_posting_plugin_destination_sites',
        'Destination Sites',
        'sbs_posting_plugin_destination_sites_input',
        'general',
        'sbs_posting_plugin_settings_section'
    );
}

// Settings section callback
function sbs_posting_plugin_settings_section_callback() {
    echo '<p>Enter details of source and destination sites.</p>';
}

// Source sites input callback
function sbs_posting_plugin_source_sites_input() {
    $options = get_option( 'sbs_posting_plugin_options' );
    $source_sites = isset( $options['source_sites'] ) ? $options['source_sites'] : '';
    echo '<textarea name="sbs_posting_plugin_options[source_sites]" rows="10" cols="50">' . esc_textarea( $source_sites ) . '</textarea>';
}

// Destination sites input callback
function sbs_posting_plugin_destination_sites_input() {
    $options = get_option( 'sbs_posting_plugin_options' );
    $destination_sites = isset( $options['destination_sites'] ) ? $options['destination_sites'] : '';
    echo '<textarea name="sbs_posting_plugin_options[destination_sites]" rows="10" cols="50">' . esc_textarea( $destination_sites ) . '</textarea>';
}

// Validate settings
function sbs_posting_plugin_options_validate( $input ) {
    $input['source_sites'] = sanitize_textarea_field( $input['source_sites'] );
    $input['destination_sites'] = sanitize_textarea_field( $input['destination_sites'] );
    return $input;
}

// Admin menu
add_action( 'admin_menu', 'sbs_posting_plugin_admin_menu' );
function sbs_posting_plugin_admin_menu() {
    add_options_page(
        'SBS Posting Plugin',
        'SBS Posting Plugin',
        'manage_options',
        'sbs-posting-plugin',
        'sbs_posting_plugin_settings_page'
    );
}

// Settings page
function sbs_posting_plugin_settings_page() {
    ?>
    <div class="wrap">
        <h1>SBS Posting Plugin Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'sbs_posting_plugin_options' );
            do_settings_sections( 'general' );
            submit_button();
            ?>
        </form>
        <form method="post" action="">
            <?php
            if ( isset( $_POST['sbs_posting_manual_trigger'] ) ) {
                sbs_posting_cron_job(); // Call the cross-posting function manually
                echo '<div class="updated"><p>Cross-posting triggered manually.</p></div>';
            }
            ?>
            <input type="submit" name="sbs_posting_manual_trigger" class="button button-primary" value="Trigger Cross-Posting">
        </form>
    </div>
    <?php
}

// Register the cron job
register_activation_hook( __FILE__, 'sbs_posting_plugin_activation' );
register_deactivation_hook( __FILE__, 'sbs_posting_plugin_deactivation' );
add_action( 'sbs_posting_cron_event', 'sbs_posting_cron_job' );
