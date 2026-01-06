<?php
namespace MyPlugin\Models;

class SalesforceConfig{
    private const OPTION_NAME="my_plugin_settings";
    private const ENCRYPTION_KEY = "ma-cle-secrete-de-32-caracteres!"; // Clé fixe

 private array $settings;

 // ========== FONCTIONS DE CHIFFREMENT/DÉCHIFFREMENT ==========
 
 // Chiffrer une donnée avant de la stocker en BDD
 public function encrypt($data) {
    if (empty($data)) return '';
    $key = self::ENCRYPTION_KEY;
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode(base64_encode($iv) . '::' . $encrypted);
 }
 
 // Déchiffrer une donnée après lecture de la BDD
 private function decrypt($data) {
    if (empty($data)) return '';
    $key = self::ENCRYPTION_KEY;
    $decoded = base64_decode($data);
    $parts = explode('::', $decoded, 2);
    if (count($parts) !== 2) return $data;
    $iv = base64_decode($parts[0]);
    return openssl_decrypt($parts[1], 'AES-256-CBC', $key, 0, $iv);
 }

 // ========== FIN FONCTIONS DE CHIFFREMENT/DÉCHIFFREMENT ==========

//recupere les parametrtes de my plugin setting Et les met dans $this->settings
 public function __construct()
 {
    $this->settings =get_option(self:: OPTION_NAME, []);
 }
// Elles lisent un paramètre précis , Depuis le tableau $this->settings

public function getInstance():string
{
   $value = $this->settings['instance'] ?? '';
   return $this->decrypt($value);
}

public function getClientID(): string
{
   $value = $this->settings['client_id'] ?? '';
   return $this->decrypt($value);
}

public function getClientSecret():string
{
   $value = $this->settings['client_secret'] ?? '';
   return $this->decrypt($value);
}

public function getUsername():string
{
   $value = $this->settings['User_namme'] ?? '';
   return $this->decrypt($value);
}

public function getPassword():string
{
   $value = $this->settings['pass'] ?? '';
   return $this->decrypt($value);
}

public function getReportId():string
{
   $value = $this->settings['report_id'] ?? '';
   return $this->decrypt($value);
}

public function getHostUrl():string
{
   $value = $this->settings['HostUrl'] ?? '';
   return $this->decrypt($value);
}

public function getUserSecret():string
{
   $value = $this->settings['UserSecret'] ?? '';
   return $this->decrypt($value);
}

//Configurations des credentials gmail:
public function getGmailClientId():string
{
$value = $this->settings['gmail_client_id'] ?? '';
   return $this->decrypt($value);
}

public function getGmailClientSecret():string
{
   $value = $this->settings['gmail_client_secret'] ?? '';
   return $this->decrypt($value);
}

public function getGmailRefreshToken():string
{
    $value = $this->settings['gmail_refresh_token'] ?? '';
   return $this->decrypt($value);
}


//vérification que tous les champs sont remplie
public function isConfigured():bool
{
    return !empty($this->getClientID())
        && !empty($this->getClientSecret())
        && !empty($this->getUsername())
        && !empty($this->getPassword())
        && !empty($this->getHostUrl())
        && !empty($this->getUserSecret())
        && !empty($this->getGmailClientId())
        && !empty($this->getGmailClientSecret())
        && !empty($this->getGmailRefreshToken());
}



//Construit l'url complete de l'API SALSFORCE  : (etape de construction de l'URL selsforce)
public function getTokenUrl():string
{
   return rtrim($this->getHostUrl(),'/'). '/services/oauth2/token';
}


//Colle le mot de passe et le user secret ensemble
public function getFullPassword():string
{
   return $this->getPassword().$this->getUserSecret();
}




}





?>