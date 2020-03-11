<?php

namespace hmmhmmmm\quest\utils;

use hmmhmmmm\quest\Quest;
use hmmhmmmm\quest\QuestData;

use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;

class QuestUtils{
   private $plugin;

   public function __construct(){
      $this->plugin = Quest::getInstance();
   }
   
   public function getPlugin(): Quest{
      return $this->plugin;
   }
   
   private function makeSlapperNBT(string $type, Player $player, string $name, string $cmd): CompoundTag{
      $nbt = Entity::createBaseNBT($player, null, $player->getYaw(), $player->getPitch());
      $nbt->setShort("Health", 1);
      $cmds = [new StringTag($cmd, $cmd)];
      $nbt->setTag(new CompoundTag("Commands", $cmds));
      $nbt->setString("MenuName", "");
      $nbt->setString("CustomName", $name);
      $nbt->setString("SlapperVersion", 1.5);
      if($type === "Human") {
         $player->saveNBT();
         $inventoryTag = $player->namedtag->getListTag("Inventory");
         assert($inventoryTag !== null);
         $nbt->setTag(clone $inventoryTag);
         $skinTag = $player->namedtag->getCompoundTag("Skin");
         assert($skinTag !== null);
         $nbt->setTag(clone $skinTag);
      }
      return $nbt;
   }
   
   public function makeSlapper(Player $player): void{
      if(isset($this->plugin->array["slapper"][$player->getName()]["get"])){
         $quest = $this->plugin->array["slapper"][$player->getName()]["get"];
         $text = QuestData::getQuestDataInfo($quest);
         $nbt = $this->makeSlapperNBT($this->plugin->getConfig()->getNested("slapper-type"), $player, $text, "quest add {player} ".$quest);
         $entity = Entity::createEntity("Slapper".$this->plugin->getConfig()->getNested("slapper-type"), $player->getLevel(), $nbt);
         $entity->setNameTag($text);
         $entity->setNameTagVisible(true);
         $entity->setNameTagAlwaysVisible(true);
         $entity->namedtag->setString("slapper_Quest", $quest);
         $entity->spawnToAll();
         $player->sendMessage($this->plugin->getPrefix()." ".$this->plugin->getLanguage()->getTranslate(
            "questutils.makeslapper.complete"
         ));
         unset($this->plugin->array["slapper"][$player->getName()]);
      }
   }
   
   public static function getItemToString(Item $item): string{
      $enchantName = [
         "PROTECTION",
         "FIRE_PROTECTION",
         "FEATHER_FALLING",
         "BLAST_PROTECTION",
         "PROJECTILE_PROTECTION",
         "THORNS",
         "RESPIRATION",
         "DEPTH_STRIDER",
         "AQUA_AFFINITY",
         "SHARPNESS",
         "SMITE",
         "BANE_OF_ARTHROPODS",
         "KNOCKBACK",
         "FIRE_ASPECT",
         "LOOTING",
         "EFFICIENCY",
         "SILK_TOUCH",
         "UNBREAKING",
         "FORTUNE",
         "POWER",
         "PUNCH",
         "FLAME",
         "INFINITY",
         "LUCK_OF_THE_SEA",
         "LURE",
         "FROST_WALKER",
         "MENDING",
         "BINDING",
         "VANISHING",
         "IMPALING",
         "RIPTIDE",
         "LOYALTY",
         "CHANNELING"
      ];
      if($item->hasEnchantments()){
         $e = [];
         foreach($item->getEnchantments() as $ent){
            $e[] = "\n• Enchant ".$enchantName[$ent->getId()]." Level ".$ent->getLevel();
         }
         $enchant = implode("", $e);
      }else{
         $enchant = "";
      }
      if($item->hasCustomName()){
         $customname = "\n• CustomName ".$item->getCustomName();
      }else{
         $customname = "";
      }
      return "\n• ".$item->getName()."\n• Amount ".$item->getCount().$customname.$enchant."\n";
   }
   
}