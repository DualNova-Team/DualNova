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

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Lever extends Flowable{

	protected $id = self::LEVER;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getName() : string{
		return "Lever";
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $facePos, Player $player = \null) : bool{
		if($blockClicked->isTransparent() === false){
			$this->meta = $face;
			$this->getLevel()->setBlock($blockReplace, $this, true, false);
			return true;
		}
		return false;
	}

	public function getHardness() : float{
		return 0.5;
	}

	public function getVariantBitmask() : int{
		return 0;
	}	
}
