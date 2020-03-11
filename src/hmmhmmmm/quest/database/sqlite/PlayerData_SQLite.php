<?php

namespace hmmhmmmm\quest\database\sqlite;

use hmmhmmmm\quest\Quest;
use hmmhmmmm\quest\database\Database;
use hmmhmmmm\quest\utils\SQL_PlayerDataUtils;
use poggit\libasynql\libasynql;

use pocketmine\item\Item;

class PlayerData_SQLite implements Database{
   private $plugin;
   public $db_name;
   
   private $db;
   private $data;
   
   public function __construct(string $db_name){
      $this->plugin = Quest::getInstance();
      $this->db_name = $db_name;
      $this->data = $this->plugin->array["SQL_Utils_Player"] = new SQL_PlayerDataUtils([]);
      $mc = $this->plugin->getConfig()->getNested("MySQL-Info");
      $libasynql_friendly_config = [
         "type" => $this->plugin->getConfig()->getNested("playerdata-database"),
         "sqlite" => [
            "file" => $this->plugin->getDataFolder()."players.sqlite3"
         ],
         "mysql" => array_combine(
            ["host", "username", "password", "schema", "port"],
            [$mc["Host"], $mc["User"], $mc["Password"], $mc["Database"], $mc["Port"]]
         )
      ];
      $this->db = $this->plugin->array["SQL_Data_Player"] = libasynql::create($this->plugin, $libasynql_friendly_config, [
         "sqlite" => "sqlite.sql",
         "mysql" => "sqlite.sql"
      ]);
      $this->db->executeGeneric("questplugin.player.init");
      $this->db->executeSelect("questplugin.player.load", [],
         function(array $playerData): void{
            foreach($playerData as $pd){
               $name = $pd["Name"];
               $quest = $pd["Quest"];
               $object[$name][$quest] = [
                  "start" => $pd["Start"],
                  "max" => $pd["Max"],
                  "event" => $pd["Event"],
                  "info" => $pd["Info"],
                  "info-award" => $pd["InfoAward"],
                  "command-award" => $pd["CommandAward"]
               ];
               if($pd["BoolLimit"]){
                  $object[$name][$quest]["playerLimit"] = unserialize($pd["ListLimit"]);
               }
               if($pd["BoolTrade"]){
                  $object[$name][$quest]["trading"] = unserialize($pd["Trading"]);
               }
               $this->data->setAll($object);
            }
         }
      );
   }
   
   public function getDatabaseName(): string{
      return $this->db_name;
   }
   
   public function getData(): SQL_PlayerDataUtils{
      return $this->data;
   }
   
   public function close(): void{
      $this->db->close();
   }
  
   public function reset(): void{
      $this->db->executeChange("questplugin.player.reset");
   }
   
   public function load(): void{
      
   }
  
}