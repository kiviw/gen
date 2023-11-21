<?php
/**
 * Plugin Name: Monero Subaddress Generator
 * Description: Generates a Monero subaddress using RPC and displays it on a page.
 * Version: 1.1
 * Author: Your Name
 */

function generate_monero_subaddress() {
    ?>
    <div id="monero-subaddress-container">
        <p id="monero-subaddress-result">Click the button to generate Monero subaddress.</p>
        <button id="generate-monero-subaddress">Generate Subaddress</button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var button = document.getElementById('generate-monero-subaddress');
            var resultContainer = document.getElementById('monero-subaddress-result');

            button.addEventListener('click', function () {
                // Make an AJAX request to the server to generate Monero subaddress
                var xhr = new XMLHttpRequest();
                xhr.open('GET', '<?php echo admin_url('admin-ajax.php'); ?>?action=generate_monero_subaddress', true);

                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        // Update the result container with the generated subaddress
                        resultContainer.textContent = 'Generated Monero Subaddress: ' + xhr.responseText;
                    }
                };

                xhr.send();
            });
        });
    </script>
    <?php
}

function generate_monero_subaddress_callback() {
    // Log the start of subaddress generation
    error_log('Generating Monero subaddress...');

    $rpc_url = 'http://127.0.0.1:18080/json_rpc'; // Replace with your Monero RPC URL

    $request_body = json_encode([
        'jsonrpc' => '2.0',
        'id' => '0',
        'method' => 'create_address',
        'params' => [],
    ]);

    $response = wp_remote_post($rpc_url, [
        'body' => $request_body,
        'headers' => [
            'Content-Type' => 'application/json',
        ],
    ]);

    if (is_wp_error($response)) {
        // Log the error
        error_log('Error generating Monero subaddress: ' . $response->get_error_message());
        echo 'Error generating Monero subaddress';
    } else {
        $body = wp_remote_retrieve_body($response);
        $result = json_decode($body, true);

        if (isset($result['result']['address'])) {
            // Log the successful subaddress generation
            error_log('Generated Monero subaddress: ' . $result['result']['address']);
            echo $result['result']['address'];
        } else {
            // Log the error
            error_log('Error generating Monero subaddress: Unexpected response format');
            echo 'Error generating Monero subaddress';
        }
    }

    wp_die();
}

function register_monero_subaddress_shortcode() {
    add_shortcode('monero_subaddress', 'generate_monero_subaddress');
}

add_action('init', 'register_monero_subaddress_shortcode');

// Add AJAX action for subaddress generation
add_action('wp_ajax_generate_monero_subaddress', 'generate_monero_subaddress_callback');
