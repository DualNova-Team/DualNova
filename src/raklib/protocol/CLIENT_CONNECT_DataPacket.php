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

class CLIENT_CONNECT_DataPacket extends Packet{
	public static $ID = 0x09;

	public $clientID;
	public $sendPing;
	public $useSecurity = \false;

	public function encode(){
		parent::encode();
		($this->buffer .= (\pack("NN", $this->clientID >> 32, $this->clientID & 0xFFFFFFFF)));
		($this->buffer .= (\pack("NN", $this->sendPing >> 32, $this->sendPing & 0xFFFFFFFF)));
		($this->buffer .= \chr($this->useSecurity ? 1 : 0));
	}

	public function decode(){
		parent::decode();
		$this->clientID = (Binary::readLong($this->get(8)));
		$this->sendPing = (Binary::readLong($this->get(8)));
		$this->useSecurity = (\ord($this->get(1))) > 0;
	}
}
