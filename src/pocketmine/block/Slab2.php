<?php

/*
 *
 *  ____               _   ___     _
 * |  _ \             |　| |   \   | |
 * | | | |_   _  ____ | | | |\ \  | | _____    ______
 * | | | | | | |/ _  \| | | | \ \ | |/ _ \ \  / / _  \
 * | |_| | |_| | (_)  | |_| |  \ \| | (_) \ \/ / (_)  |
 * |____/\_____|\___|_\___|_|   \___|\___/ \__/ \___|_|
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author DualNova-Team
 * 
 *
*/

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\math\AxisAlignedBB;
use pocketmine\Player;

class Slab2 extends Transparent{
	
	const RED_SANDSTONE = 0;
	const PURPUR = 1;

	protected $id = self::SLAB2;
	
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() {
		return 2;
	}

	public function getName() : string{
		static $names = [
			0 => "Red Sandstone",
			1 => "Purpur",
		];
		return (($this->meta & 0x08) > 0 ? "Upper " : "") . $names[$this->meta & 0x07] . " Slab";
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$this->meta &= 0x07;
		if($face === 0){
			if($target->getId() === self::SLAB2 and ($target->getDamage() & 0x08) === 0x08 and ($target->getDamage() & 0x07) === ($this->meta & 0x07)){
				$this->getLevel()->setBlock($target, Block::get(Item::DOUBLE_SLAB2, $this->meta), true);

				return true;
			}elseif($block->getId() === self::SLAB2 and ($block->getDamage() & 0x07) === ($this->meta & 0x07)){
				$this->getLevel()->setBlock($block, Block::get(Item::DOUBLE_SLAB2, $this->meta), true);

				return true;
			}else{
				$this->meta |= 0x08;
			}
		}elseif($face === 1){
			if($target->getId() === self::SLAB2 and ($target->getDamage() & 0x08) === 0 and ($target->getDamage() & 0x07) === ($this->meta & 0x07)){
				$this->getLevel()->setBlock($target, Block::get(Item::DOUBLE_SLAB2, $this->meta), true);

				return true;
			}elseif($block->getId() === self::SLAB2 and ($block->getDamage() & 0x07) === ($this->meta & 0x07)){
				$this->getLevel()->setBlock($block, Block::get(Item::DOUBLE_SLAB2, $this->meta), true);

				return true;
			}
			//TODO: check for collision
		}else{
			if($block->getId() === self::SLAB2){
				if(($block->getDamage() & 0x07) === ($this->meta & 0x07)){
					$this->getLevel()->setBlock($block, Block::get(Item::DOUBLE_SLAB2, $this->meta), true);

					return true;
				}

				return false;
			}else{
				if($fy > 0.5){
					$this->meta |= 0x08;
				}
			}
		}

		if($block->getId() === self::SLAB2 and ($target->getDamage() & 0x07) !== ($this->meta & 0x07)){
			return false;
		}
		$this->getLevel()->setBlock($block, $this, true, true);

		return true;
	}
}