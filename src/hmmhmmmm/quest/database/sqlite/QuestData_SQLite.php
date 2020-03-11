<?php

namespace hmmhmmmm\quest\database\sqlite;

use hmmhmmmm\quest\Quest;
use hmmhmmmm\quest\database\Database;
use hmmhmmmm\quest\utils\SQL_QuestDataUtils;
use poggit\libasynql\libasynql;

use pocketmine\item\Item;

class QuestData_SQLite implements Database{
   private $plugin;
   public $db_name;
   
   private $db;
   public $data;
   
   public function __construct(string $db_name){
      $this->plugin = Quest::getInstance();
      $this->db_name = $db_name;
      $mc = $this->plugin->getConfig()->getNested("MySQL-Info");
      $libasynql_friendly_config = [
         "type" => $this->plugin->getConfig()->getNested("questdata-database"),
         "sqlite" => [
            "file" => $this->plugin->getDataFolder()."quest.sqlite3"
         ],
         "mysql" => array_combine(
            ["host", "username", "password", "schema", "port"],
            [$mc["Host"], $mc["User"], $mc["Password"], $mc["Database"], $mc["Port"]]
         )
      ];
      $this->db = libasynql::create($this->plugin, $libasynql_friendly_config, [
         "sqlite" => "sqlite.sql",
         "mysql" => "sqlite.sql"
      ]);
      $this->data = new SQL_QuestDataUtils([]);
      $this->db->executeGeneric("questplugin.quest.init");
      $this->db->executeSelect("questplugin.quest.load", [],
         function(array $questData): void{
            foreach($questData as $qt){
               $name = $qt["Name"];
               $object[$name] = [
                  "max" => $qt["Max"],
                  "event" => $qt["Event"],
                  "info" => $qt["Info"],
                  "info-award" => $qt["InfoAward"],
                  "command-award" => $qt["CommandAward"]
               ];
               if($qt["BoolLimit"]){
                  $object[$name]["playerLimit"] = unserialize($qt["ListLimit"]);
               }
               if($qt["BoolTrade"]){
                  $object[$name]["trading"] = unserialize($qt["Trading"]);
               }
               $this->data->setAll($object);
            }
         }
      );
   }
   
   public function getDatabaseName(): string{
      return $this->db_name;
   }
   
   public function getData(): SQL_QuestDataUtils{
      return $this->data;
   }
   
   public function close(): void{
      $this->db->close();
   }
  
   public function reset(): void{
      $this->db->executeChange("questplugin.quest.reset");
   }
   
   public function saveAll(): void{
      foreach($this->getAll() as $name){
         $this->db->executeChange("questplugin.quest.save", [
            "name" => $name,
            "max" => (int) $this->getMax($name),
            "event" => $this->getEvent($name),
            "info" => $this->getDescription($name),
            "infoAward" => $this->getInfoAward($name),
            "commandAward" => $this->getCommandAward($name)
         ]);
         if($this->isLimit($name)){
            $this->db->executeChange("questplugin.quest.save2", [
               "name" => $name,
               "boolLimit" => true,
               "listLimit" => serialize($this->getLimit($name))
            ]);
         }
         if($this->isTrading($name)){
            $this->db->executeChange("questplugin.quest.save3", [
               "name" => $name,
               "boolTrade" => true,
               "trading" => serialize($this->getTrading($name)->jsonSerialize())
            ]);
         }
      }
   }
   
   public function register(string $name, array $object): void{
      $this->db->executeChange("questplugin.quest.register", [
         "name" => $name,
         "max" => (int) $object["max"],
         "event" => $object["event"],
         "info" => $object["info"],
         "infoAward" => $object["info-award"],
         "commandAward" => $object["command-award"],
         "boolLimit" => false,
         "listLimit" => serialize([]),
         "boolTrade" => false,
         "trading" => serialize([])
      ]);
   }
   
   public function unregister(string $name): void{
      $this->db->executeChange("questplugin.quest.unregister", [
         "name" => $name
      ]);
   }
   
   public function load(): void{
      
   }
  
   public function getAll(): array{
      $data = $this->getData()->getAll();
      return array_keys($data);
   }
   
   public function getCount(): int{
      $data = $this->getData()->getAll();
      return count($data);
   }
   
   public function exists(string $name): bool{
      $data = $this->getData()->getAll();
      return isset($data[$name]);
   }
   
   public function create(string $name, array $object): void{
      $this->register($name, $object);
      $data = $this->getData()->getAll();
      $data[$name] = $object;
      $this->getData()->setAll($data);
      $this->getData()->save();
   }
   
   public function remove(string $name): void{
      $this->unregister($name);
      $data = $this->getData()->getAll();
      unset($data[$name]);
      $this->getData()->setAll($data);
      $this->getData()->save();
   }
   
   public function edit(string $name, array $object): void{
      $data = $this->getData()->getAll();
      $data[$name] = $object;
      $this->getData()->setAll($data);
      $this->getData()->save();
   }
   
   public function getEvent(string $name): string{
      $data = $this->getData()->getAll();
      return $data[$name]["event"];
   }
   
   public function getMax(string $name): int{
      $data = $this->getData()->getAll();
      return $data[$name]["max"];
   }
   
   public function getDescription(string $name): string{
      $data = $this->getData()->getAll();
      return $data[$name]["info"];
   }
  
   public function getInfoAward(string $name): string{
      $data = $this->getData()->getAll();
      return $data[$name]["info-award"];
   }

   public function getCommandAward(string $name): string{
      $data = $this->getData()->getAll();
      return $data[$name]["command-award"];
   }
   
   public function isLimit(string $name): bool{
      $data = $this->getData()->getAll();
      return isset($data[$name]["playerLimit"]);
   }
   
   public function boolLimit(string $name, bool $boolLimit): void{
      $this->db->executeChange("questplugin.quest.save2", [
         "name" => $name,
         "boolLimit" => $boolLimit,
         "listLimit" => serialize([])
      ]);
   }
   
   public function setLimit(string $name): void{
      $data = $this->getData()->getAll();
      $data[$name]["playerLimit"] = [];
      $this->getData()->setAll($data);
      $this->boolLimit($name, true);
   }
   
   public function getLimit(string $name): array{
      $data = $this->getData()->getAll();
      if($this->isLimit($name)){
         return $data[$name]["playerLimit"];
      }else{
         return [];
      } 
   }
   
   public function addLimit(string $name, string $playerName): void{
      $data = $this->getData()->getAll();
      $data[$name]["playerLimit"][] = strtolower($playerName);
      $this->getData()->setAll($data);
      $this->getData()->save();
   }
   
   public function resetLimit(string $name): void{
      $this->setLimit($name);
   }
   
   public function removeLimit(string $name): void{
      $data = $this->getData()->getAll();
      unset($data[$name]["playerLimit"]);
      $this->getData()->setAll($data);
      $this->boolLimit($name, false);
   }
   
   public function isTrading(string $name): bool{
      $data = $this->getData()->getAll();
      return isset($data[$name]["trading"]);
   }
   
   public function addTrading(string $name, Item $item): void{
      $data = $this->getData()->getAll();
      $data[$name]["trading"] = $item->jsonSerialize();
      $this->getData()->setAll($data);
      $this->getData()->save();
   }
  
   public function getTrading(string $name): Item{
      $data = $this->getData()->getAll();
      if($this->isTrading($name)){
         return Item::jsonDeserialize($data[$name]["trading"]);
      }else{
         return Item::get(0, 0, 0);
      } 
   }
}