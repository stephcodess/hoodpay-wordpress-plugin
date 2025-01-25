<?php
/**
 * Plugin Name: HoodPay Gateway
 * Plugin URI: https://yourwebsite.com
 * Description: Accept payments via HoodPay (debit cards and cryptocurrency) in WordPress.
 * Version: 1.0.0
 * Author: Raji Olaoluwa Segun
 * Author URI: https://www.linkedin.com/in/olaoluwa-raji-14a5681b8/
 * Text Domain: hoodpay-gateway
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Add admin menu for HoodPay settings and transactions.
 */
function hoodpay_add_admin_menu()
{
    add_menu_page(
        __('HoodPay Transactions', 'hoodpay-gateway'),
        __('HoodPay Transactions', 'hoodpay-gateway'),
        'manage_options',
        'hoodpay-transactions',
        'hoodpay_render_transactions_page',
        'dashicons-analytics',
        26
    );

    add_submenu_page(
        'options-general.php',
        __('HoodPay Settings', 'hoodpay-gateway'),
        __('HoodPay Settings', 'hoodpay-gateway'),
        'manage_options',
        'hoodpay-settings',
        'hoodpay_render_settings_page'
    );
}
add_action('admin_menu', 'hoodpay_add_admin_menu');

/**
 * Render the admin transactions page.
 */
function hoodpay_render_transactions_page()
{
    $transactions = hoodpay_fetch_transactions_from_api();

    ?>
    <div class="wrap">
        <h1><?php _e('HoodPay Transactions', 'hoodpay-gateway'); ?></h1>
        <?php if (!$transactions['success']): ?>
            <p style="color: red;"><?php echo esc_html($transactions['message']); ?></p>
        <?php else: ?>
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Payment ID', 'hoodpay-gateway'); ?></th>
                        <th><?php _e('Amount', 'hoodpay-gateway'); ?></th>
                        <th><?php _e('Currency', 'hoodpay-gateway'); ?></th>
                        <th><?php _e('Status', 'hoodpay-gateway'); ?></th>
                        <th><?php _e('Date', 'hoodpay-gateway'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions['data'] as $transaction): ?>
                        <tr>
                            <td><?php echo esc_html($transaction['id']); ?></td>
                            <td><?php echo esc_html($transaction['amount']); ?></td>
                            <td><?php echo esc_html($transaction['currency']); ?></td>
                            <td><?php echo esc_html($transaction['status']); ?></td>
                            <td><?php echo esc_html(date('Y-m-d H:i:s', strtotime($transaction['createdAt']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Fetch transactions from HoodPay API.
 */
function hoodpay_fetch_transactions_from_api()
{
    $api_key = get_option('hoodpay_api_key');
    $business_id = get_option('hoodpay_business_id');

    if (!$api_key || !$business_id) {
        return [
            'success' => false,
            'message' => __('API Key or Business ID is not configured.', 'hoodpay-gateway'),
        ];
    }

    $url = "https://api.hoodpay.io/v1/businesses/{$business_id}/payments";

    $headers = [
        'Authorization' => 'Bearer ' . $api_key,
        'Content-Type' => 'application/json',
    ];

    $response = wp_remote_get($url, ['headers' => $headers]);

    if (is_wp_error($response)) {
        return [
            'success' => false,
            'message' => __('Failed to fetch transactions. Please try again later.', 'hoodpay-gateway'),
        ];
    }

    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($response_body['error'])) {
        return [
            'success' => false,
            'message' => $response_body['error'],
        ];
    }

    return [
        'success' => true,
        'data' => $response_body,
    ];
}

/**
 * Render HoodPay settings page.
 */
function hoodpay_render_settings_page()
{
    if (isset($_POST['hoodpay_save_settings'])) {
        update_option('hoodpay_api_key', sanitize_text_field($_POST['hoodpay_api_key']));
        update_option('hoodpay_business_id', sanitize_text_field($_POST['hoodpay_business_id']));
    }

    $api_key = get_option('hoodpay_api_key', '');
    $business_id = get_option('hoodpay_business_id', '');

    ?>
    <div class="wrap">
        <h1><?php _e('HoodPay Settings', 'hoodpay-gateway'); ?></h1>
        <form method="POST">
            <table class="form-table">
                <tr>
                    <th><?php _e('API Key', 'hoodpay-gateway'); ?></th>
                    <td>
                        <input type="text" name="hoodpay_api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Business ID', 'hoodpay-gateway'); ?></th>
                    <td>
                        <input type="text" name="hoodpay_business_id" value="<?php echo esc_attr($business_id); ?>" class="regular-text" />
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button type="submit" name="hoodpay_save_settings" class="button-primary">
                    <?php _e('Save Changes', 'hoodpay-gateway'); ?>
                </button>
            </p>
        </form>
    </div>
    <?php
}

/**
 * Enqueue frontend assets.
 */
function hoodpay_enqueue_assets()
{
    wp_enqueue_script('hoodpay-script', plugin_dir_url(__FILE__) . 'assets/js/hoodpay.js', [], '1.0.0', true);
    wp_localize_script('hoodpay-script', 'hoodpay_settings', [
        'api_key' => get_option('hoodpay_api_key'),
        'business_id' => get_option('hoodpay_business_id'),
        'payment_url' => 'https://api.hoodpay.io/v1/businesses/',
    ]);
}
add_action('wp_enqueue_scripts', 'hoodpay_enqueue_assets');

/**
 * Render the payment button.
 */
function hoodpay_render_payment_button()
{
    ?>
    <button id="hoodpay-initiate-payment" class="button"><?php _e('Pay with HoodPay', 'hoodpay-gateway'); ?></button>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const initiatePayment = document.getElementById('hoodpay-initiate-payment');

            initiatePayment.addEventListener('click', () => {
                const apiKey = hoodpay_settings.api_key;
                const businessId = hoodpay_settings.business_id;
                const url = `${hoodpay_settings.payment_url}${businessId}/payments`;

                const data = {
                    name: 'Payment for Order',
                    description: 'Order description',
                    currency: 'USD',
                    amount: 100,
                    redirectUrl: window.location.origin + '/payment-success',
                };

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${apiKey}`,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                })
                    .then(response => response.json())
                    .then(result => {
                        if (result.paymentUrl) {
                            window.location.href = result.paymentUrl;
                        } else {
                            alert(result.message || 'Failed to create payment.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
            });
        });
    </script>
    <?php
}
add_shortcode('hoodpay_payment', 'hoodpay_render_payment_button');
