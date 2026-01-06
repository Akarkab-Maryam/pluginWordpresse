<?php
namespace MyPlugin\Admin;
use MyPlugin\Services\Logger;
use MyPlugin\Services\TwigService;
USE MyPlugin\Models\SalesforceConfig;
USE MyPlugin\Services\SalesforceService;
use MyPlugin\Services\EmailService;

class AdminController
{
    private $logger;
    private $twig;
    private $option_name = 'my_plugin_settings';

    public function __construct(Logger $logger, TwigService $twig) //éXécusion de la fonction pour lir les logs de plugins et Affichié les pages
    {
        $this->logger = $logger;
        $this->twig = $twig;
        $this->init();
    }

    private function init()
    {
        add_action('admin_menu', [$this, 'addAdminMenu']); // hook qui ajoute le menue
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']); // hook qui charge les fichiers styles
        add_action('admin_post_save_my_plugin_settings', [$this, 'saveSettings']);// hook qui traite le formulaire
        add_action('wp_ajax_test_salesforce_connection',[$this,'testSalesforceConnection']); //Enregistre une action Ajax
        add_action('wp_ajax_download_salesforce_report',[$this,'downloadSalesforceReport']);//hook pour téléchargemment de rapport
         add_action('admin_post_save_gmail_settings', [$this, 'handleSaveGmailSettings']);

    }

    public function addAdminMenu() //Ajouter le plugins dans wordpresse
    {
        //Maryam
        add_menu_page(
            'Plugin Maryam',
            'Plugin Maryam',
            'manage_options',
            'my-plugin',
            [$this, 'adminPage'],
            'dashicons-admin-generic'
        );
    }

    public function adminPage()// récupéré les valeurs sasie par l'utulisateur
{
    // Utiliser SalesforceConfig pour lire les données déchiffrées
    $config = new SalesforceConfig();
    
    $settings = [
        'instance' => $config->getInstance(),
        'client_id' => $config->getClientID(),
        'client_secret' => $config->getClientSecret(),
        'User_namme' => $config->getUsername(),
        'pass' => $config->getPassword(),
        'report_id' => $config->getReportId(),
        'HostUrl' => $config->getHostUrl(),
        'UserSecret' => $config->getUserSecret(),
        'gmail_client_id' => $config->getGmailClientId(),
        'gmail_client_secret' => $config->getGmailClientSecret(),
        'gmail_refresh_token' => $config->getGmailRefreshToken(),
   
    ];

    // Récupérer les statistiques du plugin pour les widgets
   $stats = get_option('my_plugin_stats');
  if (!is_array($stats)) {
    $stats = [];
 }
  $stats = array_merge([
    'total_connections' => 0,
    'total_reports' => 0,
    'last_report_date' => '',
    'last_connection_status' => 'unknown',
    'successful_connections' => 0
  ], $stats);

  

        // Déterminer le label du statut de connexion (Affichage de résultat de conenxion selsforce)
        $connection_status_label = 'Non testé';
        if ($stats['last_connection_status'] === 'success') {
            $connection_status_label = 'Connecté';
        } elseif ($stats['last_connection_status'] === 'error') {
            $connection_status_label = 'Erreur';
        }
        
        //donnés envoyés au twig pour affichié Formulaire
        $data = [
            'title' => 'Administration Plugin Maryam',
            'settings' => $settings,
            'nonce_field' => wp_nonce_field('save_my_plugin_settings', 'my_plugin_nonce', true, false),
            'admin_url' => admin_url(),
            // Données pour les widgets du dashboard
            'connection_status' => $stats['last_connection_status'],
            'connection_status_label' => $connection_status_label,
            'total_connections' => $stats['total_connections'],
            'total_reports' => $stats['total_reports'],
            'last_report_date' => $stats['last_report_date'] ?: 'Aucun'
        ];
        
        //si settings-updated existe ==>le sauvegard est éffectué (settings-updated=true est ajouté au momment de la redirection)
        if (isset($_GET['settings-updated'])) {
            $data['success_message'] = 'Paramètres enregistrés avec succès !';
        }
        
        echo $this->twig->render('admin/main.twig', $data);//chargemment de la template+injection des donnés$data+affichage de html
    }

