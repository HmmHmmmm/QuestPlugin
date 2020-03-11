<?php

namespace hmmhmmmm\quest;

use pocketmine\Player;
use pocketmine\command\ConsoleCommandSender;

class QuestManager{

   public static function getQuestEventList(): array{
      $plugin = Quest::getInstance();
      $list = [
         "breakblock" => $plugin->getLanguage()->getTranslate(
            "questmanager.eventlist.breakblock"
         ),
         "placeblock" => $plugin->getLanguage()->getTranslate(
            "questmanager.eventlist.placeblock"
         ),
         "kill_entity" => $plugin->getLanguage()->getTranslate(
            "questmanager.eventlist.kill_entity"
         ),
         "kill_player" => $plugin->getLanguage()->getTranslate(
            "questmanager.eventlist.kill_player"
         ),
         "trading" => $plugin->getLanguage()->getTranslate(
            "questmanager.eventlist.trading.text1"
         )."#".$plugin->getLanguage()->getTranslate(
            "questmanager.eventlist.trading.text2"
         ),
         "online" => $plugin->getLanguage()->getTranslate(
            "questmanager.eventlist.online"
         )
      ];
      return $list;
   }
   
   public static function addQuest(Player $player, string $quest): void{
      $name = strtolower($player->getName());
      $playerQuest = new PlayerQuest($name);
      $quest_db = Quest::getInstance()->getDatabase();
      $max = $quest_db->getMax($quest);
      $event = $quest_db->getEvent($quest);
      $description = $quest_db->getDescription($quest);
      $infoAward = $quest_db->getInfoAward($quest);
      $cmdAward = $quest_db->getCommandAward($quest);
      
      switch(Quest::getInstance()->getConfig()->getNested("playerdata-database")){
         case "sqlite":
            $playerQuest->getDatabase()->register($name, $quest);
            $playerQuest->getDatabase()->createObject($name, $quest);
            break;
         case "mysql":
            $playerQuest->getDatabase()->register($name, $quest);
            $playerQuest->getDatabase()->createObject($name, $quest);
            break;
      }
      
      $playerQuest->setQuestStart($quest, 0);
      $playerQuest->setQuestMax($quest, $max);
      $playerQuest->setQuestEvent($quest, $event);
      $playerQuest->setQuestDescription($quest, $description);
      $playerQuest->setQuestInfoAward($quest, $infoAward);
      $playerQuest->setQuestCommandAward($quest, $cmdAward);
      
      if($quest_db->isLimit($quest)){
         $playerQuest->setQuestLimit($quest);
      }
      if($quest_db->isTrading($quest)){
         $playerQuest->setQuestTrading($quest, $quest_db->getTrading($quest));
      }
   }
  
   public static function startQuest(Player $player, string $quest): void{
      $plugin = Quest::getInstance();
      $name = strtolower($player->getName());
      $playerQuest = new PlayerQuest($name);
      $start = $playerQuest->getQuestStart($quest);
      $start++;
      $playerQuest->setQuestStart($quest, $start);
      if($playerQuest->getQuestStart($quest) >= $playerQuest->getQuestMax($quest)){
         if($playerQuest->isQuestLimit($quest)){
            if(QuestData::isQuestData($quest) && QuestData::isQuestDataLimit($quest)){
               QuestData::addQuestDataLimit($quest, $name);
            }
         }
         $cmd = str_replace("{player}", $name, $playerQuest->getQuestCommandAward($quest));
         $plugin->getServer()->dispatchCommand(new ConsoleCommandSender(), $cmd);
         $plugin->getServer()->broadcastMessage($plugin->getPrefix()." ".$plugin->getLanguage()->getTranslate(
            "questmanager.start.complete",
            [$player->getName(), $quest, $playerQuest->getQuestInfoAward($quest)]
         ));
         $playerQuest->removeQuest($quest);
      }
   }
   
   public static function onQuestEvent(Player $player, string $event): void{
      $name = strtolower($player->getName());
      $playerQuest = new PlayerQuest($name);
      if($playerQuest->getCountQuest() !== 0){
         foreach($playerQuest->getQuest() as $quest){
            if($event == $playerQuest->getQuestEvent($quest)){
               QuestManager::startQuest($player, $quest);
            }
         }
      }
   }
   
   public static function onQuestTrading(Player $player, string $quest): void{
      $name = strtolower($player->getName());
      $playerQuest = new PlayerQuest($name);
      if($playerQuest->isQuestTrading($quest)){
         QuestManager::startQuest($player, $quest);
      }
   }

}