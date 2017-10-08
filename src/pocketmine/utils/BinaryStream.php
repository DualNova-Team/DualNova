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

namespace pocketmine\utils;

use pocketmine\utils\Binary;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

class BinaryStream{

	/** @var int */
	public $offset;
	/** @var string */
	public $buffer;

	public function __construct(string $buffer = "", int $offset = 0){
		$this->buffer = $buffer;
		$this->offset = $offset;
	}

	public function reset(){
		$this->buffer = "";
		$this->offset = 0;
	}

	public function setBuffer(string $buffer = "", int $offset = 0){
		$this->buffer = $buffer;
		$this->offset = $offset;
	}

	public function getOffset() : int{
		return $this->offset;
	}

	public function getBuffer() : string{
		return $this->buffer;
	}

	/**
	 * @param int|bool $len
	 *
	 * @return string
	 */
	public function get($len) : string{
		if($len === \true){
			$str = \substr($this->buffer, $this->offset);
			$this->offset = \strlen($this->buffer);
			return $str;
		}elseif($len < 0){
			$this->offset = \strlen($this->buffer) - 1;
			return "";
		}elseif($len === 0){
			return "";
		}

		return $len === 1 ? $this->buffer{$this->offset++} : \substr($this->buffer, ($this->offset += $len) - $len, $len);
	}

	public function getRemaining() : string{
		$str = \substr($this->buffer, $this->offset);
		$this->offset = \strlen($this->buffer);
		return $str;
	}

	public function put(string $str){
		$this->buffer .= $str;
	}


	public function getBool() : bool{
		return $this->get(1) !== "\x00";
	}

	public function putBool(bool $v){
		$this->buffer .= ($v ? "\x01" : "\x00");
	}


	public function getByte() : int{
		return \ord($this->buffer{$this->offset++});
	}

	public function putByte(int $v){
		$this->buffer .= \chr($v);
	}


	public function getShort() : int{
		return (\unpack("n", $this->get(2))[1]);
	}

	public function getSignedShort() : int{
		return (\unpack("n", $this->get(2))[1] << 48 >> 48);
	}

	public function putShort(int $v){
		$this->buffer .= (\pack("n", $v));
	}

	public function getLShort() : int{
		return (\unpack("v", $this->get(2))[1]);
	}

	public function getSignedLShort() : int{
		return (\unpack("v", $this->get(2))[1] << 48 >> 48);
	}

	public function putLShort(int $v){
		$this->buffer .= (\pack("v", $v));
	}


	public function getTriad() : int{
		return \unpack("N", "\x00" . $this->get(3))[1];
	}

	public function putTriad(int $v){
		$this->buffer .= (\substr(\pack("N", $v), 1));
	}

	public function getLTriad() : int{
		return \unpack("V", $this->get(3) . "\x00")[1];
	}

	public function putLTriad(int $v){
		$this->buffer .= (\substr(\pack("V", $v), 0, -1));
	}


	public function getInt() : int{
		return (\unpack("N", $this->get(4))[1] << 32 >> 32);
	}

	public function putInt(int $v){
		$this->buffer .= (\pack("N", $v));
	}

	public function getLInt() : int{
		return (\unpack("V", $this->get(4))[1] << 32 >> 32);
	}

	public function putLInt(int $v){
		$this->buffer .= (\pack("V", $v));
	}


	public function getFloat() : float{
		return (\unpack("G", $this->get(4))[1]);
	}

	public function getRoundedFloat(int $accuracy) : float{
		return (\round((\unpack("G", $this->get(4))[1]),  $accuracy));
	}

	public function putFloat(float $v){
		$this->buffer .= (\pack("G", $v));
	}

	public function getLFloat() : float{
		return (\unpack("g", $this->get(4))[1]);
	}

	public function getRoundedLFloat(int $accuracy) : float{
		return (\round((\unpack("g", $this->get(4))[1]),  $accuracy));
	}

	public function putLFloat(float $v){
		$this->buffer .= (\pack("g", $v));
	}


	/**
	 * @return int
	 */
	public function getLong() : int{
		return Binary::readLong($this->get(8));
	}

	/**
	 * @param int $v
	 */
	public function putLong(int $v){
		$this->buffer .= (\pack("NN", $v >> 32, $v & 0xFFFFFFFF));
	}

	/**
	 * @return int
	 */
	public function getLLong() : int{
		return Binary::readLLong($this->get(8));
	}

	/**
	 * @param int $v
	 */
	public function putLLong(int $v){
		$this->buffer .= (\pack("VV", $v & 0xFFFFFFFF, $v >> 32));
	}


	public function getString() : string{
		return $this->get($this->getUnsignedVarInt());
	}

