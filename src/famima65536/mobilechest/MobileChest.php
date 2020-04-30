<?php

namespace famima65536\mobilechest;

use pocketmine\block\Chest as BlockChest;
use pocketmine\tile\Chest as TileChest;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\plugin\PluginBase;

class MobileChest extends PluginBase {

    const TAG_MOBILE_CHEST = "mobilechest";

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }

    public function makeChestBlockIntoItem(BlockChest $chest) :Item {
        /** @var TileChest */
        $chestTile = $chest->getLevel()->getTile($chest);
        $chestInventory = $chestTile->getRealInventory(); // is for never duplicating items

        $contentsNbtSerialized = array_map(function(Item $item){
            return $item->nbtSerialize();
        }, $chestInventory->getContents());

        $itemInfo = array_map(function(Item $item) {
            return "{$item->getName()}*{$item->getCount()}";
        }, $chestInventory->getContents());
        $chestInventory->clearAll(); //for no drop items
        $chest = Item::get(ItemIds::CHEST);
        $chest->getNamedTag()->setTag(
            new ListTag(self::TAG_MOBILE_CHEST, $contentsNbtSerialized)
        );

        $chest->setLore($itemInfo);

        return $chest;
    }


    public function setChestContentsFromMobileChest(BlockChest $chest, Item $chestItem) {
        /** @var TileChest */
        $chestTile = $chest->getLevel()->getTile($chest);
        if($chestTile == null)return;
        $contents = $this->getChestContentsInChest($chestItem);
        $chestTile->getRealInventory()->setContents($contents);
    }

    /**
     * @param Item $chest 
     * @return Item[]
     */
    private function getChestContentsInChest(Item $chest) {
        $itemNbtSeriarizedList = $chest->getNamedTag()->getListTag(self::TAG_MOBILE_CHEST);
        if($itemNbtSeriarizedList == null) return [];

        $contentsNbtSerialized = $itemNbtSeriarizedList->getAllValues();

        $contents = array_map(function(CompoundTag $itemNbtSeriarized){
            return Item::nbtDeserialize($itemNbtSeriarized);
        }, $contentsNbtSerialized);

        return $contents;

    }

}