    public function saveSettings()
    {
        if (!current_user_can('manage_options')) {
            wp_die('Vous n\'avez pas les permissions nécessaires');
        }

        if (!isset($_POST['my_plugin_nonce']) || !wp_verify_nonce($_POST['my_plugin_nonce'], 'save_my_plugin_settings')) {
            wp_die('Erreur de sécurité');
        }

     
     // Récupérer et nettoyer les données
$config = new \MyPlugin\Models\SalesforceConfig();
$settings = [
   'instance'     => $config->encrypt(sanitize_text_field($_POST['my_plugin_settings']['instance'] ?? '')),
   'client_id'    => $config->encrypt(sanitize_text_field($_POST['my_plugin_settings']['client_id'] ?? '')),
   'client_secret'=> $config->encrypt(sanitize_text_field($_POST['my_plugin_settings']['client_secret'] ?? '')),
   'User_namme'   => $config->encrypt(sanitize_text_field($_POST['my_plugin_settings']['User_namme'] ?? '')),
   'pass'         => $config->encrypt(sanitize_text_field($_POST['my_plugin_settings']['pass'] ?? '')),
   'report_id'    => $config->encrypt(sanitize_text_field($_POST['my_plugin_settings']['report_id'] ?? '')),
   'HostUrl'      => $config->encrypt(sanitize_text_field($_POST['my_plugin_settings']['HostUrl'] ?? '')),
   'UserSecret'   => $config->encrypt(sanitize_text_field($_POST['my_plugin_settings']['UserSecret'] ?? '')),
    
];


        // Enregistrer dans wp_options
        update_option($this->option_name, $settings);
        
        $this->logger->info('Paramètres enregistrés avec succès');
        
        // Rediriger avec message de succès
        wp_redirect(add_query_arg('settings-updated', 'true', admin_url('admin.php?page=my-plugin')));
        exit;
    }

    public function enqueueAdminAssets()
    {

        //echo '<pre>URL CSS: ' . MY_PLUGIN_URL . 'assets/css/admin.css</pre>';
        wp_enqueue_style('my-plugin-admin', MY_PLUGIN_URL . 'assets/css/admin.css', [], time());
        //wp_enqueue_style('my-plugin-admin', MY_PLUGIN_URL . 'assets/css/admin.css', [], MY_PLUGIN_VERSION);//charge le style css pour styler le dashbord
        wp_enqueue_script('my-plugin-admin', MY_PLUGIN_URL . 'assets/js/admin-salesforce.js', ['jquery'], MY_PLUGIN_VERSION, true);//charge le style js pour géré les actions ( boutton -ajax)

        wp_localize_script('my-plugin-admin', 'myPluginAjax', [//myPluginAjax:UrL POUR Envoyer des requetes AJax a wordpresse , my-plugin-admin : le nom de script js qui va recevoir les info php ) 
            'ajaxurl' => admin_url('admin-ajax.php') //Transformer les informations php en javascripte 
        ]);
    }

     
    
