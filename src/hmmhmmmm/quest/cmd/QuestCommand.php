<?php

namespace hmmhmmmm\quest\cmd;

use hmmhmmmm\quest\Quest;
use hmmhmmmm\quest\PlayerQuest;
use hmmhmmmm\quest\QuestData;
use hmmhmmmm\quest\QuestManager;
use hmmhmmmm\quest\utils\QuestUtils;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class QuestCommand extends Command{
   private $plugin;
   private $prefix;
   private $lang;

   public function __construct(Quest $plugin){
      parent::__construct("quest");
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
   
   public function sendConsoleError(CommandSender $sender): void{
      $sender->sendMessage($this->lang->getTranslate(
         "command.consoleError"
      ));
   }

   public function sendPermissionError(CommandSender $sender): void{
      $sender->sendMessage($this->lang->getTranslate(
         "command.permissionError"
      ));
   }
   
   public function sendHelp(CommandSender $sender): void{
      $sender->sendMessage($this->getPrefix()." : §fCommand");    
      if($sender->hasPermission("quest.command.info")){
         $sender->sendMessage("§a".$this->lang->getTranslate(
            "command.info.usage"
         )." : ".$this->lang->getTranslate(
            "command.info.description"
         ));
      }
      if($sender->hasPermission("quest.command.list")){
         $sender->sendMessage("§a".$this->lang->getTranslate(
            "command.list.usage"
         )." : ".$this->lang->getTranslate(
            "command.list.description"
         ));
      }
      if($sender->hasPermission("quest.command.remove")){
         $sender->sendMessage("§a".$this->lang->getTranslate(
            "command.remove.usage"
         )." : ".$this->lang->getTranslate(
            "command.remove.description"
         ));
      }
      if($sender->hasPermission("quest.command.add")){
         $sender->sendMessage("§a".$this->lang->getTranslate(
            "command.add.usage"
         )." : ".$this->lang->getTranslate(
            "command.add.description"
         ));
      }
      if($sender->hasPermission("quest.command.data.event")){
         $sender->sendMessage("§a".$this->lang->getTranslate(
            "command.data.event.usage"
         )." : ".$this->lang->getTranslate(
            "command.data.event.description"
         ));
      }
      if($sender->hasPermission("quest.command.data.create")){
         $sender->sendMessage("§a".$this->lang->getTranslate(
            "command.data.create.usage"
         )." : ".$this->lang->getTranslate(
            "command.data.create.description"
         ));
      }
      if($sender->hasPermission("quest.command.data.remove")){
         $sender->sendMessage("§a".$this->lang->getTranslate(
            "command.data.remove.usage"
         )." : ".$this->lang->getTranslate(
            "command.data.remove.description"
         ));
      }
      if($sender->hasPermission("quest.command.data.list")){
         $sender->sendMessage("§a".$this->lang->getTranslate(
            "command.data.list.usage"
         )." : ".$this->lang->getTranslate(
            "command.data.list.description"
         ));
      }
      if($sender->hasPermission("quest.command.data.setlimit")){
         $sender->sendMessage("§a".$this->lang->getTranslate(
            "command.data.setlimit.usage"
         )." : ".$this->lang->getTranslate(
            "command.data.setlimit.description"
         ));
      }
      if($sender->hasPermission("quest.command.data.addlimit")){
         $sender->sendMessage("§a".$this->lang->getTranslate(
            "command.data.addlimit.usage"
         )." : ".$this->lang->getTranslate(
            "command.data.addlimit.description"
         ));
      }
      if($sender->hasPermission("quest.command.data.slapperget")){
         $sender->sendMessage("§a".$this->lang->getTranslate(
            "command.data.slapper_get.usage"
         )." : ".$this->lang->getTranslate(
            "command.data.slapper_get.description"
         ));
      }
      
   }
   
   public function execute(CommandSender $sender, string $commandLabel, array $args): void{
      if(empty($args)){
         if($sender instanceof Player){
            $this->getPlugin()->getQuestForm()->Menu($sender);
            $sender->sendMessage($this->lang->getTranslate(
               "command.sendHelp.empty"
            ));
         }else{
            $this->sendHelp($sender);  
         }
         return;
      }
      $sub = array_shift($args);
      if(isset($sub)){
         switch($sub){
            case "help":
               $this->sendHelp($sender);
               break;
            case "info":
               if(!$sender->hasPermission("quest.command.info")){
                  $this->sendPermissionError($sender);
                  return;
               }
               $sender->sendMessage($this->getPlugin()->getPluginInfo());
               break;
            case "data":
               if(count($args) < 1){
                  $sender->sendMessage($this->lang->getTranslate(
                     "command.data.error1",
                     [
                        $this->lang->getTranslate(
                           "command.data.usage"
                        )
                     ]
                  ));
                  return;
               }
               $sub_data = array_shift($args);
               switch($sub_data){
                  case "event":
                     if(!$sender->hasPermission("quest.command.data.event")){
                        $this->sendPermissionError($sender);
                        return;
                     }
                     foreach(QuestManager::getQuestEventList() as $key => $value){
                        $sender->sendMessage($this->lang->getTranslate(
                           "command.data.event.complete",
                           [$key, $value]
                        ));
                     }
                     break;
                  case "create":
                     if(!$sender->hasPermission("quest.command.data.create")){
                        $this->sendPermissionError($sender);
                        return;
                     }
                     if(count($args) < 6){
                        $sender->sendMessage($this->lang->getTranslate(
                           "command.data.create.error1",
                           [
                              $this->lang->getTranslate(
                                 "command.data.create.usage"
                              )
                           ]
                        ));
                        return;
                     }
                     $quest = array_shift($args);
                     if($this->getPlugin()->getDatabase()->exists($quest)){
                        $sender->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                           "command.data.create.error2"
                        ));
                        return;
                     }
                     $max = (int) array_shift($args);
                     if(!is_numeric($max)){
                        $sender->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                           "command.data.create.error3"
                        ));
                        return;
                     }
                     $event = array_shift($args);
                     if(!in_array($event, array_keys($this->getPlugin()->questEvent))){
                        $sender->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                           "command.data.create.error4",
                           [$event]
                        ));
                        return;
                     }
                     $info = array_shift($args);
                     $infoAward = array_shift($args);
                     $commandAward = implode(" ", $args);
                     $questUtils = new QuestUtils();
                     switch($event){
                        case "trading":
                           if(!$sender instanceof Player){
                              $this->sendConsoleError($sender);
                              return;
                           }
                           $item = $sender->getInventory()->getItemInHand();
                           if($item->getId() === 0){
                              $sender->sendMessage($this->getPrefix()." ".
                                 $this->lang->getTranslate(
                                    "command.data.create.error5"
                                 )
                              );
                              return;
                           }
                           QuestData::createQuestData($quest, $max, $event, $info, $infoAward, $commandAward);
                           QuestData::addQuestDataTrading($quest, $item);
                           $event_value = explode("#", $this->getPlugin()->questEvent["trading"]); 
                           $sender->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                              "command.data.create.complete2",
                              [$quest, $event, $event_value[0], $questUtils->getItemToString($item)]
                           ));
                           break;
                        default:
                           QuestData::createQuestData($quest, $max, $event, $info, $infoAward, $commandAward);
                           $event_value = $this->getPlugin()->questEvent[$event]; 
                           $sender->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                              "command.data.create.complete1",
                              [$quest, $event, $event_value]
                           ));
                           break;
                     }
                     break;
                  case "remove":
                     if(!$sender->hasPermission("quest.command.data.remove")){
                        $this->sendPermissionError($sender);
                        return;
                     }
                     if(count($args) < 1){
                        $sender->sendMessage($this->lang->getTranslate(
                           "command.data.remove.error1",
                           [
                              $this->lang->getTranslate(
                                 "command.data.remove.usage"
                              )
                           ]
                        ));
                        return;
                     }
                     $quest = array_shift($args);
                     if(!$this->getPlugin()->getDatabase()->exists($quest)){
                        $sender->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                           "command.data.remove.error2",
                           [$quest]
                        ));
                        return;
                     }
                     QuestData::removeQuestData($quest);
                     $sender->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                        "command.data.remove.complete",
                        [$quest]
                     ));
                     break;
                  case "list":
                     if(!$sender->hasPermission("quest.command.data.list")){
                        $this->sendPermissionError($sender);
                        return;
                     }
                     if(QuestData::getCountQuestData() == 0){
                        $sender->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                           "command.data.list.error1"
                        ));
                        return;
                     }
                     $sender->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                        "command.data.list.complete",
                        [implode(", ", $this->getPlugin()->getDatabase()->getAll())]
                     ));
                     break;
                  case "setlimit":
                     if(!$sender->hasPermission("quest.command.data.setlimit")){
                        $this->sendPermissionError($sender);
                        return;
                     }
                     if(count($args) < 1){
                        $sender->sendMessage($this->lang->getTranslate(
                           "command.data.setlimit.error1",
                           [
                              $this->lang->getTranslate(
                                 "command.data.setlimit.usage"
                              )
                           ]
                        ));
                        return;
                     }
                     $quest = array_shift($args);
                     if(!$this->getPlugin()->getDatabase()->exists($quest)){
                        $sender->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                           "command.data.setlimit.error2",
                           [$quest]
                        ));
                        return;
                     }
                     QuestData::setQuestDataLimit($quest);
                     $sender->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                        "command.data.setlimit.complete",
                        [$quest]
                     ));
                     break;
                  case "addlimit":
                     if(!$sender->hasPermission("quest.command.data.addlimit")){
                        $this->sendPermissionError($sender);
                        return;
                     }
                     if(count($args) < 2){
                        $sender->sendMessage($this->lang->getTranslate(
                           "command.data.addlimit.error1",
                           [
                              $this->lang->getTranslate(
                                 "command.data.addlimit.usage"
                              )
                           ]
                        ));
                        return;
                     }
                     $quest = array_shift($args);
                     if(!$this->getPlugin()->getDatabase()->exists($quest)){
                        $sender->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                           "command.data.addlimit.error2",
                           [$quest]
                        ));
                        return;
                     }
                     if(!QuestData::isQuestDataLimit($quest)){
                        $sender->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                           "command.data.addlimit.error3"
                        ));
                        return;
                     }
                     $playerName = array_shift($args);
                     $playerName = strtolower($playerName);
                     if(in_array($playerName, QuestData::getQuestDataLimit($quest))){
                        $sender->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                           "command.data.addlimit.error4"
                        ));
                        return;
                     }
                     QuestData::addQuestDataLimit($quest, $playerName);
                     $sender->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                        "command.data.addlimit.complete",
                        [$quest, $playerName]
                     ));
                     break;
                  case "slapper_get":
                     if(!$sender->hasPermission("quest.command.data.slapperget")){
                        $this->sendPermissionError($sender);
                        return;
                     }
                     if(count($args) < 1){
                        $sender->sendMessage($this->lang->getTranslate(
                           "command.data.slapper_get.error1",
                           [
                              $this->lang->getTranslate(
                                 "command.data.slapper_get.usage"
                              )
                           ]
                        ));
                        return;
                     }
                     $quest = array_shift($args);
                     if(!$this->getPlugin()->getDatabase()->exists($quest)){
                        $sender->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                           "command.data.slapper_get.error2",
                           [$quest]
                        ));
                        return;
                     }
                     $questUtils = new QuestUtils();
                     $this->getPlugin()->array["slapper"][$sender->getName()]["get"] = $quest;
                     $questUtils->makeSlapper($sender);
                     break;
               }
               break;
            case "list":
               if(!$sender->hasPermission("quest.command.list")){
                  $this->sendPermissionError($sender);
                  return;
               }
               if(!$sender instanceof Player){
                  $this->sendConsoleError($sender);
                  return;
               }
               $playerQuest = new PlayerQuest($sender->getName());
               if($playerQuest->getCountQuest() == 0){
                  $sender->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                     "command.list.error1"
                  ));
                  return;
               }
               foreach($playerQuest->getQuest() as $quest){
                  $sender->sendMessage($playerQuest->getQuestInfo($quest));
               }
               break;
            case "remove":
               if(!$sender->hasPermission("quest.command.remove")){
                  $this->sendPermissionError($sender);
                  return;
               }
               if(!$sender instanceof Player){
                  $this->sendConsoleError($sender);
                  return;
               }
               if(count($args) < 1){
                  $sender->sendMessage($this->lang->getTranslate(
                     "command.remove.error1",
                     [
                        $this->lang->getTranslate(
                           "command.remove.usage"
                        )
                     ]
                  ));
                  return;
               }
               $playerQuest = new PlayerQuest($sender->getName());
               if($playerQuest->getCountQuest() == 0){
                  $sender->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                     "command.remove.error2"
                  ));
                  return;
               }
               $quest = array_shift($args);
               if(!$playerQuest->isQuest($quest)){
                  $sender->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                     "command.remove.error3",
                     [$quest]
                  ));
                  return;
               }
               $playerQuest->removeQuest($quest);
               $sender->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                  "command.remove.complete",
                  [$quest]
               ));
               break;
            case "add":
            case "get":
               if(!$sender->hasPermission("quest.command.add")){
                  $this->sendPermissionError($sender);
                  return;
               }
               if(count($args) < 2){
                  $sender->sendMessage($this->lang->getTranslate(
                     "command.add.error1",
                     [
                        $this->lang->getTranslate(
                           "command.add.usage"
                        )
                     ]
                  ));
                  return;
               }
               $player = $this->getPlugin()->getServer()->getPlayer(array_shift($args));
               if($player === null){
			      $sender->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                     "command.add.error2"
                  ));
			      return;
               }
               $quest = array_shift($args);
               if(!$this->getPlugin()->getDatabase()->exists($quest)){
                  $player->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                     "command.add.error3",
                     [$quest]
                  ));
                  return;
               }
               $playerName = strtolower($player->getName());
               if(QuestData::isQuestDataLimit($quest)){
                  if(in_array($playerName, QuestData::getQuestDataLimit($quest))){
                     $player->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                        "command.add.error4"
                     ));
                     return;
                  }
               }
               $playerQuest = new PlayerQuest($player->getName());
               $questUtils = new QuestUtils();
               if($playerQuest->isQuest($quest)){
                  if($playerQuest->isQuestTrading($quest)){
                     $item = $playerQuest->getQuestTrading($quest);
                     if($player->getInventory()->contains($item)){
                        $player->getInventory()->removeItem($item);
                        QuestManager::onQuestTrading($player, $quest);
                     }else{
                        $text = $this->lang->getTranslate(
                           "command.add.error5",
                           [$questUtils->getItemToString($item)]
                        );
                        $player->sendMessage($playerQuest->getQuestInfo($quest, $text));
                     }
                  }else{
                     $player->sendMessage($playerQuest->getQuestInfo($quest));
                  }
                  return;
               }
               QuestManager::addQuest($player, $quest);
               $sender->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                  "command.add.complete1",
                  [$player->getName(), $quest]
               ));
               $player->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                  "command.add.complete2",
                  [$quest]
               ));
               break;
            default:
               $this->sendHelp($sender);
               break;
         }
      }
   }
   
}