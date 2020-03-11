<?php

namespace hmmhmmmm\quest;

use hmmhmmmm\quest\data\PlayerData;

use pocketmine\Player;
use pocketmine\item\Item;

class PlayerQuest extends PlayerData{

   public function getQuest(): array{
      return $this->getDatabase()->getAll();
   }
   
   public function getCountQuest(): int{
      return $this->getDatabase()->getCount();
   }
   
   public function isQuest(string $quest): bool{
      return $this->getDatabase()->exists($quest);
   }
  
   public function getQuestStart(string $quest): int{
      return $this->getDatabase()->getStart($quest);
   }
   
   public function setQuestStart(string $quest, int $start): void{
      $this->getDatabase()->setStart($quest, $start);
   }
   
   public function getQuestMax(string $quest): int{
      return $this->getDatabase()->getMax($quest);
   }
   
   public function setQuestMax(string $quest, int $max): void{
      $this->getDatabase()->setMax($quest, $max);
   }
   
   public function getQuestEvent(string $quest): string{
      return $this->getDatabase()->getEvent($quest);
   }
   
   public function setQuestEvent(string $quest, string $event): void{
      $this->getDatabase()->setEvent($quest, $event);
   }
   
   public function getQuestDescription(string $quest): string{
      return $this->getDatabase()->getDescription($quest);
   }
   
   public function setQuestDescription(string $quest, string $info): void{
      $this->getDatabase()->setDescription($quest, $info);
   }
   
   public function getQuestInfoAward(string $quest): string{
      return $this->getDatabase()->getInfoAward($quest);
   }
   
   public function setQuestInfoAward(string $quest, string $award): void{
      $this->getDatabase()->setInfoAward($quest, $award);
   }
   
   public function getQuestCommandAward(string $quest): string{
      return $this->getDatabase()->getCommandAward($quest);
   }
   
   public function setQuestCommandAward(string $quest, string $cmd): void{
      $this->getDatabase()->setCommandAward($quest, $cmd);
   }
   
   public function getQuestInfo(string $quest, string $text = ""): string{
      $plugin = Quest::getInstance();
      if($text !== ""){
         $text = "\n".$plugin->getLanguage()->getTranslate(
            "playerquest.info.error1",
            [$text]
         );
      }
      $start = $this->getQuestStart($quest);
      $max = $this->getQuestMax($quest);
      $description = $this->getQuestDescription($quest);
      $infoAward = $this->getQuestInfoAward($quest);
      $arrayText = [
         $plugin->getPrefix()." ".$plugin->getLanguage()->getTranslate(
            "playerquest.info.text1",
            [$quest]
         ),
         $plugin->getLanguage()->getTranslate(
            "playerquest.info.text2",
            [$start, $max]
         ),
         $plugin->getLanguage()->getTranslate(
            "playerquest.info.text3",
            [$description]
         ),
         $plugin->getLanguage()->getTranslate(
            "playerquest.info.text4",
            [$infoAward, $text]
         )
      ];
      return implode("\n", $arrayText);
   }
  
   public function isQuestLimit(string $quest): bool{
      return $this->getDatabase()->isLimit($quest);
   }
   
   public function setQuestLimit(string $quest): void{
      $this->getDatabase()->setLimit($quest);
   }
   
   public function isQuestTrading(string $quest): bool{
      return $this->getDatabase()->isTrading($quest);
   }
   
   public function getQuestTrading(string $quest): Item{
      return $this->getDatabase()->getTrading($quest);
   }
   
   public function setQuestTrading(string $quest, Item $item): void{
      $this->getDatabase()->setTrading($quest, $item);
   }
   
   public function removeQuest(string $quest): void{
      $this->getDatabase()->remove($quest);
   }
   
}