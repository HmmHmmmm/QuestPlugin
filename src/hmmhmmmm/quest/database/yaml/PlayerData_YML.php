<?php

namespace hmmhmmmm\quest\database\yaml;

use hmmhmmmm\quest\Quest;
use hmmhmmmm\quest\database\Database;

use pocketmine\item\Item;
use pocketmine\utils\Config;

class PlayerData_YML implements Database{
   private $plugin;
   public $db_name;
   private $name;
   
   public $path;
   public $data;
   
   public function __construct(string $name, string $db_name){
      $this->plugin = Quest::getInstance();
      $this->db_name = $db_name;
      $this->name = strtolower($name);
      $this->path = $this->plugin->getDataFolder()."account/$this->name.yml";
   }
   
   public function getName(): string{
      return $this->name;
   }
   
   public function getDatabaseName(): string{
      return $this->db_name;
   }
   
   public function getData(): Config{
      return new Config($this->path, Config::YAML, array());
   }
   
   public function close(): void{
   
   }
   
   public function reset(): void{
      $cfg = new Config($this->path, Config::YAML, array());
      $questData = $cfg->getAll();     
      $cfg->setAll($questData);
      $cfg->save();
   }
   
   public function saveAll(): void{
   
   }
   
   public function isData(): bool{
      return file_exists($this->path);
   }
   
   public function register(): void{
      $this->reset();
   }
   
   public function unregister(): void{
      $cfg = new Config($this->path, Config::YAML, array());
      unlink($cfg);
   }
  
   public function getAll(): array{
      $data = $this->getData()->getAll();
      return array_keys($data);
   }
   
   public function getCount(): int{
      $data = $this->getData()->getAll();
      return count($data);
   }
   
   public function exists(string $quest): bool{
      $data = $this->getData()->getAll();
	  return isset($data[$quest]);
   }
  
   public function getStart(string $quest): int{
      $data = $this->getData()->getAll();
      return $data[$quest]["start"];
   }
   
   public function setStart(string $quest, int $start): void{
      $cfg = new Config($this->path, Config::YAML, array());
      $data = $cfg->getAll();
      $data[$quest]["start"] = $start;
      $cfg->setAll($data);
      $cfg->save();
   }
   
   public function getMax(string $quest): int{
      $data = $this->getData()->getAll();
      return $data[$quest]["max"];
   }
   
   public function setMax(string $quest, int $max): void{
      $cfg = new Config($this->path, Config::YAML, array());
      $data = $cfg->getAll();
      $data[$quest]["max"] = $max;
      $cfg->setAll($data);
      $cfg->save();
   }
   
   public function getEvent(string $quest): string{
      $data = $this->getData()->getAll();
      return $data[$quest]["event"];
   }
   
   public function setEvent(string $quest, string $event): void{
      $cfg = new Config($this->path, Config::YAML, array());
      $data = $cfg->getAll();
      $data[$quest]["event"] = $event;
      $cfg->setAll($data);
      $cfg->save();
   }
   
   public function getDescription(string $quest): string{
      $data = $this->getData()->getAll();
      return $data[$quest]["info"];
   }
   
   public function setDescription(string $quest, string $info): void{
      $cfg = new Config($this->path, Config::YAML, array());
      $data = $cfg->getAll();
      $data[$quest]["info"] = $info;
      $cfg->setAll($data);
      $cfg->save();
   }
   
   public function getInfoAward(string $quest): string{
      $data = $this->getData()->getAll();
      return $data[$quest]["info-award"];
   }
   
   public function setInfoAward(string $quest, string $award): void{
      $cfg = new Config($this->path, Config::YAML, array());
      $data = $cfg->getAll();
      $data[$quest]["info-award"] = $award;
      $cfg->setAll($data);
      $cfg->save();
   }
   
   public function getCommandAward(string $quest): string{
      $data = $this->getData()->getAll();
      return $data[$quest]["command-award"];
   }
   
   public function setCommandAward(string $quest, string $cmd): void{
      $cfg = new Config($this->path, Config::YAML, array());
      $data = $cfg->getAll();
      $data[$quest]["command-award"] = $cmd;
      $cfg->setAll($data);
      $cfg->save();
   }
  
   public function isLimit(string $quest): bool{
      $data = $this->getData()->getAll();
      return isset($data[$quest]["playerLimit"]);
   }
   
   public function setLimit(string $quest): void{
      $cfg = new Config($this->path, Config::YAML, array());
      $data = $cfg->getAll();
      $data[$quest]["playerLimit"] = [];
      $cfg->setAll($data);
      $cfg->save();
   }
   
   public function isTrading(string $quest): bool{
      $data = $this->getData()->getAll();
      return isset($data[$quest]["trading"]);
   }
   
   public function getTrading(string $quest): Item{
      $data = $this->getData()->getAll();
      return Item::jsonDeserialize($data[$quest]["trading"]);
   }
   
   public function setTrading(string $quest, Item $item): void{
      $cfg = new Config($this->path, Config::YAML, array());
      $data = $cfg->getAll();
      $data[$quest]["trading"] = $item->jsonSerialize();
      $cfg->setAll($data);
      $cfg->save();
   }
   
   public function remove(string $quest): void{
      $cfg = new Config($this->path, Config::YAML, array());
      $data = $cfg->getAll();
      unset($data[$quest]);
      $cfg->setAll($data);
      $cfg->save();
   }

}