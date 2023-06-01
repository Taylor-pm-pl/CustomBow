<?php

namespace YTBJero\CustomBow;

use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\Listener;

class Main extends PluginBase implements Listener
{
    public function onEnable(): void
    {
        $this->getServer()
            ->getPluginManager()
            ->registerEvents($this, $this);
        $this->saveDefaultConfig();
    }

    public function onProjectileHit(ProjectileHitEvent $event): void
    {
        $projectile = $event->getEntity();
        $entity = $projectile->getOwningEntity();
        if (!$projectile instanceof Arrow) {
            return;
        }
        if (
            $entity instanceof Player &&
            $event instanceof ProjectileHitEntityEvent
        ) {
            $target = $event->getEntityHit();
            if ($target instanceof Player) {
                if ($this->getConfig()->getNested("sound-enable", true)) {
                    $pk = PlaySoundPacket::create(
                        soundName: $this->getConfig()->getNested("hit-sound"),
                        x: $entity->getPosition()->getX(),
                        y: $entity->getPosition()->getY(),
                        z: $entity->getPosition()->getZ(),
                        volume: 1,
                        pitch: 1
                    );
                    $entity->getNetworkSession()->sendDataPacket($pk);
                }
                $message = $this->getConfig()->get("hit-message");
                if ($this->getConfig()->getNested("message-enable", true)) {
                    $entity->sendMessage(
                        str_replace(
                            ["{hp}", "{damage}", "{name}", "{target}"],
                            [
                                $target->getHealth(),
                                $projectile->getResultDamage(),
                                $entity->getName(),
                                $target->getDisplayName(),
                            ],
                            $message
                        )
                    );
                }
                $popup = $this->getConfig()->get("hit-popup");
                if ($this->getConfig()->getNested("popup-enable", true)) {
                    $entity->sendPopup(
                        str_replace(
                            ["{hp}", "{damage}", "{name}", "{target}"],
                            [
                                $target->getHealth(),
                                $projectile->getResultDamage(),
                                $entity->getName(),
                                $target->getDisplayName(),
                            ],
                            $popup
                        )
                    );
                }
            }
        }
    }
}
