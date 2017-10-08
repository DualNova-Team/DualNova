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

class BookEditPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::BOOK_EDIT_PACKET;

	const TYPE_REPLACE_PAGE = 0;
	const TYPE_ADD_PAGE = 1;
	const TYPE_DELETE_PAGE = 2;
	const TYPE_SWAP_PAGES = 3;
	const TYPE_SIGN_BOOK = 4;

	/** @var int */
	public $type;
	/** @var int */
	public $inventorySlot;
	/** @var int */
	public $pageNumber;
	/** @var int */
	public $secondaryPageNumber;

	/** @var string */
	public $content1;
	/** @var string */
	public $content2;

	/** @var string */
	public $title;
	/** @var string */
	public $author;

	protected function decodePayload(){
		$this->type = (\ord($this->get(1)));
		$this->inventorySlot = (\ord($this->get(1)));

		switch($this->type){
			case self::TYPE_REPLACE_PAGE:
			case self::TYPE_ADD_PAGE:
				$this->pageNumber = (\ord($this->get(1)));
				$this->content1 = $this->getString();
				$this->content2 = $this->getString();
				break;
			case self::TYPE_DELETE_PAGE:
				$this->pageNumber = (\ord($this->get(1)));
				break;
			case self::TYPE_SWAP_PAGES:
				$this->pageNumber = (\ord($this->get(1)));
				$this->secondaryPageNumber = (\ord($this->get(1)));
				break;
			case self::TYPE_SIGN_BOOK:
				$this->title = $this->getString();
				$this->author = $this->getString();
				break;
			default:
				throw new \UnexpectedValueException("Unknown book edit type $this->type!");
		}
	}

	protected function encodePayload(){
		($this->buffer .= \chr($this->type));
		($this->buffer .= \chr($this->inventorySlot));

		switch($this->type){
			case self::TYPE_REPLACE_PAGE:
			case self::TYPE_ADD_PAGE:
				($this->buffer .= \chr($this->pageNumber));
				$this->putString($this->content1);
				$this->putString($this->content2);
				break;
			case self::TYPE_DELETE_PAGE:
				($this->buffer .= \chr($this->pageNumber));
				break;
			case self::TYPE_SWAP_PAGES:
				($this->buffer .= \chr($this->pageNumber));
				($this->buffer .= \chr($this->secondaryPageNumber));
				break;
			case self::TYPE_SIGN_BOOK:
				$this->putString($this->title);
				$this->putString($this->author);
				break;
			default:
				throw new \UnexpectedValueException("Unknown book edit type $this->type!");
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleBookEdit($this);
	}
}
