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

namespace pocketmine\nbt\tag;

use pocketmine\nbt\NBT;

use pocketmine\utils\Binary;

class LongTag extends NamedTag{

	/**
	 * LongTag constructor.
	 *
	 * @param string $name
	 * @param int    $value
	 */
	public function __construct(string $name = "", int $value = 0){
		parent::__construct($name, $value);
	}

	public function getType(){
		return NBT::TAG_Long;
	}

	public function read(NBT $nbt, bool $network = \false){
		$this->value = ($network === \true ? Binary::readVarLong($nbt->buffer, $nbt->offset) : ($nbt->endianness === 1 ? Binary::readLong($nbt->get(8)) : Binary::readLLong($nbt->get(8))));
	}

	public function write(NBT $nbt, bool $network = \false){
		($nbt->buffer .=  $network === \true ? Binary::writeVarLong($this->value) : ($nbt->endianness === 1 ? (\pack("NN", $this->value >> 32, $this->value & 0xFFFFFFFF)) : (\pack("VV", $this->value & 0xFFFFFFFF, $this->value >> 32))));
	}
}
