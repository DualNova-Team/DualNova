<?php

/*
 *
 *  ____               _   ___    _
 * |  _ \ _   _  ____ | | |   \  | | _____    ______
 * | | | | | | |/ _  \| | | |\ \ | |/ _ \ \  / / _  \
 * | |_| | |_| | (_)  | |_| | \ \| | (_) \ \/ / (_)  |
 * |____/\_____|\___|_\___|_|  \___|\___/ \__/ \___|_|
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

declare(strict_types=1);

namespace pocketmine\item;

class WrittenBook extends Item{
	public function __construct(int $meta = 0){
		parent::__construct(self::WRITTEN_BOOK, $meta, "Written Book");
	}

}

