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

use pocketmine\item\Item;
use pocketmine\item\Tool;

class EndStoneBricks extends Solid{

	protected $id = self::END_BRICKS;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
		return 1.5;
	}

	public function getName() : string{
		return "End Stone Bricks";
	}
	
	public function getToolType() : int{
		return Tool::TYPE_PICKAXE;
	}
}