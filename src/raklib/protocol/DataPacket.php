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

abstract class DataPacket extends Packet{

	/** @var EncapsulatedPacket[] */
	public $packets = [];

	public $seqNumber;

	public function encode(){
		parent::encode();
		($this->buffer .= (\substr(\pack("V", $this->seqNumber), 0, -1)));
		foreach($this->packets as $packet){
			($this->buffer .= $packet instanceof EncapsulatedPacket ? $packet->toBinary() : (string) $packet);
		}
	}

	public function length(){
		$length = 4;
		foreach($this->packets as $packet){
			$length += $packet instanceof EncapsulatedPacket ? $packet->getTotalLength() : \strlen($packet);
		}

		return $length;
	}

	public function decode(){
		parent::decode();
		$this->seqNumber = (\unpack("V", $this->get(3) . "\x00")[1]);

		while(!$this->feof()){
			$offset = 0;
			$data = \substr($this->buffer, $this->offset);
			$packet = EncapsulatedPacket::fromBinary($data, \false, $offset);
			$this->offset += $offset;
			if($packet->buffer === ''){
				break;
			}
			$this->packets[] = $packet;
		}
	}

	public function clean(){
		$this->packets = [];
		$this->seqNumber = \null;

		return parent::clean();
	}
}
