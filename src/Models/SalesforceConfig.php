<?php
namespace MyPlugin\Models;

class SalesforceConfig{
    private const OPTION_NAME="my_plugin_settings";

 private array $settings;
//recupere les parametrtes de my plugin setting Et les met dans $this->settings
 public function __construct()
 {
    $this->settings =get_option(self:: OPTION_NAME, []);
 }
// Elles lisent un paramètre précis , Depuis le tableau $this->settings

public function getInstance():string
{
   return $this->settings['instance'] ?? '';
}

public function getClientID(): string
{
   return $this->settings['client_id']??'';
   
}

public function getClientSecret():string
{
   return $this->settings ['client_secret'] ?? '';
}

public function getUsername():string
{
   return $this->settings['User_namme']??'';
}

public function getPassword():string
{
   return $this->settings['pass'] ??'';
}

public function getReportId():string
{
 return $this->settings['report_id']??''; 
}

public function getHostUrl():string
{
   return $this->settings['HostUrl']??'';
}

public function getUserSecret():string
{
   return $this->settings['UserSecret']??'';
}

//vérification que tous les champs sont remplie
public function  isConfigured():bool
{
   return !empty($this->getClientID())
   &&     !empty($this->getClientSecret())
   &&     !empty($this->getUsername())
   &&     !empty($this->getPassword())
   &&     !empty($this->getHostUrl())
   &&     !empty($this->getUserSecret());
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
