<?php

namespace MyPlugin\Frontend;

use MyPlugin\Services\Logger;
use MyPlugin\Services\TwigService;

class FrontendController
{
    private $logger;
    private $twig;

    public function __construct(Logger $logger, TwigService $twig)
    {
        $this->logger = $logger;
        $this->twig = $twig;
        $this->init();
    }

    private function init()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueFrontendAssets']);
        add_action('wp_ajax_nopriv_my_plugin_frontend', [$this, 'handleFrontendAjax']);
        add_action('wp_ajax_my_plugin_frontend', [$this, 'handleFrontendAjax']);
        add_filter('the_content', [$this, 'filterContent']);
    }

    public function enqueueFrontendAssets()
    {
        wp_enqueue_style('my-plugin-frontend', MY_PLUGIN_URL . 'assets/css/frontend.css', [], MY_PLUGIN_VERSION);
        wp_enqueue_script('my-plugin-frontend', MY_PLUGIN_URL . 'assets/js/frontend.js', ['jquery'], MY_PLUGIN_VERSION, true);
        
        wp_localize_script('my-plugin-frontend', 'myPluginAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('my_plugin_frontend_nonce')
        ]);
    }

    public function handleFrontendAjax()
    {
        check_ajax_referer('my_plugin_frontend_nonce', 'nonce');
        
        $this->logger->info('Action AJAX frontend exécutée');
        
        wp_send_json_success(['data' => 'Réponse frontend']);
    }

    public function filterContent($content)
    {
        if (is_single() && in_the_loop() && is_main_query()) {
            $additionalContent = $this->twig->render('frontend/content-addon.twig', [
                'post_id' => get_the_ID()
            ]);
            $content .= $additionalContent;
        }
        
        return $content;
    }
}