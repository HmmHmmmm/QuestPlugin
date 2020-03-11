<?php

namespace hmmhmmmm\quest;

use hmmhmmmm\quest\cmd\QuestCommand;
use hmmhmmmm\quest\data\Language;
use hmmhmmmm\quest\database\Database;
use hmmhmmmm\quest\database\mysql\PlayerData_MySQL;
use hmmhmmmm\quest\database\mysql\QuestData_MySQL;
use hmmhmmmm\quest\database\sqlite\PlayerData_SQLite;
use hmmhmmmm\quest\database\sqlite\QuestData_SQLite;
use hmmhmmmm\quest\database\yaml\QuestData_YML;
use hmmhmmmm\quest\listener\EventListener;
use hmmhmmmm\quest\scheduler\QuestTask;
use hmmhmmmm\quest\scheduler\SlapperUpdateTask;
use hmmhmmmm\quest\ui\QuestForm;
use xenialdan\customui\API as XenialdanCustomUI;
use poggit\libasynql\libasynql;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

use function class_exists;

class Quest extends PluginBase{
   private static $instance = null;

   private $prefix = "?";
   private $facebook = "§cwithout";
   private $youtube = "§cwithout";
   private $discord = "§cwithout";
   private $language = null;
   public $array = [];
   private $slapper = null;
   private $questform = null;
   
   public $database;
   public $player_database;
    
   private $langClass = [
      "thai",
      "english"
   ];
   
   public $questEvent = null;
   
   public static function getInstance(): Quest{
      return self::$instance;
   }
   public function onLoad(): void{
      self::$instance = $this;
   } 
   
   public function onEnable(): void{
      @mkdir($this->getDataFolder());
      @mkdir($this->getDataFolder()."language/");
      $this->prefix = "Quest";
      $this->youtube = "https://bit.ly/2HL1j28";
      $langConfig = $this->getConfig()->getNested("language");
      if(!in_array($langConfig, $this->langClass)){
         $this->getLogger()->error("§cNot found language ".$langConfig.", Please try ".implode(", ", $this->langClass));
         $this->getServer()->getPluginManager()->disablePlugin($this);
         return;
      }else{
         $this->language = new Language($this, $langConfig);
         $this->questform = new QuestForm($this);
         $this->questEvent = QuestManager::getQuestEventList();
         $this->getServer()->getCommandMap()->register("QuestPlugin", new QuestCommand($this));
         $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
         $this->getScheduler()->scheduleRepeatingTask(new SlapperUpdateTask($this), (20 * $this->getConfig()->getNested("slapper-update")));
         $this->getScheduler()->scheduleRepeatingTask(new QuestTask($this), 20);
         switch($this->getConfig()->getNested("questdata-database")){
            case "sqlite":
               $this->database = new QuestData_SQLite("SQLite");
               break;
            case "mysql":
               $this->database = new QuestData_MySQL("MySQL");
               break;
            case "yml":
               $this->database = new QuestData_YML("Yaml");
               break;
            default:
               $this->database = new QuestData_YML("Yaml");
               break;
         }
         switch($this->getConfig()->getNested("playerdata-database")){
            case "sqlite":
               $this->player_database = new PlayerData_SQLite("SQLite3");
               break;
            case "mysql":
               $this->player_database = new PlayerData_MySQL("MySQL");
               break;
            case "yml":
               @mkdir($this->getDataFolder()."account/");
               break;
      }
      }
      if($this->getServer()->getPluginManager()->getPlugin("Slapper") === null){
         $this->getLogger()->error($this->language->getTranslate("notfound.plugin", ["Slapper"]));
         $this->getServer()->getPluginManager()->disablePlugin($this);
         return;
      }else{
         $this->slapper = $this->getServer()->getPluginManager()->getPlugin("Slapper");
      }
      if(!class_exists(XenialdanCustomUI::class)){
         $this->getLogger()->error($this->language->getTranslate("notfound.libraries", ["CustomUI"]));
         $this->getServer()->getPluginManager()->disablePlugin($this);
         return;
      }
      if(!class_exists(libasynql::class)){
         $this->getLogger()->error($this->language->getTranslate("notfound.libraries", ["Libasynql"]));
         $this->getServer()->getPluginManager()->disablePlugin($this);
         return;
      }
   }
   
   public function getPrefix(): string{
      return "§b[§e".$this->prefix."§b]§f";
   }
   
   public function getFacebook(): string{
      return $this->facebook;
   }
   
   public function getYoutube(): string{
      return $this->youtube;
   }
   
   public function getDiscord(): string{
      return $this->discord;
   }
   
   public function getLanguage(): Language{
      return $this->language;
   }
   
   public function getQuestForm(): QuestForm{
      return $this->questform;
   }
   
   public function getDatabase(): Database{
      return $this->database;
   }
   
   public function getPlayerDatabase(): Database{
      return $this->player_database;
   }
  
   public function onDisable(): void{
      if($this->database instanceof Database){
         $this->getDatabase()->close();
      }
      if($this->player_database instanceof Database){
         $this->getPlayerDatabase()->close();
      }
   }
   
   public function getPluginInfo(): string{
      $author = implode(", ", $this->getDescription()->getAuthors());
      $arrayText = [
         $this->getPrefix()." ".$this->getLanguage()->getTranslate("plugininfo.name", [$this->getDescription()->getName()]),
         $this->getPrefix()." ".$this->getLanguage()->getTranslate("plugininfo.version", [$this->getDescription()->getVersion()]),
         $this->getPrefix()." ".$this->getLanguage()->getTranslate("plugininfo.author", [$author]),
         $this->getPrefix()." ".$this->getLanguage()->getTranslate("plugininfo.description"),
         $this->getPrefix()." ".$this->getLanguage()->getTranslate("plugininfo.facebook", [$this->getFacebook()]),
         $this->getPrefix()." ".$this->getLanguage()->getTranslate("plugininfo.youtube", [$this->getYoutube()]),
         $this->getPrefix()." ".$this->getLanguage()->getTranslate("plugininfo.website", [$this->getDescription()->getWebsite()]),
         $this->getPrefix()." ".$this->getLanguage()->getTranslate("plugininfo.discord", [$this->getDiscord()]),
      ];
      return implode("\n", $arrayText);
   }
   
   
}