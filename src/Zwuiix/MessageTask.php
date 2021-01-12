<?php
namespace Zwuiix;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use Zwuiix\Main;

class MessageTask extends Task{

    private $tp;
    private $sender;
    private $timer = 6;

    public function __construct(Main $tp, Player $sender)
    {
        $this->tp = $tp;
        $this->sender = $sender;
    }

    public function onRun(int $currentTick)
    {

        $sender = $this->sender;
        $this->timer--;
        $sender->sendPopup("§fTeleport in : " . $this->timer);
        if ($this->timer < 1){
            $this->tp->getScheduler()->cancelTask($this->getTaskId());
        }
    }
}