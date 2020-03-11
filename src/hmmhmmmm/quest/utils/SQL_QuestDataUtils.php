<?php

namespace hmmhmmmm\quest\utils;

use hmmhmmmm\quest\Quest;

class SQL_QuestDataUtils{
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
      Quest::getInstance()->getDatabase()->saveAll();
   }
   
}