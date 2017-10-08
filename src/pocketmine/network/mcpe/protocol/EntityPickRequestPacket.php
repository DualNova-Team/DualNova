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

namespace pocketmine\network\mcpe\protocol;

use pocketmine\utils\Binary;

use pocketmine\network\mcpe\NetworkSession;

class EntityPickRequestPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::ENTITY_PICK_REQUEST_PACKET;

	/** @var int */
	public $entityTypeId;
	/** @var int */
	public $hotbarSlot;

	protected function decodePayload(){
		$this->entityTypeId = (Binary::readLLong($this->get(8)));
		$this->hotbarSlot = (\ord($this->get(1)));
	}

	protected function encodePayload(){
		($this->buffer .= (\pack("VV", $this->entityTypeId & 0xFFFFFFFF, $this->entityTypeId >> 32)));
		($this->buffer .= \chr($this->hotbarSlot));
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleEntityPickRequest($this);
	}
}
