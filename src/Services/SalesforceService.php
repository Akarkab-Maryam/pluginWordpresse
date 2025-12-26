<?php
namespace MyPlugin\Services;

use MyPlugin\Models\SalesforceConfig;
use Shuchkin\SimpleXLSXGen;

class SalesforceService
{
    //stoke la configuration salsforce
    private SalesforceConfig $config;
    private EmailService $emailService;
   
    //recoit la configuration
    public function __construct(SalesforceConfig $config,EmailService $emailService)
    {
        //sauvegard la config
        $this->config = $config;
         $this->emailService = $emailService;
    }

    //Vérif est c que tout les champs sont remplie 
    public function testConnection(): array
    {
        if (!$this->config->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Configuration incomplete'
            ];
        }
        //recupere l'url selsforce+demande de token d'authentification
        $url = $this->config->getTokenUrl();

        //préparer les parametres
        $body = [
            'grant_type'    => 'password',
            'client_id'     => $this->config->getClientID(),
            'client_secret' => $this->config->getClientSecret(),
            'username'      => $this->config->getUsername(),
            'password'      => $this->config->getFullPassword()
        ];

        //Envoi de la requete poste a l'API selsforce ( temps a attendre 30s)
        $response = wp_remote_post($url, [
            'body'    => $body,
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        ]);
        
        //vérif de la requete si echoue =>Affiche erreure
        if (is_wp_error($response)) {
            return [
                'success' => false,
                'message' => 'Erreur de connexion : ' . $response->get_error_message()
            ];
        }
     
        $data = json_decode(wp_remote_retrieve_body($response), true);
        // recupere token salsforce si la conn est reussi 
        if (isset($data['access_token'])) {
            return [
                'success'      => true,
                'message'      => 'Connexion réussie !',
                'access_token' => $data['access_token'],
                'instance_url' => $data['instance_url'] ?? ''
            ];
        }

        return [
            'success' => false,
            'message' => $data['error_description'] ?? $data['error'] ?? 'Erreur inconnue'
        ];
    }

    //Télécharger le rapport Salesforce en format Excel
    public function downloadReport(): array
    {
        // Vérifier la configuration
        if (!$this->config->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Configuration incomplete'
            ];
        }

        // Teste la conn selsforce
        $authResult = $this->testConnection();
        
        if (!$authResult['success']) {
            return [
                'success' => false,
                'message' => 'Authentification échouée : ' . $authResult['message']
            ];
        }

        // Récupéré les information (token, adress selsforce, id rapport)
        $accessToken = $authResult['access_token'];
        $instanceUrl = $authResult['instance_url'];
        $reportId = $this->config->getReportId();

        // 3. Récupérer le rapport depuis Salesforce
        $reportUrl = $instanceUrl . '/services/data/v58.0/analytics/reports/' . $reportId;

        // Wordpresse envoie la requette a selsforce pour telechargé le rapport  ( envoyer la requette get a selsforce)
        $response = wp_remote_get($reportUrl, [
            'timeout' => 60,
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ]
        ]);

        // Vérif si la requete échoue
        if (is_wp_error($response)) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la récupération du rapport : ' . $response->get_error_message()
            ];
        }

        // Recupere le code de la requette 
        $httpCode = wp_remote_retrieve_response_code($response);
        
        // Si le code #200 ==> erreur (200 :succes , 401 : non autorisé , 404 : non trouvé , 500 : Erreure serveur)
        if ($httpCode !== 200) {
            return [
                'success' => false,
                'message' => 'Erreur Salesforce (code ' . $httpCode . ')'
            ];
        }

        // Recupere la répanse de la requette (wp_remote_retrieve_body() récupéré le contenue d'une réponse)
        $data = json_decode(wp_remote_retrieve_body($response), true);

        // 4. Générer le fichier Excel
        $upload_dir = wp_upload_dir();
        $filename = 'salesforce-report-' . date('Y-m-d-H-i-s') . '.xlsx';
        $filepath = $upload_dir['path'] . '/' . $filename;
        $file_url = $upload_dir['url'] . '/' . $filename;

        $result = $this->createExcelFile($data, $filepath);

        if (!$result) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la création du fichier Excel'
            ];
        }

        $to = $this->config->getUsername();
        $emailSent = $this->emailService->envoyerRapport($to, $filepath, $filename, $data);

        if ($emailSent) {
            return [
                'success' => true,
                'file_url' => $file_url,
                'message' => 'Rapport généré et envoyé par email à ' . $to
            ];
        } else {
            return [
                'success' => true,
                'file_url' => $file_url,
                'message' => 'Rapport généré avec succès (échec envoi email)'
            ];
        }
    }

    private function createExcelFile(array $data, string $filepath): bool
        
       
    {
        // Extraire les en-têtes et les lignes
        $headers = [];
        $rows = [];

        // Récupérer les colonnes (en-têtes)
        if (isset($data['reportMetadata']['detailColumns'])) {
            $headers = $data['reportMetadata']['detailColumns'];
        }

        // Récupérer les lignes de données
        if (isset($data['factMap']['T!T']['rows'])) {
            foreach ($data['factMap']['T!T']['rows'] as $row) {
                $rowData = [];
                foreach ($row['dataCells'] as $cell) {
                    $rowData[] = $cell['label'] ?? '';
                }
                $rows[] = $rowData;
            }
        }

        // Construire le tableau complet (en-têtes + données)
        $allData = [];
        $allData[] = $headers;

        foreach ($rows as $row) {
            $allData[] = $row;
        }

        // Créer le fichier Excel et sauvegarder
        $xlsx = SimpleXLSXGen::fromArray($allData);
        
        return $xlsx->saveAs($filepath);
    }

   
}
