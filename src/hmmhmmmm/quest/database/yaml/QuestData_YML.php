<?php

namespace hmmhmmmm\quest\database\yaml;

use hmmhmmmm\quest\Quest;
use hmmhmmmm\quest\database\Database;

use pocketmine\item\Item;
use pocketmine\utils\Config;

class QuestData_YML implements Database{
   private $plugin;
   public $db_name;
   
   public $data;
   
   public function __construct(string $db_name){
      $this->plugin = Quest::getInstance();
      $this->db_name = $db_name;
      $this->data = new Config($this->plugin->getDataFolder()."quest.yml", Config::YAML, array());
   }
   
   public function getDatabaseName(): string{
      return $this->db_name;
   }
   
   public function getData(): Config{
      return $this->data;
   }
   
   public function close(): void{
   
   }
   
   public function reset(): void{
      $data = $this->getData()->getAll();
      $data = [];
      $this->getData()->setAll($data);
      $this->getData()->save();
   }
   
   public function saveAll(): void{
   
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
      $data = $this->getData()->getAll();
      $data[$name] = $object;
      $this->getData()->setAll($data);
      $this->getData()->save();
   }
   
   public function remove(string $name): void{
      $data = $this->getData()->getAll();
      unset($data[$name]);
      $this->getData()->setAll($data);
      $this->getData()->save();
   }
   
   public function edit(string $name, array $object): void{
      $this->create($name, $object);
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
   
   public function setLimit(string $name): void{
      $data = $this->getData()->getAll();
      $data[$name]["playerLimit"] = [];
      $this->getData()->setAll($data);
      $this->getData()->save();
   }
   
   public function getLimit(string $name): array{
      $data = $this->getData()->getAll();
      return $data[$name]["playerLimit"];
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
      $this->getData()->save();
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
      return Item::jsonDeserialize($data[$name]["trading"]);
   }
   
}