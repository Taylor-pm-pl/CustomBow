<?php

namespace YTBJero\CustomBow;

use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\Listener;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
    }
    
    public function onProjectileHit(ProjectileHitEvent $event)
    {
        $projectile = $event->getEntity();
        $entity = $projectile->getOwningEntity();
        if(!$projectile instanceof Arrow) return;
        if($entity instanceof Player && $event instanceof ProjectileHitEntityEvent){
            $target = $event->getEntityHit();
            if($target instanceof Player){
				if($this->getConfig()->getNested("sound-enable", true)){
                $pk = new PlaySoundPacket();
                $pk->x = $entity->getPosition()->getX();
                $pk->y = $entity->getPosition()->getY();
                $pk->z = $entity->getPosition()->getZ();
                $pk->volume = 1;
                $pk->pitch = 1;
				$pk->soundName = $this->getConfig()->getNested("hit-sound");
                $entity->getNetworkSession()->sendDataPacket($pk);
				}
                $message = $this->getConfig()->get("hit-message");
                if($this->getConfig()->getNested("message-enable", true)){
                    $entity->sendMessage(str_replace(['{hp}', '{damage}', '{name}', '{target}'], [$target->getHealth(), $projectile->getResultDamage(), $entity->getName(), $target->getDisplayName()], $message));
                }
                $popup = $this->getConfig()->get("hit-popup");
                if($this->getConfig()->getNested("popup-enable", true)){
                    $entity->sendPopup(str_replace(['{hp}', '{damage}', '{name}', '{target}'], [$target->getHealth(), $projectile->getResultDamage(), $entity->getName(), $target->getDisplayName()], $popup));
                }
            }
        }
	}
}
