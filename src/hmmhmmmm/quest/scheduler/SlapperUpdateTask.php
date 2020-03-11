<?php

namespace hmmhmmmm\quest\scheduler;

use hmmhmmmm\quest\Quest;
use hmmhmmmm\quest\QuestData;
use slapper\entities\SlapperEntity;
use slapper\entities\SlapperHuman;

use pocketmine\scheduler\Task;

class SlapperUpdateTask extends Task{
   private $plugin;

   public function __construct(Quest $plugin){
      $this->plugin = $plugin;
   }
   
   public function getPlugin(): Quest{
      return $this->plugin;
   }
   
   public function onRun(int $currentTick): void{
      $this->onSlapperUpdate();
   }
   
   public function onSlapperUpdate(): void{
      foreach($this->plugin->getServer()->getLevels() as $level){
         foreach($level->getEntities() as $entity){
            if($entity instanceof SlapperEntity || $entity instanceof SlapperHuman){
               if(!empty($entity->namedtag->getString("slapper_Quest", ""))){
                  $quest = $entity->namedtag->getString("slapper_Quest");
                  if(QuestData::isQuestData($quest)){
                     $entity->setNameTag(QuestData::getQuestDataInfo($quest));
                  }else{
                     $entity->close();
                  }
               }
            }
         }
      }
   }

}