<?php

namespace famima65536\mobilechest;

use pocketmine\block\Block;
use pocketmine\block\Chest;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;

class EventListener implements Listener {


    /** @var MobileChest */
    private $plugin;

    public function __construct(MobileChest $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
    * @ignoreCancelled false
    */
    public function onBlockBreak(BlockBreakEvent $event) {
        $block  = $event->getBlock();
        if($block instanceof Chest) {
            $mobilechest = $this->plugin->makeChestBlockIntoItem($block);
            $event->setDrops([$mobilechest]);
        }
    }

    /**
    * @ignoreCancelled false
    */
    public function onBlockPlace(BlockPlaceEvent $event) {
        $block  = $event->getBlock();
        if($block instanceof Chest) {
            $item = $event->getItem();
            $player = $event->getPlayer();
            $block->place($item, $block, Block::get(0), 0, new Vector3(0, 0, 0), $player);
            $this->plugin->setChestContentsFromMobileChest($block, $item);
            $player->getInventory()->removeItem($item);
            $event->setCancelled();
        }
    }
}