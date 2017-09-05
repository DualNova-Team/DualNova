<?php

/*
 *
 *  ____               _   ___     _
 * |  _ \             | | |   \   | |
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

class Slab2 extends WoodenSlab{
	const RED_SANDSTONE = 0;
	const PURPUR = 1;

	protected $id = self::SLAB2;

	protected $doubleId = self::DOUBLE_SLAB2;
	
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

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= Tool::TIER_WOODEN){
			return [
				[$this->id, $this->meta & 0x07, 1],
			];
		}else{
			return [];
		}
	}
}