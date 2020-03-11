<?php

namespace hmmhmmmm\quest;

use pocketmine\item\Item;

class QuestData{

   public static function getQuestData(): array{
      $db = Quest::getInstance()->getDatabase();
      return $db->getAll();
   }
   
   public static function getCountQuestData(): int{
      $db = Quest::getInstance()->getDatabase();
      return $db->getCount();
   }
   
   public static function isQuestData(string $name): bool{
      $db = Quest::getInstance()->getDatabase();
      return $db->exists($name);
   }
   
   public static function createQuestData(string $name, int $max, string $event, string $info, string $infoAward, string $commandAward): void{
      $db = Quest::getInstance()->getDatabase();
      $object = [
         "max" => $max,
         "event" => $event,
         "info" => $info,
         "info-award" => $infoAward,
         "command-award" => $commandAward
      ];
      $db->create($name, $object);
   }
   
   public static function editQuestData(string $name, int $max, string $info, string $infoAward, string $commandAward): void{
      $db = Quest::getInstance()->getDatabase();
      $object = [
         "max" => $max,
         "event" => $db->getEvent($name),
         "info" => $info,
         "info-award" => $infoAward,
         "command-award" => $commandAward
      ];
      if(QuestData::isQuestDataLimit($name)){
         $object["playerLimit"] = QuestData::getQuestDataLimit($name);
      }
      if(QuestData::isQuestDataTrading($name)){
         $object["trading"] = QuestData::getQuestDataTrading($name)->jsonSerialize();
      }
      $db->edit($name, $object);
   }
   
   public static function removeQuestData(string $name): void{
      $db = Quest::getInstance()->getDatabase();
      $db->remove($name);
   }
   
   public static function isQuestDataLimit(string $name): bool{
      $db = Quest::getInstance()->getDatabase();
      return $db->isLimit($name);
   }
   
   public static function setQuestDataLimit(string $name): void{
      $db = Quest::getInstance()->getDatabase();
      $db->setLimit($name);
   }
   
   public static function getQuestDataLimit(string $name): array{
      $db = Quest::getInstance()->getDatabase();
      return $db->getLimit($name);
   }
   
   public static function addQuestDataLimit(string $name, string $playerName): void{
      $db = Quest::getInstance()->getDatabase();
      $db->addLimit($name, $playerName);
   }
   
   public static function resetQuestDataLimit(string $name): void{
      $db = Quest::getInstance()->getDatabase();
      $db->resetLimit($name);
   }
   
   public static function removeQuestDataLimit(string $name): void{
      $db = Quest::getInstance()->getDatabase();
      $db->removeLimit($name);
   }
   
   public static function getQuestDataInfo(string $name): string{
      $db = Quest::getInstance()->getDatabase();
      $plugin = Quest::getInstance();
      $arrayText = [
         $plugin->getPrefix()." ".$plugin->getLanguage()->getTranslate(
            "questdata.info.text1",
            [$name]
         ),
         $plugin->getLanguage()->getTranslate(
            "questdata.info.text2",
            [$db->getDescription($name)]
         ),
         $plugin->getLanguage()->getTranslate(
            "questdata.info.text3",
            [$db->getMax($name)]
         ),
         $plugin->getLanguage()->getTranslate(
            "questdata.info.text4",
            [$db->getInfoAward($name)]
         )
      ];
      return implode("\n", $arrayText);
   }
   
   public static function isQuestDataTrading(string $name): bool{
      $db = Quest::getInstance()->getDatabase();
      return $db->isTrading($name);
   }
   
   public static function addQuestDataTrading(string $name, Item $item): void{
      $db = Quest::getInstance()->getDatabase();
      $db->addTrading($name, $item);
   }
  
   public static function getQuestDataTrading(string $name): Item{
      $db = Quest::getInstance()->getDatabase();
      return $db->getTrading($name);
   }
}