<?php

namespace MyPlugin\Shortcodes;

use MyPlugin\Services\Logger;
use MyPlugin\Services\TwigService;

class ShortcodeManager
{
    private $logger;
    private $twig;

    public function __construct(Logger $logger, TwigService $twig)
    {
        $this->logger = $logger;
        $this->twig = $twig;
        $this->registerShortcodes();
    }

    private function registerShortcodes()
    {
        add_shortcode('my_plugin_display', [$this, 'displayShortcode']);
        add_shortcode('my_plugin_form', [$this, 'formShortcode']);
    }

    public function displayShortcode($atts)
    {
        $atts = shortcode_atts([
            'type' => 'default',
            'count' => 5,
            'class' => ''
        ], $atts);

        $this->logger->info('Shortcode display appelé avec type: ' . $atts['type']);

        return $this->twig->render('shortcodes/display.twig', [
            'type' => $atts['type'],
            'count' => intval($atts['count']),
            'class' => sanitize_html_class($atts['class']),
            'items' => $this->getDisplayItems($atts['type'], $atts['count'])
        ]);
    }

    public function formShortcode($atts)
    {
        $atts = shortcode_atts([
            'action' => 'submit',
            'redirect' => '',
            'class' => ''
        ], $atts);

        return $this->twig->render('shortcodes/form.twig', [
            'action' => $atts['action'],
            'redirect' => $atts['redirect'],
            'class' => sanitize_html_class($atts['class']),
            'nonce' => wp_create_nonce('my_plugin_form_nonce')
        ]);
    }

    private function getDisplayItems($type, $count)
    {
        // Logique pour récupérer les éléments selon le type
        return [];
    }
}