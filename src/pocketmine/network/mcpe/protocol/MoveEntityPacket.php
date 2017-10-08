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


use pocketmine\math\Vector3;
use pocketmine\network\mcpe\NetworkSession;

class MoveEntityPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::MOVE_ENTITY_PACKET;

	/** @var int */
	public $entityRuntimeId;
	/** @var Vector3 */
	public $position;
	/** @var float */
	public $yaw;
	/** @var float */
	public $headYaw;
	/** @var float */
	public $pitch;
	/** @var bool */
	public $onGround = \false;
	/** @var bool */
	public $teleported = \false;

	protected function decodePayload(){
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->position = $this->getVector3Obj();
		$this->pitch = $this->getByteRotation();
		$this->headYaw = $this->getByteRotation();
		$this->yaw = $this->getByteRotation();
		$this->onGround = (($this->get(1) !== "\x00"));
		$this->teleported = (($this->get(1) !== "\x00"));
	}

	protected function encodePayload(){
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putVector3Obj($this->position);
		$this->putByteRotation($this->pitch);
		$this->putByteRotation($this->headYaw);
		$this->putByteRotation($this->yaw);
		($this->buffer .= ($this->onGround ? "\x01" : "\x00"));
		($this->buffer .= ($this->teleported ? "\x01" : "\x00"));
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleMoveEntity($this);
	}

}
