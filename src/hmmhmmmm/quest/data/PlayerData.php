<?php

namespace hmmhmmmm\quest\data;

use hmmhmmmm\quest\Quest;
use hmmhmmmm\quest\database\Database;
use hmmhmmmm\quest\database\mysql\PlayerData2_MySQL;
use hmmhmmmm\quest\database\sqlite\PlayerData2_SQLite;
use hmmhmmmm\quest\database\yaml\PlayerData_YML;

use pocketmine\Player;
use pocketmine\utils\Config;

class PlayerData{
   private $plugin;
   private $name;
   
   public $database;

   public function __construct(string $name){
      $this->plugin = Quest::getInstance();
      $this->name = strtolower($name);
      switch($this->plugin->getConfig()->getNested("playerdata-database")){
         case "sqlite":
            $this->database = new PlayerData2_SQLite($this->name);
            break;
         case "mysql":
            $this->database = new PlayerData2_MySQL($this->name);
            break;
         case "yml":
            $this->database = new PlayerData_YML($this->name, "Yaml");
            break;
         default:
            $this->database = new PlayerData_YML($this->name, "Yaml");
            break;
      }
   }
   
   public function getPlugin(): Quest{
      return $this->plugin;
   }
   
   public function getName(): string{
      return $this->name;
   }

   public function getDatabase(): Database{
      return $this->database;
   }
   
}