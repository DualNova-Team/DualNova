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

class SERVER_HANDSHAKE_DataPacket extends Packet{
	public static $ID = 0x10;

	public $address;
	public $port;
	public $systemAddresses = [
		["127.0.0.1", 0, 4],
		["0.0.0.0", 0, 4],
		["0.0.0.0", 0, 4],
		["0.0.0.0", 0, 4],
		["0.0.0.0", 0, 4],
		["0.0.0.0", 0, 4],
		["0.0.0.0", 0, 4],
		["0.0.0.0", 0, 4],
		["0.0.0.0", 0, 4],
		["0.0.0.0", 0, 4]
	];

	public $sendPing;
	public $sendPong;

	public function encode(){
		parent::encode();
		$this->putAddress($this->address, $this->port, 4);
		($this->buffer .= (\pack("n", 0)));
		for($i = 0; $i < 10; ++$i){
			$this->putAddress($this->systemAddresses[$i][0], $this->systemAddresses[$i][1], $this->systemAddresses[$i][2]);
		}

		($this->buffer .= (\pack("NN", $this->sendPing >> 32, $this->sendPing & 0xFFFFFFFF)));
		($this->buffer .= (\pack("NN", $this->sendPong >> 32, $this->sendPong & 0xFFFFFFFF)));
	}

	public function decode(){
		parent::decode();
		//TODO, not needed yet
	}
}
