<?php

namespace hmmhmmmm\quest\ui;

use hmmhmmmm\quest\Quest;
use hmmhmmmm\quest\PlayerQuest;
use hmmhmmmm\quest\QuestData;
use hmmhmmmm\quest\QuestManager;
use hmmhmmmm\quest\utils\QuestUtils;
use xenialdan\customui\elements\Button;
use xenialdan\customui\elements\Dropdown;
use xenialdan\customui\elements\Input;
use xenialdan\customui\elements\Label;
use xenialdan\customui\elements\Slider;
use xenialdan\customui\elements\StepSlider;
use xenialdan\customui\elements\Toggle;
use xenialdan\customui\windows\CustomForm;
use xenialdan\customui\windows\ModalForm;
use xenialdan\customui\windows\SimpleForm;

use pocketmine\Player;
use pocketmine\item\Item;

class QuestForm{
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
   
   public function Menu(Player $player, string $content = ""): void{
      $playerQuest = new PlayerQuest($player->getName());
      $form = new SimpleForm(
         $this->getPrefix()." Menu",
         $content
      );
      $button = [
         $this->lang->getTranslate(
            "form.menu.button1"
         ) => 0,
         $this->lang->getTranslate(
            "form.menu.button2",
            [$playerQuest->getCountQuest()]
         ) => 1
      ];
      foreach($button as $buttons => $value){
         $form->addButton(new Button($buttons));
      }
      $form->setCallable(function ($player, $data) use ($playerQuest, $button){
         if(!($data === null)){
            switch($button[$data]){
               case 0:
                  if(!$player->hasPermission("quest.command.data.gui")){
                     $text = $this->lang->getTranslate(
                        "form.menu.error1"
                     );
                     $this->Menu($player, $text);
                     return;
                  }
                  $this->DataMenu($player);
                  break;
               case 1:
                  if(!$player->hasPermission("quest.command.list")){
                     $text = $this->lang->getTranslate(
                        "form.menu.error1"
                     );
                     $this->Menu($player, $text);
                     return;
                  }
                  if($playerQuest->getCountQuest() == 0){
                     $text = $this->lang->getTranslate(
                        "form.menu.error2"
                     );
                     $this->Menu($player, $text);
                     return;
                  }
                  $this->List($player);
                  break;
            }
            
         }
      });
      $form->setCallableClose(function (Player $player){
         //??
      });
      $player->sendForm($form);
   }
   
   public function List(Player $player, string $content = ""): void{
      $playerQuest = new PlayerQuest($player->getName());
      $form = new SimpleForm(
         $this->getPrefix()." List",
         $content
      );
      $button = [];
      if($playerQuest->getCountQuest() !== 0){
         foreach($playerQuest->getQuest() as $questName){
            $button[] = $questName;
         }
         foreach($button as $buttons){
            $form->addButton(new Button($buttons));
         }
      }
      $form->setCallable(function ($player, $data){
         if(!($data === null)){
            $this->Edit($player, $data);
         }
      });
      $form->setCallableClose(function (Player $player){
         //??
      });
      $player->sendForm($form);
   }
   
   public function Edit(Player $player, string $questName, string $content = ""): void{
      $playerQuest = new PlayerQuest($player->getName());
      $form = new SimpleForm(
         $this->getPrefix()." Edit ".$questName,
         $content."\n".$playerQuest->getQuestInfo($questName)
      );
      $button = [
         $this->lang->getTranslate(
            "form.edit.button1"
         ) => 0
      ];
      foreach($button as $buttons => $value){
         $form->addButton(new Button($buttons));
      }
      $form->setCallable(function ($player, $data) use ($button, $questName){
         if(!($data === null)){
            switch($button[$data]){
               case 0:
                  $this->Remove($player, $questName);
                  break;
            }
            
         }
      });
      $form->setCallableClose(function (Player $player){
         //??
      });
      $player->sendForm($form);
   }
   
