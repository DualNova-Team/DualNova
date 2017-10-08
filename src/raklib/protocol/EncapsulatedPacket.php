<?php

/*
 * RakLib network library
 *
 *
 * This project is not affiliated with Jenkins Software LLC nor RakNet.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 */

namespace raklib\protocol;


use raklib\Binary;

class EncapsulatedPacket{

	public $reliability;
	public $hasSplit = \false;
	public $length = 0;
	public $messageIndex = \null;
	public $orderIndex = \null;
	public $orderChannel = \null;
	public $splitCount = \null;
	public $splitID = \null;
	public $splitIndex = \null;
	public $buffer;
	public $needACK = \false;
	public $identifierACK = \null;

	/**
	 * @param string $binary
	 * @param bool   $internal
	 * @param int    &$offset
	 *
	 * @return EncapsulatedPacket
	 */
	public static function fromBinary($binary, $internal = \false, &$offset = \null){

		$packet = new EncapsulatedPacket();

		$flags = \ord($binary{0});
		$packet->reliability = $reliability = ($flags & 0b11100000) >> 5;
		$packet->hasSplit = $hasSplit = ($flags & 0b00010000) > 0;
		if($internal){
			$length = (\unpack("N", \substr($binary, 1, 4))[1] << 32 >> 32);
			$packet->identifierACK = (\unpack("N", \substr($binary, 5, 4))[1] << 32 >> 32);
			$offset = 9;
		}else{
			$length = (int) \ceil((\unpack("n", \substr($binary, 1, 2))[1]) / 8);
			$offset = 3;
			$packet->identifierACK = \null;
		}

		if($reliability > PacketReliability::UNRELIABLE){
			if($reliability >= PacketReliability::RELIABLE and $reliability !== PacketReliability::UNRELIABLE_WITH_ACK_RECEIPT){
				$packet->messageIndex = \unpack("V", \substr($binary, $offset, 3) . "\x00")[1];
				$offset += 3;
			}

			if($reliability <= PacketReliability::RELIABLE_SEQUENCED and $reliability !== PacketReliability::RELIABLE){
				$packet->orderIndex = \unpack("V", \substr($binary, $offset, 3) . "\x00")[1];
				$offset += 3;
				$packet->orderChannel = \ord($binary{$offset++});
			}
		}

		if($hasSplit){
			$packet->splitCount = (\unpack("N", \substr($binary, $offset, 4))[1] << 32 >> 32);
			$offset += 4;
			$packet->splitID = (\unpack("n", \substr($binary, $offset, 2))[1]);
			$offset += 2;
			$packet->splitIndex = (\unpack("N", \substr($binary, $offset, 4))[1] << 32 >> 32);
			$offset += 4;
		}

		$packet->buffer = \substr($binary, $offset, $length);
		$offset += $length;

		return $packet;
	}

	public function getTotalLength(){
		return 3 + \strlen($this->buffer) + ($this->messageIndex !== \null ? 3 : 0) + ($this->orderIndex !== \null ? 4 : 0) + ($this->hasSplit ? 10 : 0);
	}

	/**
	 * @param bool $internal
	 *
	 * @return string
	 */
	public function toBinary($internal = \false){
		return
			\chr(($this->reliability << 5) | ($this->hasSplit ? 0b00010000 : 0)) .
			($internal ? (\pack("N", \strlen($this->buffer))) . (\pack("N", $this->identifierACK)) : (\pack("n", \strlen($this->buffer) << 3))) .
			($this->reliability > PacketReliability::UNRELIABLE ?
				(($this->reliability >= PacketReliability::RELIABLE and $this->reliability !== PacketReliability::UNRELIABLE_WITH_ACK_RECEIPT) ? (\substr(\pack("V", $this->messageIndex), 0, -1)) : "") .
				(($this->reliability <= PacketReliability::RELIABLE_SEQUENCED and $this->reliability !== PacketReliability::RELIABLE) ? (\substr(\pack("V", $this->orderIndex), 0, -1)) . \chr($this->orderChannel) : "")
				: ""
			) .
			($this->hasSplit ? (\pack("N", $this->splitCount)) . (\pack("n", $this->splitID)) . (\pack("N", $this->splitIndex)) : "")
			. $this->buffer;
	}

	public function __toString(){
		return $this->toBinary();
	}
}
