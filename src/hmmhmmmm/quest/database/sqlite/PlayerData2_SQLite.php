<?php

namespace hmmhmmmm\quest\database\sqlite;

use hmmhmmmm\quest\Quest;
use hmmhmmmm\quest\database\Database;
use hmmhmmmm\quest\utils\SQL_PlayerDataUtils;
use adeynes\cucumber\libs\poggit\libasynql\libasynql;

use pocketmine\item\Item;

class PlayerData2_SQLite extends PlayerData_SQLite{
   private $plugin;
   private $name;
   
   private $data;
   private $db;
   
   public function __construct(string $name){
      $this->plugin = Quest::getInstance();
      $this->name = strtolower($name);
      $this->data = $this->plugin->array["SQL_Utils_Player"];
      $this->db = $this->plugin->array["SQL_Data_Player"];
   }
   
   public function getData(): SQL_PlayerDataUtils{
      return $this->data;
   }
   
   public function getName(): string{
      return $this->name;
   }
   
   public function saveAll(string $name): void{
      $data = $this->getData()->getAll();
      foreach(array_keys($data[$name]) as $quest){
         $this->db->executeChange("questplugin.player.save", [
            "name" => $name,
            "quest" => $quest,
            "start" => (int) $data[$name][$quest]["start"],
            "max" => (int) $data[$name][$quest]["max"],
            "event" => $data[$name][$quest]["event"],
            "info" => $data[$name][$quest]["info"],
            "infoAward" => $data[$name][$quest]["info-award"],
            "commandAward" => $data[$name][$quest]["command-award"]
         ]);
         if(isset($data[$name][$quest]["playerLimit"])){
            $this->db->executeChange("questplugin.player.save2", [
               "name" => $name,
               "quest" => $quest,
               "boolLimit" => true,
               "listLimit" => serialize([])
            ]);
         }
         if(isset($data[$name][$quest]["trading"])){
            $this->db->executeChange("questplugin.player.save3", [
               "name" => $name,
               "quest" => $quest,
               "boolTrade" => true,
               "trading" => serialize($data[$name][$quest]["trading"])
            ]);
         }
      }
   }
   
   public function register(string $name, string $quest): void{
      $this->db->executeChange("questplugin.player.register", [
         "name" => $name,
         "quest" => $quest,
         "start" => 0,
         "max" => 0,
         "event" => "?",
         "info" => "?",
         "infoAward" => "?",
         "commandAward" => "?",
         "boolLimit" => false,
         "listLimit" => serialize([]),
         "boolTrade" => false,
         "trading" => serialize([])
      ]);
   }
   
   public function unregister(string $name, string $quest): void{
      $this->db->executeChange("questplugin.player.unregister", [
         "name" => $name,
         "quest" => $quest
      ]);
   }
   
   public function createObject(string $name, string $quest): void{
      $data = $this->getData()->getAll();
      $data[$name][$quest] = [
         "start" => 0,
         "max" => 0,
         "event" => "?",
         "info" => "?",
         "info-award" => "?",
         "command-award" => "?"
      ];
      $this->getData()->setAll($data);
   }
   
   public function getAll(): array{
      $name = $this->getName();
      $data = $this->getData()->getAll();
      return array_keys($data[$name]);
   }
   
   public function getCount(): int{
      $name = $this->getName();
      $data = $this->getData()->getAll();
      if(isset($data[$name])){
         return count($data[$name]);
      }else{
         return 0;
      }
   }
   
   public function exists(string $quest): bool{
      $name = $this->getName();
      $data = $this->getData()->getAll();
      return isset($data[$name][$quest]);
   }
  
   public function getStart(string $quest): int{
      $name = $this->getName();
      $data = $this->getData()->getAll();
      return $data[$name][$quest]["start"];
   }
   
   public function setStart(string $quest, int $start): void{
      $name = $this->getName();
      $data = $this->getData()->getAll();
      $data[$name][$quest]["start"] = $start;
      $this->getData()->setAll($data);
      $this->saveAll($name);
   }
   
   public function getMax(string $quest): int{
      $name = $this->getName();
      $data = $this->getData()->getAll();
      return $data[$name][$quest]["max"];
   }
   
   public function setMax(string $quest, int $max): void{
      $name = $this->getName();
      $data = $this->getData()->getAll();
      $data[$name][$quest]["max"] = $max;
      $this->getData()->setAll($data);
      $this->saveAll($name);
   }
   
   public function getEvent(string $quest): string{
      $name = $this->getName();
      $data = $this->getData()->getAll();
      return $data[$name][$quest]["event"];
   }
   
   public function setEvent(string $quest, string $event): void{
      $name = $this->getName();
      $data = $this->getData()->getAll();
      $data[$name][$quest]["event"] = $event;
      $this->getData()->setAll($data);
      $this->saveAll($name);
   }
   
   public function getDescription(string $quest): string{
      $name = $this->getName();
      $data = $this->getData()->getAll();
      return $data[$name][$quest]["info"];
   }
   
   public function setDescription(string $quest, string $info): void{
      $name = $this->getName();
      $data = $this->getData()->getAll();
      $data[$name][$quest]["info"] = $info;
      $this->getData()->setAll($data);
      $this->saveAll($name);
   }
   
   public function getInfoAward(string $quest): string{
      $name = $this->getName();
      $data = $this->getData()->getAll();
      return $data[$name][$quest]["info-award"];
   }
   
   public function setInfoAward(string $quest, string $award): void{
      $name = $this->getName();
      $data = $this->getData()->getAll();
      $data[$name][$quest]["info-award"] = $award;
      $this->getData()->setAll($data);
      $this->saveAll($name);
   }
   
   public function getCommandAward(string $quest): string{
      $name = $this->getName();
      $data = $this->getData()->getAll();
      return $data[$name][$quest]["command-award"];
   }
   
   public function setCommandAward(string $quest, string $cmd): void{
      $name = $this->getName();
      $data = $this->getData()->getAll();
      $data[$name][$quest]["command-award"] = $cmd;
      $this->getData()->setAll($data);
      $this->saveAll($name);
   }
  
   public function isLimit(string $quest): bool{
      $name = $this->getName();
      $data = $this->getData()->getAll();
      return isset($data[$name][$quest]["playerLimit"]);
   }
   
   public function getLimit(string $quest): array{
      $name = $this->getName();
      $data = $this->getData()->getAll();
      return $data[$name][$quest]["playerLimit"];
   }
   
   public function setLimit(string $quest): void{
      $name = $this->getName();
      $data = $this->getData()->getAll();
      $data[$name][$quest]["playerLimit"] = [];
      $this->getData()->setAll($data);
      $this->saveAll($name);
   }
   
   public function isTrading(string $quest): bool{
      $name = $this->getName();
      $data = $this->getData()->getAll();
      return isset($data[$name][$quest]["trading"]);
   }
   
   public function getTrading(string $quest): Item{
      $name = $this->getName();
      $data = $this->getData()->getAll();
      return Item::jsonDeserialize($data[$name][$quest]["trading"]);
   }
   
   public function setTrading(string $quest, Item $item): void{
      $name = $this->getName();
      $data = $this->getData()->getAll();
      $data[$name][$quest]["trading"] = $item->jsonSerialize();
      $this->getData()->setAll($data);
      $this->saveAll($name);
   }
   
   public function remove(string $quest): void{
      $name = $this->getName();
      $this->unregister($name, $quest);
      $data = $this->getData()->getAll();
      unset($data[$name][$quest]);
      $this->getData()->setAll($data);
      $this->saveAll($name);
   }
   
}