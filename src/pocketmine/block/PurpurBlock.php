<?php

/*
 *
 *  ____               _   ___    _
 * |  _ \ _   _  ____ | | |   \  | | _____    ______
 * | | | | | | |/ _  \| | | |\ \ | |/ _ \ \  / / _  \
 * | |_| | |_| | (_)  | |_| | \ \| | (_) \ \/ / (_)  |
 * |____/\_____|\___|_\___|_|  \___|\___/ \__/ \___|_|
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

use pocketmine\block\utils\PillarRotationHelper;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\math\Vector3;
use pocketmine\Player;

class PurpurBlock extends Solid{
	
	const PURPUR_NORMAL = 0;
	const PURPUR_NORMAL2 = 1;
	const PURPUR_PILLAR = 2;

	protected $id = self::PURPUR_BLOCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
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

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $facePos, Player $player = \null) : bool{
		if($this->meta !== self::PURPUR_NORMAL){
			$this->meta = PillarRotationHelper::getMetaFromFace($this->meta, $face);
		}
		return $this->getLevel()->setBlock($blockReplace, $this, \true, \true);
	}

	public function getToolType() : int{
		return Tool::TYPE_PICKAXE;
	}
	
	public function getDrops(Item $item) : array{
		if($item->isPickaxe() >= Tool::TIER_WOODEN){
			return parent::getDrops($item);
		}

		return [];
	}
}