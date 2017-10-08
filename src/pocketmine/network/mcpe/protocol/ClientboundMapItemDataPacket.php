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
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\utils\Color;

class ClientboundMapItemDataPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::CLIENTBOUND_MAP_ITEM_DATA_PACKET;

	const BITFLAG_TEXTURE_UPDATE = 0x02;
	const BITFLAG_DECORATION_UPDATE = 0x04;

	/** @var int */
	public $mapId;
	/** @var int */
	public $type;
	/** @var int */
	public $dimensionId = DimensionIds::OVERWORLD;

	/** @var int[] */
	public $eids = [];
	/** @var int */
	public $scale;

	/** @var int[] */
	public $decorationEntityUniqueIds = [];
	/** @var array */
	public $decorations = [];

	/** @var int */
	public $width;
	/** @var int */
	public $height;
	/** @var int */
	public $xOffset = 0;
	/** @var int */
	public $yOffset = 0;
	/** @var Color[][] */
	public $colors = [];

	protected function decodePayload(){
		$this->mapId = $this->getEntityUniqueId();
		$this->type = $this->getUnsignedVarInt();
		$this->dimensionId = (\ord($this->get(1)));

		if(($this->type & 0x08) !== 0){
			$count = $this->getUnsignedVarInt();
			for($i = 0; $i < $count; ++$i){
				$this->eids[] = $this->getEntityUniqueId();
			}
		}

		if(($this->type & (self::BITFLAG_DECORATION_UPDATE | self::BITFLAG_TEXTURE_UPDATE)) !== 0){ //Decoration bitflag or colour bitflag
			$this->scale = (\ord($this->get(1)));
		}

		if(($this->type & self::BITFLAG_DECORATION_UPDATE) !== 0){
			for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
				$this->decorationEntityUniqueIds[] = $this->getEntityUniqueId();
			}

			for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
				$this->decorations[$i]["rot"] = (\ord($this->get(1)));
				$this->decorations[$i]["img"] = (\ord($this->get(1)));
				$this->decorations[$i]["xOffset"] = (\ord($this->get(1)));
				$this->decorations[$i]["yOffset"] = (\ord($this->get(1)));
				$this->decorations[$i]["label"] = $this->getString();

				$this->decorations[$i]["color"] = Color::fromABGR($this->getUnsignedVarInt());
			}
		}

		if(($this->type & self::BITFLAG_TEXTURE_UPDATE) !== 0){
			$this->width = $this->getVarInt();
			$this->height = $this->getVarInt();
			$this->xOffset = $this->getVarInt();
			$this->yOffset = $this->getVarInt();

			$count = $this->getUnsignedVarInt();
			\assert($count === $this->width * $this->height);

			for($y = 0; $y < $this->height; ++$y){
				for($x = 0; $x < $this->width; ++$x){
					$this->colors[$y][$x] = Color::fromABGR($this->getUnsignedVarInt());
				}
			}
		}
	}

	protected function encodePayload(){
		$this->putEntityUniqueId($this->mapId);

		$type = 0;
		if(($eidsCount = \count($this->eids)) > 0){
			$type |= 0x08;
		}
		if(($decorationCount = \count($this->decorations)) > 0){
			$type |= self::BITFLAG_DECORATION_UPDATE;
		}
		if(\count($this->colors) > 0){
			$type |= self::BITFLAG_TEXTURE_UPDATE;
		}

		$this->putUnsignedVarInt($type);
		($this->buffer .= \chr($this->dimensionId));

		if(($type & 0x08) !== 0){ //TODO: find out what these are for
			$this->putUnsignedVarInt($eidsCount);
			foreach($this->eids as $eid){
				$this->putEntityUniqueId($eid);
			}
		}

		if(($type & (self::BITFLAG_TEXTURE_UPDATE | self::BITFLAG_DECORATION_UPDATE)) !== 0){
			($this->buffer .= \chr($this->scale));
		}

		if(($type & self::BITFLAG_DECORATION_UPDATE) !== 0){
			$this->putUnsignedVarInt(\count($this->decorationEntityUniqueIds));
			foreach($this->decorationEntityUniqueIds as $id){
				$this->putEntityUniqueId($id);
			}

			$this->putUnsignedVarInt($decorationCount);
			foreach($this->decorations as $decoration){
				($this->buffer .= \chr($decoration["rot"]));
				($this->buffer .= \chr($decoration["img"]));
				($this->buffer .= \chr($decoration["xOffset"]));
				($this->buffer .= \chr($decoration["yOffset"]));
				$this->putString($decoration["label"]);

				\assert($decoration["color"] instanceof Color);
				$this->putUnsignedVarInt($decoration["color"]->toABGR());
			}
		}

		if(($type & self::BITFLAG_TEXTURE_UPDATE) !== 0){
			$this->putVarInt($this->width);
			$this->putVarInt($this->height);
			$this->putVarInt($this->xOffset);
			$this->putVarInt($this->yOffset);

			$this->putUnsignedVarInt($this->width * $this->height); //list count, but we handle it as a 2D array... thanks for the confusion mojang

			for($y = 0; $y < $this->height; ++$y){
				for($x = 0; $x < $this->width; ++$x){
					$this->putUnsignedVarInt($this->colors[$y][$x]->toABGR());
				}
			}
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleClientboundMapItemData($this);
	}
}