   public function Remove(Player $player, string $questName): void{
      $playerQuest = new PlayerQuest($player->getName());
      $form = new ModalForm(
         $this->getPrefix()." Remove ".$questName,
         $this->getPrefix()." ".$this->lang->getTranslate(
            "form.remove.content",
            [$questName]
         ),
         $this->lang->getTranslate(
            "form.remove.button1"
         ),
         $this->lang->getTranslate(
            "form.remove.button2"
         )
      );
      $form->setCallable(function ($player, $data) use ($playerQuest, $questName){
         if(!($data === null)){
            if($data){
               $playerQuest->removeQuest($questName);
               $player->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                  "command.remove.complete",
                  [$questName]
               ));
            }
         }
      });
      $form->setCallableClose(function (Player $player){
         //??
      });
      $player->sendForm($form);
   }
   
   public function DataMenu(Player $player, string $content = ""): void{
      $form = new SimpleForm(
         $this->getPrefix()." Data Menu",
         $content
      );
      $button = [];
      $button[] = $this->lang->getTranslate(
         "form.data.menu.button1"
      );
      if(QuestData::getCountQuestData() !== 0){
         foreach(QuestData::getQuestData() as $questName){
            $button[] = $questName;
         }
      }
      foreach($button as $buttons){
         $form->addButton(new Button($buttons));
      }
      $form->setCallable(function ($player, $data) use ($button){
         if(!($data === null)){
            switch($data){
               case $button[0]:
                  $this->DataEvent($player);
                  break;
               default:
                  $this->DataEdit($player, $data);
                  break;
            }
            
         }
      });
      $form->setCallableClose(function (Player $player){
         //??
      });
      $player->sendForm($form);
   }
   
   public function DataEvent(Player $player): void{
      $form = new SimpleForm(
         $this->getPrefix()." Data Event",
         $this->lang->getTranslate(
            "form.data.event.content"
         )
      );
      $button = [];
      foreach($this->getPlugin()->questEvent as $key => $value){
         $button[] = $key;
      }
      foreach($button as $buttons){
         $form->addButton(new Button($buttons));
      }
      $form->setCallable(function ($player, $data){
         if(!($data === null)){
            $this->DataCreate($player, $data);
         }
      });
      $form->setCallableClose(function (Player $player){
         //??
      });
      $player->sendForm($form);
   }
   
   public function DataCreate(Player $player, string $event, string $content = ""): void{
      $form = new CustomForm(
         $this->getPrefix()." Data Create Event ".$event
      );
      $form->addElement(new Label($content));
      $input = [
         $this->lang->getTranslate(
            "form.data.create.input1"
         ) => "test",
         $this->lang->getTranslate(
            "form.data.create.input2"
         ) => "10",
         $this->lang->getTranslate(
            "form.data.create.input3"
         ) => "??",
         $this->lang->getTranslate(
            "form.data.create.input4"
         ) => "Diamond64",
         $this->lang->getTranslate(
            "form.data.create.input5"
         ) => "give {player} 264 64"
      ];
      foreach($input as $inputs => $value){
         $form->addElement(new Input($inputs, $value));
      }
      $form->setCallable(function ($player, $data) use ($event){
         if($data == null){
            return;
         }
         $name = explode(" ", $data[1]); 
         if($name[0] == null){
            $text = $this->lang->getTranslate(
               "form.data.create.error1"
            );
            $this->DataCreate($player, $event, $text);
            return;
         }
         $name = $name[0];
         if(QuestData::isQuestData($name)){
            $text = $this->lang->getTranslate(
               "form.data.create.error2",
               [$name]
            );
            $this->DataCreate($player, $event, $text);
            return;
         }
         $max = explode(" ", $data[2]); 
         if($max[0] == null){
            $text = $this->lang->getTranslate(
               "form.data.create.error3"
            );
            $this->DataCreate($player, $event, $text);
            return;
         }
         if(!is_numeric($max[0])){
            $text = $this->lang->getTranslate(
               "form.data.create.error3"
            );
            $this->DataCreate($player, $event, $text);
            return;
         }
         $max = (int) $max[0];
         $info = explode(" ", $data[3]); 
         if($info[0] == null){
            $text = $this->lang->getTranslate(
               "form.data.create.error4"
            );
            $this->DataCreate($player, $event, $text);
            return;
         }
         $info = $data[3];
         $infoAward = explode(" ", $data[4]); 
         if($infoAward[0] == null){
            $text = $this->lang->getTranslate(
               "form.data.create.error5"
            );
            $this->DataCreate($player, $event, $text);
            return;
         }
         $infoAward = $data[4];
         $commandAward = explode(" ", $data[5]); 
         if($commandAward[0] == null){
            $text = $this->lang->getTranslate(
               "form.data.create.error6"
            );
            $this->DataCreate($player, $event, $text);
            return;
         }
         $commandAward = $data[5];
         $questUtils = new QuestUtils();
         switch($event){
            case "trading":
               $item = $player->getInventory()->getItemInHand();
               if($item->getId() === 0){
                  $text = $this->lang->getTranslate(
                     "form.data.create.error7"
                  );
                  $this->DataCreate($player, $event, $text);
                  return;
               }
               QuestData::createQuestData($name, $max, $event, $info, $infoAward, $commandAward);
               QuestData::addQuestDataTrading($name, $item);
               $event_value = explode("#", $this->getPlugin()->questEvent["trading"]); 
               $player->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                  "command.data.create.complete2",
                  [$name, $event, $event_value[0], $questUtils->getItemToString($item)]
               ));
               break;
            default:
               QuestData::createQuestData($name, $max, $event, $info, $infoAward, $commandAward);
               $event_value = $this->getPlugin()->questEvent[$event]; 
               $player->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                  "command.data.create.complete1",
                  [$name, $event, $event_value]
               ));
               break;
         }
      });
      $form->setCallableClose(function (Player $player){
         //??
      });
      $player->sendForm($form);
   }
   
   public function DataEdit(Player $player, string $questName, string $content = ""): void{
      $form = new SimpleForm(
         $this->getPrefix()." Edit ".$questName,
         $content
      );
      $button = [
         $this->lang->getTranslate(
            "form.data.edit.button1"
         ) => 0,
         $this->lang->getTranslate(
            "form.data.edit.button2"
         ) => 1,
         $this->lang->getTranslate(
            "form.data.edit.button3"
         ) => 2,
         $this->lang->getTranslate(
            "form.data.edit.button4"
         ) => 3,
         $this->lang->getTranslate(
            "form.data.edit.button5"
         ) => 4,
         $this->lang->getTranslate(
            "form.data.edit.button6"
         ) => 5
      ];
      foreach($button as $buttons => $value){
         $form->addButton(new Button($buttons));
      }
      $form->setCallable(function ($player, $data) use ($button, $questName){
         if(!($data === null)){
            switch($button[$data]){
               case 0:
                  $this->DataEdit2($player, $questName);
                  break;
               case 1:
                  QuestData::setQuestDataLimit($questName);
                  $player->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                     "command.data.setlimit.complete",
                     [$questName]
                  ));
                  break;
               case 2:
                  QuestData::resetQuestDataLimit($questName);
                  $player->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                     "form.data.edit.resetlimit.complete",
                     [$questName]
                  ));
                  break;
               case 3:
                  QuestData::removeQuestDataLimit($questName);
                  $player->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                     "form.data.edit.unlimit.complete",
                     [$questName]
                  ));
                  break;
               case 4:
                  $questUtils = new QuestUtils();
                  $this->getPlugin()->array["slapper"][$player->getName()]["get"] = $questName;
                  $questUtils->makeSlapper($player);
                  break;
               case 5:
                  $this->DataRemove($player, $questName);
                  break;
            }
            
         }
      });
      $form->setCallableClose(function (Player $player){
         //??
      });
      $player->sendForm($form);
   }
   
   public function DataEdit2(Player $player, string $questName, string $content = ""): void{
      $form = new CustomForm(
         $this->getPrefix()." Data Edit ".$questName
      );
      $form->addElement(new Label($content));
      $input = [
         $this->lang->getTranslate(
            "form.data.create.input2"
         ) => "10",
         $this->lang->getTranslate(
            "form.data.create.input3"
         ) => "??",
         $this->lang->getTranslate(
            "form.data.create.input4"
         ) => "Diamond64",
         $this->lang->getTranslate(
            "form.data.create.input5"
         ) => "give {player} 264 64"
      ];
      foreach($input as $inputs => $value){
         $form->addElement(new Input($inputs, $value));
      }
      $form->setCallable(function ($player, $data) use ($questName){
         if($data == null){
            return;
         }
         $max = explode(" ", (int) $data[1]); 
         if($max[0] == null){
            $text = $this->lang->getTranslate(
               "form.data.create.error3"
            );
            $this->DataEdit2($player, $questName, $text);
            return;
         }
         if(!is_numeric($max[0])){
            $text = $this->lang->getTranslate(
               "form.data.create.error3"
            );
            $this->DataEdit2($player, $questName, $text);
            return;
         }
         $max = $max[0];
         $info = explode(" ", $data[2]); 
         if($info[0] == null){
            $text = $this->lang->getTranslate(
               "form.data.create.error4"
            );
            $this->DataEdit2($player, $questName, $text);
            return;
         }
         $info = $data[2];
         $infoAward = explode(" ", $data[3]); 
         if($infoAward[0] == null){
            $text = $this->lang->getTranslate(
               "form.data.create.error5"
            );
            $this->DataEdit2($player, $questName, $text);
            return;
         }
         $infoAward = $data[3];
         $commandAward = explode(" ", $data[4]); 
         if($commandAward[0] == null){
            $text = $this->lang->getTranslate(
               "form.data.create.error6"
            );
            $this->DataEdit2($player, $questName, $text);
            return;
         }
         $commandAward = $data[4];
         QuestData::editQuestData($questName, $max, $info, $infoAward, $commandAward);
         $player->sendMessage($this->getPrefix()." Successfully edit");
      });
      $form->setCallableClose(function (Player $player){
         //??
      });
      $player->sendForm($form);
   }
   
   public function DataRemove(Player $player, string $questName): void{
      $form = new ModalForm(
         $this->getPrefix()." Data Remove ".$questName,
         $this->getPrefix()." ".$this->lang->getTranslate(
            "form.remove.content",
            [$questName]
         ),
         $this->lang->getTranslate(
            "form.remove.button1"
         ),
         $this->lang->getTranslate(
            "form.remove.button2"
         )
      );
      $form->setCallable(function ($player, $data) use ($questName){
         if(!($data === null)){
            if($data){
               QuestData::removeQuestData($questName);
               $player->sendMessage($this->getPrefix()." ".$this->lang->getTranslate(
                  "command.remove.complete",
                  [$questName]
               ));
            }
         }
      });
      $form->setCallableClose(function (Player $player){
         //??
      });
      $player->sendForm($form);
   }

}