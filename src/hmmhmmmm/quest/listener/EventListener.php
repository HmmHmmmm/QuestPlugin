<?php

namespace hmmhmmmm\quest\listener;

use hmmhmmmm\quest\Quest;
use hmmhmmmm\quest\PlayerQuest;
use hmmhmmmm\quest\QuestManager;

use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerLoginEvent;

class EventListener implements Listener{
   private $plugin;
   private $prefix;
   private $lang;
   
   public function __construct(Quest $plugin){
      $this->plugin = $plugin;
      $this->prefix = $this->plugin->getPrefix();
      $this->lang = $this->plugin->getLanguage();
   }
   
   public function getPlugin(): Quest{
      return $this->plugin;
   }
   
   public function getPrefix(): string{
      return $this->prefix;
   }
   
   public function onPlayerLogin(PlayerLoginEvent $event): void{
      $player = $event->getPlayer();
      $playerQuest = new PlayerQuest($player->getName());
      if($this->plugin->getConfig()->getNested("playerdata-database") == "yml"){
         if(!$playerQuest->getDatabase()->isData()){
            $playerQuest->getDatabase()->register();
         }
      }
   }
   
   public function onBreakBlock(BlockBreakEvent $event){
      $player = $event->getPlayer();
      $block = $event->getBlock();
      QuestManager::onQuestEvent($player, "breakblock");
   }
   
   public function onPlaceBlock(BlockPlaceEvent $event){
      $player = $event->getPlayer();
      $block = $event->getBlock();
      QuestManager::onQuestEvent($player, "placeblock");
   }
   
   public function onPlayerDeath(PlayerDeathEvent $event){
      $player = $event->getEntity();
      $cause = $player->getLastDamageCause();
      if($player instanceof Player){
         if($cause->getCause() == EntityDamageEvent::CAUSE_ENTITY_ATTACK
            || $cause->getCause() == EntityDamageEvent::CAUSE_PROJECTILE
         ){
            if($cause instanceof EntityDamageByEntityEvent){
               $damager = $cause->getDamager();
	           if($damager instanceof Player){
	              QuestManager::onQuestEvent($damager, "kill_player");
	           }
            }
         }
      }
   }
   
   public function onEntityDeath(EntityDeathEvent $event){
      $entity = $event->getEntity();
      $cause = $entity->getLastDamageCause();
      if($cause->getCause() == EntityDamageEvent::CAUSE_ENTITY_ATTACK
         || $cause->getCause() == EntityDamageEvent::CAUSE_PROJECTILE
      ){
         if($cause instanceof EntityDamageByEntityEvent){
            $damager = $cause->getDamager();
	        if($damager instanceof Player){
	           if($entity instanceof Entity){
	              QuestManager::onQuestEvent($damager, "kill_entity");
	           }
	        }
         }
      }
   }
   
}