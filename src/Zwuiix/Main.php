<?php

namespace Zwuiix;

use PhpParser\Node\Expr\Closure;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\entity\{Effect, EffectInstance};
use pocketmine\Server;
use pocketmine\scheduler\Task;
use pocketmine\event\player\PlayerQuitEvent;
use Zwuiix\MessageTask;

class Main extends PluginBase implements Listener {

	private $lastExec = [];

	public function onEnable() {
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
      if($cmd->getName() == "spawn"){
        if ($sender instanceof Player) {
          $name = $sender->getName();
          if ((isset($this->lastExec[$name])) && (($this->lastExec[$name] + 5 + 2) > (microtime(true)))) {
            $sender->sendMessage("§cPlease wait before placing this order.");
          } else {
            $oPosition = $sender->getPosition();
            $this->getScheduler()->scheduleDelayedTask(new ClosureTask(function ($curentTick) use ($oPosition, $sender): void {
              if(!$sender instanceof Player) return;
              if($sender->distance($oPosition) >= 1){
                $sender->sendMessage("§cTeleportation canceled. You have moved.");
                $sender->removeEffect(Effect::BLINDNESS);
                return;
              }
              $sender->teleport($this->getServer()->getDefaultLevel()->getSpawnLocation());
              $sender->sendMessage("§aYou've been teleported to the spawn.");
              $sender->removeEffect(Effect::BLINDNESS);
            }), 20*5);
            $sender->sendMessage("§7Teleportation begins in 5 seconds. Do not move.");
            $effect = new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 20 * 8, 1, false);
            $sender->addEffect($effect);
            $this->getScheduler()->scheduleRepeatingTask(new MessageTask($this, $sender), 20);
            $this->lastExec[$name] = microtime(true);
          }
          if (!isset($this->lastExec[$name])) {
            $this->lastExec[$name] = microtime(true);
          }
        } else {
          $sender->sendMessage("§cUse the in-game command.");
        }
      }
      return true;
    }

	public function onPlayerQuit(PlayerQuitEvent $evt) {
		unset($this->lastExec[$evt->getPlayer()->getName()]);
	}
}
