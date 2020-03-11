<?php

namespace hmmhmmmm\quest\utils;

use hmmhmmmm\quest\Quest;
use hmmhmmmm\quest\PlayerQuest;

class SQL_PlayerDataUtils{
   private $object;
   
   public function __construct(array $object = []){
      $this->object = $object;
   }
   
   public function getAll(): array{
      return $this->object;
   }
   
   public function setAll(array $data): void{
      $this->object = $data;
   }
   
   public function save(): void{
      /*$playerQuest = new PlayerQuest($this->name);
      $playerQuest->getDatabase()->saveAll();*/
   }
   
}