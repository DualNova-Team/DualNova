<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\Solid;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class FlintSteel extends Tool{
	public function __construct(int $meta = 0){
		parent::__construct(self::FLINT_STEEL, $meta, "Flint and Steel");
	}

	public function onActivate(Level $level, Player $player, Block $block, Block $target, int $face, Vector3 $facePos) : bool{
		if($block->getId() === self::AIR and ($target instanceof Solid)){
			$level->setBlock($block, BlockFactory::get(Block::FIRE), \true);
			if(($player->gamemode & 0x01) === 0 and $this->useOn($block)){
				if($this->getDamage() >= $this->getMaxDurability()){
					$player->getInventory()->setItemInHand(Item::get(Item::AIR, 0, 0));
				}else{
					$this->meta++;
					$player->getInventory()->setItemInHand($this);
				}
			}

			return \true;
		}

		return \false;
	}

	public function getMaxDurability(){
		return 65;
	}
}