	public function putString(string $v){
		$this->putUnsignedVarInt(\strlen($v));
		($this->buffer .= $v);
	}


	public function getUUID() : UUID{
		//This is actually two little-endian longs: UUID Most followed by UUID Least
		$part1 = ((\unpack("V", $this->get(4))[1] << 32 >> 32));
		$part0 = ((\unpack("V", $this->get(4))[1] << 32 >> 32));
		$part3 = ((\unpack("V", $this->get(4))[1] << 32 >> 32));
		$part2 = ((\unpack("V", $this->get(4))[1] << 32 >> 32));
		return new UUID($part0, $part1, $part2, $part3);
	}

	public function putUUID(UUID $uuid){
		($this->buffer .= (\pack("V", $uuid->getPart(1))));
		($this->buffer .= (\pack("V", $uuid->getPart(0))));
		($this->buffer .= (\pack("V", $uuid->getPart(3))));
		($this->buffer .= (\pack("V", $uuid->getPart(2))));
	}

	public function getSlot() : Item{
		$id = $this->getVarInt();
		if($id <= 0){
			return ItemFactory::get(0, 0, 0);
		}

		$auxValue = $this->getVarInt();
		$data = $auxValue >> 8;
		if($data === 0x7fff){
			$data = -1;
		}
		$cnt = $auxValue & 0xff;

		$nbtLen = ((\unpack("v", $this->get(2))[1]));
		$nbt = "";

		if($nbtLen > 0){
			$nbt = $this->get($nbtLen);
		}

		//TODO
		$canPlaceOn = $this->getVarInt();
		if($canPlaceOn > 0){
			for($i = 0; $i < $canPlaceOn; ++$i){
				$this->getString();
			}
		}

		//TODO
		$canDestroy = $this->getVarInt();
		if($canDestroy > 0){
			for($i = 0; $i < $canDestroy; ++$i){
				$this->getString();
			}
		}

		return ItemFactory::get($id, $data, $cnt, $nbt);
	}


	public function putSlot(Item $item){
		if($item->getId() === 0){
			$this->putVarInt(0);
			return;
		}

		$this->putVarInt($item->getId());
		$auxValue = (($item->getDamage() & 0x7fff) << 8) | $item->getCount();
		$this->putVarInt($auxValue);

		$nbt = $item->getCompoundTag();
		($this->buffer .= (\pack("v", \strlen($nbt))));
		($this->buffer .= $nbt);

		$this->putVarInt(0); //CanPlaceOn entry count (TODO)
		$this->putVarInt(0); //CanDestroy entry count (TODO)
	}

	/**
	 * Reads a 32-bit variable-length unsigned integer from the buffer and returns it.
	 * @return int
	 */
	public function getUnsignedVarInt() : int{
		return Binary::readUnsignedVarInt($this->buffer, $this->offset);
	}

	/**
	 * Writes a 32-bit variable-length unsigned integer to the end of the buffer.
	 * @param int $v
	 */
	public function putUnsignedVarInt(int $v){
		($this->buffer .= Binary::writeUnsignedVarInt($v));
	}

	/**
	 * Reads a 32-bit zigzag-encoded variable-length integer from the buffer and returns it.
	 * @return int
	 */
	public function getVarInt() : int{
		return Binary::readVarInt($this->buffer, $this->offset);
	}

	/**
	 * Writes a 32-bit zigzag-encoded variable-length integer to the end of the buffer.
	 * @param int $v
	 */
	public function putVarInt(int $v){
		($this->buffer .= Binary::writeVarInt($v));
	}

	/**
	 * Reads a 64-bit variable-length integer from the buffer and returns it.
	 * @return int
	 */
	public function getUnsignedVarLong() : int{
		return Binary::readUnsignedVarLong($this->buffer, $this->offset);
	}

	/**
	 * Writes a 64-bit variable-length integer to the end of the buffer.
	 * @param int $v
	 */
	public function putUnsignedVarLong(int $v){
		$this->buffer .= Binary::writeUnsignedVarLong($v);
	}

	/**
	 * Reads a 64-bit zigzag-encoded variable-length integer from the buffer and returns it.
	 * @return int
	 */
	public function getVarLong() : int{
		return Binary::readVarLong($this->buffer, $this->offset);
	}

	/**
	 * Writes a 64-bit zigzag-encoded variable-length integer to the end of the buffer.
	 * @param int
	 */
	public function putVarLong(int $v){
		$this->buffer .= Binary::writeVarLong($v);
	}

	/**
	 * Returns whether the offset has reached the end of the buffer.
	 * @return bool
	 */
	public function feof() : bool{
		return !isset($this->buffer{$this->offset});
	}
}
