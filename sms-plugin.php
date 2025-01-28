<?php
/*
Plugin Name: Text Notification Plugin (Telegram Version)
Plugin URI: https://amigoo.in/
Description: Sends Telegram messages when user submits their info and views products.
Version: 1.0
Author: Abhitej Vissamsetty
Author URI: https://amigoo.in/
License: GPL2
*/

// Hook to add settings page in WordPress Admin
function sms_plugin_add_settings_page() {
    add_options_page(
        'Telegram Settings', // Page title
        'Telegram Settings', // Menu title
        'manage_options',    // Capability required to access the settings page
        'sms-telegram-settings', // Menu slug
        'sms_plugin_render_settings_page' // Function to render the settings page
    );
}
add_action('admin_menu', 'sms_plugin_add_settings_page');

// Render settings page
function sms_plugin_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Telegram Notification Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('sms_plugin_settings_group'); // Output settings fields
            do_settings_sections('sms-telegram-settings'); // Display settings sections
            submit_button(); // Submit button
            ?>
        </form>
    </div>
    <?php
}


// Hook to register settings
function sms_plugin_register_settings() {
    // Register settings for token and chat ID
    register_setting('sms_plugin_settings_group', 'sms_plugin_telegram_token');
    register_setting('sms_plugin_settings_group', 'sms_plugin_telegram_chat_id');
    
    // Add a section
    add_settings_section(
        'sms_plugin_telegram_section',
        'Telegram Bot Settings',
        'sms_plugin_telegram_section_callback',
        'sms-telegram-settings'
    );

    // Add settings fields
    add_settings_field(
        'sms_plugin_telegram_token',
        'Telegram Bot API Token',
        'sms_plugin_telegram_token_field',
        'sms-telegram-settings',
        'sms_plugin_telegram_section'
    );

    add_settings_field(
        'sms_plugin_telegram_chat_id',
        'Telegram Chat ID',
        'sms_plugin_telegram_chat_id_field',
        'sms-telegram-settings',
        'sms_plugin_telegram_section'
    );
}
add_action('admin_init', 'sms_plugin_register_settings');

// Section callback
function sms_plugin_telegram_section_callback() {
    echo 'Enter your Telegram bot API token and chat ID below to enable Telegram notifications.';
}

// Token field callback
function sms_plugin_telegram_token_field() {
    $token = get_option('sms_plugin_telegram_token');
    echo '<input type="text" name="sms_plugin_telegram_token" value="' . esc_attr($token) . '" class="regular-text" />';
}

// Chat ID field callback
function sms_plugin_telegram_chat_id_field() {
    $chat_id = get_option('sms_plugin_telegram_chat_id');
    echo '<input type="text" name="sms_plugin_telegram_chat_id" value="' . esc_attr($chat_id) . '" class="regular-text" />';
}

// Hook to enqueue scripts and styles
function sms_plugin_enqueue_scripts() {
    wp_enqueue_script('sms-plugin-js', plugin_dir_url(__FILE__) . 'js/sms-plugin.js', array('jquery'), null, true);

    // Localize script to define ajaxurl for the frontend
    wp_localize_script('sms-plugin-js', 'ajaxurl', admin_url('admin-ajax.php'));

    wp_enqueue_style('sms-plugin-css', plugin_dir_url(__FILE__) . 'css/sms-plugin.css');
}
add_action('wp_enqueue_scripts', 'sms_plugin_enqueue_scripts');

// Create the modal popup to collect user information
function sms_plugin_create_modal() {
    // Check if the user has already filled out the form by checking for the cookie
    if (!isset($_COOKIE['sms_user_subscribed'])) {
        ?>
        <div id="sms-modal">
            <div class="modal-content">
                <!-- Close button -->
                <span class="close-btn" onclick="document.getElementById('sms-modal').style.display='none'">&times;</span>

                <h2>Subscribe Now</h2>
                <p>Subscribe and get the best offers from us directly to your WhatsApp</p>

                <form id="sms-form">
                    <input type="text" name="name" placeholder="Your Name" required />
                    <input type="tel" name="phone" placeholder="Your Phone Number" required />
                    <button type="submit">Add me to the List</button>
                </form>
            </div>
        </div>
        <?php
    }
}
add_action('wp_footer', 'sms_plugin_create_modal');

// Handle form submission and send Telegram message
function sms_plugin_handle_form_submission() {
    if (isset($_POST['name']) && isset($_POST['phone'])) {
        $name = sanitize_text_field($_POST['name']);
        $phone = sanitize_text_field($_POST['phone']);

        // Send a welcome message to Telegram
        sms_plugin_send_telegram_message("Customer $name with phone number $phone is visiting our website!");

        // Store user info in the database or session (use cookies here as an example)
        setcookie('sms_user_info', json_encode(['name' => $name, 'phone' => $phone]), time() + 3600, "/");

        // Set a cookie indicating that the user has already submitted the form
        setcookie('sms_user_subscribed', 'true', time() + 3600, "/");

        // Respond back to AJAX request
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    wp_die();
}
add_action('wp_ajax_sms_form_submission', 'sms_plugin_handle_form_submission');
add_action('wp_ajax_nopriv_sms_form_submission', 'sms_plugin_handle_form_submission');


// Function to send Telegram message
function sms_plugin_send_telegram_message($message) {
    // Get the bot API token and chat ID from the WordPress options table
    $bot_api_token = get_option('sms_plugin_telegram_token');
    $chat_id = get_option('sms_plugin_telegram_chat_id');

    if (empty($bot_api_token) || empty($chat_id)) {
        error_log('Telegram API Token or Chat ID is missing.');
        return;
    }

    // API URL for Telegram bot
    $api_url = "https://api.telegram.org/bot$bot_api_token/sendMessage";
    
    // Parameters for the request
    $params = [
        'chat_id' => $chat_id,
        'text' => $message,
    ];

    // Send the message via Telegram Bot API
    $response = wp_remote_post($api_url, [
        'method'    => 'POST',
        'body'      => $params
    ]);

    // Debugging: log the response for troubleshooting
    if (is_wp_error($response)) {
        error_log("Error sending Telegram message: " . $response->get_error_message());
    } else {
        error_log("Telegram response: " . print_r($response, true));
    }
}



// Function to send Telegram message when a product page is viewed
function sms_plugin_send_telegram_on_product_view() {
    if (isset($_COOKIE['sms_user_info'])) {
        // Retrieve user info from cookie
        $user_info = json_decode(stripslashes($_COOKIE['sms_user_info']), true);
        $name = $user_info['name'];
        $phone = $user_info['phone'];

        // Check if the product page is visited for the first time
        $product_viewed = isset($_COOKIE['product_viewed_' . get_the_ID()]) ? true : false;

        if (!$product_viewed && is_product()) {
            // Mark product as viewed
            setcookie('product_viewed_' . get_the_ID(), 'true', time() + 3600, "/");

            // Retrieve product details
            global $post;
            $product_name = get_the_title($post->ID);  // Get the product name
            $product_url = get_permalink($post->ID);   // Get the product URL

            // Construct the Telegram message
            $message = " $name, is viewing '$product_name'. You can visit the product page here: $product_url his phone number is $phone";

            // Send the message to Telegram
            sms_plugin_send_telegram_message($message);
        }
    }
}
add_action('wp_head', 'sms_plugin_send_telegram_on_product_view');