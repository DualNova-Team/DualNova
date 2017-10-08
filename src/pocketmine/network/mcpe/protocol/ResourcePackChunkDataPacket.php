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

class ResourcePackChunkDataPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::RESOURCE_PACK_CHUNK_DATA_PACKET;

	/** @var string */
	public $packId;
	/** @var int */
	public $chunkIndex;
	/** @var int */
	public $progress;
	/** @var string */
	public $data;

	protected function decodePayload(){
		$this->packId = $this->getString();
		$this->chunkIndex = ((\unpack("V", $this->get(4))[1] << 32 >> 32));
		$this->progress = (Binary::readLLong($this->get(8)));
		$this->data = $this->get(((\unpack("V", $this->get(4))[1] << 32 >> 32)));
	}

	protected function encodePayload(){
		$this->putString($this->packId);
		($this->buffer .= (\pack("V", $this->chunkIndex)));
		($this->buffer .= (\pack("VV", $this->progress & 0xFFFFFFFF, $this->progress >> 32)));
		($this->buffer .= (\pack("V", \strlen($this->data))));
		($this->buffer .= $this->data);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleResourcePackChunkData($this);
	}
}
