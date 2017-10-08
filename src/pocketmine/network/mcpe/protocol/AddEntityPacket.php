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

use pocketmine\entity\Attribute;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\NetworkSession;

class AddEntityPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::ADD_ENTITY_PACKET;

	/** @var int|null */
	public $entityUniqueId = \null; //TODO
	/** @var int */
	public $entityRuntimeId;
	/** @var int */
	public $type;
	/** @var Vector3 */
	public $position;
	/** @var Vector3|null */
	public $motion;
	/** @var float */
	public $yaw = 0.0;
	/** @var float */
	public $pitch = 0.0;

	/** @var Attribute[] */
	public $attributes = [];
	/** @var array */
	public $metadata = [];
	/** @var array */
	public $links = [];

	protected function decodePayload(){
		$this->entityUniqueId = $this->getEntityUniqueId();
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->type = $this->getUnsignedVarInt();
		$this->position = $this->getVector3Obj();
		$this->motion = $this->getVector3Obj();
		$this->pitch = ((\unpack("g", $this->get(4))[1]));
		$this->yaw = ((\unpack("g", $this->get(4))[1]));

		$attrCount = $this->getUnsignedVarInt();
		for($i = 0; $i < $attrCount; ++$i){
			$name = $this->getString();
			$min = ((\unpack("g", $this->get(4))[1]));
			$current = ((\unpack("g", $this->get(4))[1]));
			$max = ((\unpack("g", $this->get(4))[1]));
			$attr = Attribute::getAttributeByName($name);

			if($attr !== \null){
				$attr->setMinValue($min);
				$attr->setMaxValue($max);
				$attr->setValue($current);
				$this->attributes[] = $attr;
			}else{
				throw new \UnexpectedValueException("Unknown attribute type \"$name\"");
			}
		}

		$this->metadata = $this->getEntityMetadata();
		$linkCount = $this->getUnsignedVarInt();
		for($i = 0; $i < $linkCount; ++$i){
			$this->links[] = $this->getEntityLink();
		}
	}

	protected function encodePayload(){
		$this->putEntityUniqueId($this->entityUniqueId ?? $this->entityRuntimeId);
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putUnsignedVarInt($this->type);
		$this->putVector3Obj($this->position);
		$this->putVector3ObjNullable($this->motion);
		($this->buffer .= (\pack("g", $this->pitch)));
		($this->buffer .= (\pack("g", $this->yaw)));

		$this->putUnsignedVarInt(\count($this->attributes));
		foreach($this->attributes as $attribute){
			$this->putString($attribute->getName());
			($this->buffer .= (\pack("g", $attribute->getMinValue())));
			($this->buffer .= (\pack("g", $attribute->getValue())));
			($this->buffer .= (\pack("g", $attribute->getMaxValue())));
		}

		$this->putEntityMetadata($this->metadata);
		$this->putUnsignedVarInt(\count($this->links));
		foreach($this->links as $link){
			$this->putEntityLink($link);
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleAddEntity($this);
	}

}
