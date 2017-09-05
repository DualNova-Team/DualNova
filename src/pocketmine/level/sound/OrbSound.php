<?

/*
 *
 *  ____               _   ___     _
 * |  _ \             | | |   \   | |
 * | | | |_   _  ____ | | | |\ \  | | _____    ______
 * | | | | | | |/ _  \| | | | \ \ | |/ _ \ \  / / _  \
 * | |_| | |_| | (_)  | |_| |  \ \| | (_) \ \/ / (_)  |
 * |____/\_____|\___|_\___|_|   \___|\___/ \__/ \___|_|
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author DualNova-Team
 * 
 *
*/

namespace pocketmine\level\sound;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;

class OrbSound extends GenericSound{
	public function __construct(Vector3 $pos, $pitch = 0){
		parent::__construct($pos, LevelEventPacket::EVENT_SOUND_ORB, $pitch);
	}
}
