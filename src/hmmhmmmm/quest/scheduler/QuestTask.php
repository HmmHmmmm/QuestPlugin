<?php

namespace hmmhmmmm\quest\scheduler;

use hmmhmmmm\quest\Quest;
use hmmhmmmm\quest\QuestManager;

use pocketmine\scheduler\Task;

class QuestTask extends Task{
   private $plugin;
   
   public function __construct(Quest $plugin){
      $this->plugin = $plugin;
   }
 
   public function getPlugin(): Quest{
      return $this->plugin;
   }

   public function onRun(int $currentTick): void{
      foreach($this->getPlugin()->getServer()->getOnlinePlayers() as $player){
         QuestManager::onQuestEvent($player, "online");
      }
   }

}