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

class MovePlayerPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::MOVE_PLAYER_PACKET;

	const MODE_NORMAL = 0;
	const MODE_RESET = 1;
	const MODE_TELEPORT = 2;
	const MODE_PITCH = 3; //facepalm Mojang

	/** @var int */
	public $entityRuntimeId;
	/** @var Vector3 */
	public $position;
	/** @var float */
	public $yaw;
	/** @var float */
	public $bodyYaw;
	/** @var float */
	public $pitch;
	/** @var int */
	public $mode = self::MODE_NORMAL;
	/** @var bool */
	public $onGround = \false; //TODO
	/** @var int */
	public $ridingEid = 0;
	/** @var int */
	public $int1 = 0;
	/** @var int */
	public $int2 = 0;

	protected function decodePayload(){
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->position = $this->getVector3Obj();
		$this->pitch = ((\unpack("g", $this->get(4))[1]));
		$this->yaw = ((\unpack("g", $this->get(4))[1]));
		$this->bodyYaw = ((\unpack("g", $this->get(4))[1]));
		$this->mode = (\ord($this->get(1)));
		$this->onGround = (($this->get(1) !== "\x00"));
		$this->ridingEid = $this->getEntityRuntimeId();
		if($this->mode === MovePlayerPacket::MODE_TELEPORT){
			$this->int1 = ((\unpack("V", $this->get(4))[1] << 32 >> 32));
			$this->int2 = ((\unpack("V", $this->get(4))[1] << 32 >> 32));
		}
	}

	protected function encodePayload(){
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putVector3Obj($this->position);
		($this->buffer .= (\pack("g", $this->pitch)));
		($this->buffer .= (\pack("g", $this->yaw)));
		($this->buffer .= (\pack("g", $this->bodyYaw))); //TODO
		($this->buffer .= \chr($this->mode));
		($this->buffer .= ($this->onGround ? "\x01" : "\x00"));
		$this->putEntityRuntimeId($this->ridingEid);
		if($this->mode === MovePlayerPacket::MODE_TELEPORT){
			($this->buffer .= (\pack("V", $this->int1)));
			($this->buffer .= (\pack("V", $this->int2)));
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleMovePlayer($this);
	}

}