    //teste de connexion salesforce+envoie des results
    public function testSalesforceConnection()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json([
                'success' => false,
                'message' => 'Permissions insuffisantes'
            ]);
        }
     
        $config = new SalesforceConfig();
        $emailService = new EmailService($this->twig);
        $service = new SalesforceService($config, $emailService);
        $result = $service->testConnection();
   

        // Mettre à jour les statistiques pour les widgets
        $stats = get_option('my_plugin_stats', [
            'total_connections' => 0,
            'total_reports' => 0,
            'last_report_date' => '',
            'last_connection_status' => 'unknown'
        ]);

        if ($result['success']) {
            $stats['total_connections'] = $stats['total_connections'] + 1;
            $stats['last_connection_status'] = 'success';
        } else {
            $stats['last_connection_status'] = 'error';
        }

        update_option('my_plugin_stats', $stats);

        wp_send_json([
            'success' => $result['success'],
            'message' => $result['message']
        ]);
    }
    //

    public function downloadSalesforceReport()
    {
    
    
    if (!current_user_can('manage_options'))


        if (!current_user_can('manage_options')) {
            wp_send_json([
                'success' => false,
                'message' => 'Permissions insuffisantes'
            ]);
        }
        $config = new SalesforceConfig();
        $emailService = new EmailService($this->twig);
        $service = new SalesforceService($config, $emailService);
        
        //téléchargemment  de rapport
        $result = $service->downloadReport();

        // Mettre à jour les statistiques pour les widgets
        $stats = get_option('my_plugin_stats', [
            'total_connections' => 0,
            'total_reports' => 0,
            'last_report_date' => '',
            'last_connection_status' => 'unknown'
        ]);

        if ($result['success']) {
            $stats['total_reports'] = $stats['total_reports'] + 1;
            $stats['last_report_date'] = date('d/m/Y H:i');
        }

        update_option('my_plugin_stats', $stats);

        if ($result['success']) {
            wp_send_json([
                'success' => true,
                'file_url' => $result['file_url'],
                'message' => $result['message']
            ]);
        } else {
            wp_send_json([
                'success' => false,
                'message' => $result['message']
            ]);
        }
     
    }


/**
 * Sauvegarder les credentials Gmail
 */
public function handleSaveGmailSettings()
{
    // Vérification du nonce pour la sécurité
    if (!isset($_POST['my_plugin_nonce']) || !wp_verify_nonce($_POST['my_plugin_nonce'], 'save_my_plugin_settings')) {
        wp_die('❌ Erreur de sécurité');
    }
    
    try {
        // Récupérer les données du formulaire
        $gmail_data = $_POST['my_plugin_settings'] ?? [];
        
        // Créer l'objet pour encrypter
        $config = new \MyPlugin\Models\SalesforceConfig();
        
        // Récupérer les paramètres existants (pour garder Salesforce intact)
        $existing_settings = get_option('my_plugin_settings', []);
        
        // ✅ CORRECTION : Chiffrer SEULEMENT si les données sont en clair
        $client_id_raw = sanitize_text_field($gmail_data['gmail_client_id'] ?? '');
        $client_secret_raw = sanitize_text_field($gmail_data['gmail_client_secret'] ?? '');
        $refresh_token_raw = sanitize_text_field($gmail_data['gmail_refresh_token'] ?? '');
        
        // Mettre à jour UNIQUEMENT Gmail - Ne chiffrer que si non vide
        if (!empty($client_id_raw)) {
            $existing_settings['gmail_client_id'] = $config->encrypt($client_id_raw);
        }
        
        if (!empty($client_secret_raw)) {
            $existing_settings['gmail_client_secret'] = $config->encrypt($client_secret_raw);
        }
        
        if (!empty($refresh_token_raw)) {
            $existing_settings['gmail_refresh_token'] = $config->encrypt($refresh_token_raw);
        }
        
        // Sauvegarder dans la base de données
        update_option('my_plugin_settings', $existing_settings);
        
        // Logger
        if (isset($this->logger)) {
            $this->logger->info('✅ Paramètres Gmail enregistrés');
        }
        
        // Rediriger avec succès
        wp_redirect(add_query_arg([
            'page' => 'my-plugin',
            'settings-updated' => 'true'
        ], admin_url('admin.php')));
        exit;
        
    } catch (\Exception $e) {
        // En cas d'erreur
        if (isset($this->logger)) {
            $this->logger->error('Erreur Gmail: ' . $e->getMessage());
        }
        
        wp_redirect(add_query_arg([
            'page' => 'my-plugin',
            'gmail_error' => '1'
        ], admin_url('admin.php')));
        exit;
    }
} 






}