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
use pocketmine\math\Vector3;

class EndGateway extends Transparent{

	protected $id = self::END_GATEWAY;
	/** @var  Vector3 */
	private $temporalVector = null;
	
	public function __construct($meta = 0){
		if($this->temporalVector === null){
			$this->temporalVector = new Vector3(0, 0, 0);
		}
		$this->meta = $meta;
	}

	public function getName() : string{
		return "End Gateway";
	}

	public function getHardness() {
		return -1;
	}

	public function getResistance(){
		return 0;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function canPassThrough(){
		return true;
	}

	public function hasEntityCollision(){
		return true;
	}
	
	public function getDrops(Item $item) : array {
		return [];
	}
}