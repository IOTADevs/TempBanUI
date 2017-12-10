<?php

namespace TempBanUI;


use Modals\elements\Dropdown;
use Modals\elements\Input;
use Modals\elements\Label;
use Modals\elements\Slider;
use Modals\elements\Toggle;
use Modals\network\ModalFormRequestPacket;
use Modals\network\ModalFormResponsePacket;
use Modals\windows\CustomForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as C;

class Main extends PluginBase implements Listener
{

    public $prefix = C::GRAY . "[" . C::RED . "TempBanUI" . C::GRAY . "]";

    public $TempBanUI = 110;

    public $targetname;
    public $reason;
    public $time;

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info($this->prefix . " Enabled!");
    }
    public function sendbanUI(Player $player)
    {
        /*$result = $data[0];
        $this->targetname = $result;
        $this->reason = $data[1];
        $this->time = $data[2];*/
        foreach ($this->getServer()->getOnlinePlayers() as $value)
        {
            $nametag = $value->getNameTag();
        }
        $ui = new CustomForm("TempBanUI");
        $timeinput = new Slider("Time(per hours) ",1,120,1);
        $namedrop = new Dropdown('Player Name', [$nametag]);
        $reasoninput = new Input("Reason", "");
        $ui->addElement($namedrop);
        $ui->addElement($timeinput);
        $ui->addElement($reasoninput);
        $pk = new ModalFormRequestPacket();
        $pk->formId = 110;
        $pk->formData = json_encode($ui);
        $player->dataPacket($pk);
        return true;
    }
    public function DataPacketReceiveEvent(DataPacketReceiveEvent $event){
        $packet = $event->getPacket();
        $player = $event->getPlayer();
        if($packet instanceof ModalFormResponsePacket){
            $id = $packet->formId;
            $data = $packet->formData;
            $data = json_decode($data);
            if($data === Null) return true;
            if($id === 110){
                $this->getServer()->dispatchCommand(new ConsoleCommandSender(), "tempban " . $this->targetname . ' ' . $this->time . 'h ' . $this->reason);
                return true;
            }
        }
        return true;
    }
    public function onCommand(CommandSender $sender, Command $command, string $label, array $data): bool
    {
        $player = $sender->getPlayer();
        if($command->getName() == "tempbanui"){
            $this->sendbanUI($player);
            return true;
        }
        return true;
    }

    public function onDisable(){
        $this->getLogger()->info($this->prefix . " Disabled!");
    }
}