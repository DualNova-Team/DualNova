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
use pocketmine\Player;

class PurpurBlock extends Solid{

	const PURPUR_NORMAL = 0;
	const PURPUR_NORMAL2 = 1;
	const PURPUR_PILLAR = 2;

	protected $id = self::PURPUR_BLOCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() {
		return 1.5;
	}

	public function getName() : string{
		static $names = [
			0 => "Purrpur Block",
			1 => "Unknown",
			2 => "Purpur Pillar",
		];
		return $names[$this->meta & 0x03];
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		if($this->meta === 2){

			$faces = [
				0 => 0,
				1 => 0,
				2 => 0b1000,
				3 => 0b1000,
				4 => 0b0100,
				5 => 0b0100,
			];
			$this->meta = ($this->meta & 0x03) | $faces[$face];
		}
		$this->getLevel()->setBlock($block, $this, true, true);
		return true;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getDrops(Item $item) : array {
		if($item->isPickaxe() >= 1){
			return [
				[Item::PURPUR_BLOCK, $this->meta & 0x03, 1],
			];
		}else{
			return [];
		}
	}	
}