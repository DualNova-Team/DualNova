<?php

namespace pocketmine\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\network\mcpe\protocol\ProtocolInfo as Info;

class MakeServerCommand extends VanillaCommand{

	public function __construct($name){
		parent::__construct(
			$name,
			"Creates a PocketMine Phar",
			"/makeserver (nogz)",
			["ms"]
		);
		$this->setPermission("pocketmine.command.makeserver");
	}

	private function preg_quote_array(array $strings, string $delim = null) : array{
		return array_map(function(string $str) use ($delim) : string{ return preg_quote($str, $delim); }, $strings);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return false;
		}

		$server = $sender->getServer();
		$pharPath = Server::getInstance()->getPluginPath() . DIRECTORY_SEPARATOR . "DevTools" . DIRECTORY_SEPARATOR . $server->getName() . "_" . $server->getPocketMineVersion() . ".phar";
		if(file_exists($pharPath)){
			$sender->sendMessage("[DevTools] Phar file already exists, overwriting...");
			@unlink($pharPath);
		}
		$start = microtime(true);
		$phar = new \Phar($pharPath);
		$phar->setMetadata([
			"name" => $server->getName(),
			"version" => $server->getPocketMineVersion(),
			"api" => $server->getApiVersion(),
			"minecraft" => $server->getVersion(),
			"protocol" => Info::CURRENT_PROTOCOL,
			"creationDate" => time()
		]);
		$phar->setStub('<?php define("pocketmine\\\\PATH", "phar://". __FILE__ ."/"); require_once("phar://". __FILE__ ."/src/pocketmine/PocketMine.php");  __HALT_COMPILER();');
		$phar->setSignatureAlgorithm(\Phar::SHA1);
		$phar->startBuffering();

		$excludedSubstrings = [
			"/.", //"Hidden" files, git information etc
			realpath($pharPath) //don't add the phar to itself
		];

		$filePath = realpath(\pocketmine\PATH) . "/";
		$filePath = rtrim(str_replace("\\", "/", $filePath), "/") . "/";

		$regex = sprintf('/^(?!.*(%s))^%s(%s).*/i',
			implode('|', $this->preg_quote_array($excludedSubstrings, '/')), //String may not contain any of these substrings
			preg_quote($filePath, '/'), //String must start with this path...
			implode('|', $this->preg_quote_array(["src","vendor"], '/')) //... and must be followed by one of these relative paths, if any were specified. If none, this will produce a null capturing group which will allow anything.
		);
		$count = count($phar->buildFromDirectory($filePath,$regex));
		$sender->sendMessage("[DevTools] Added " . $count . " files");
		$sender->sendMessage("[DevTools] Checking for compressible files...");
		foreach($phar as $file => $finfo){
			/** @var \PharFileInfo $finfo */
			if($finfo->getSize() > (1024 * 512)){
				$sender->sendMessage("[DevTools] Compressing " . $finfo->getFilename());
				$finfo->compress(\Phar::GZ);
			}
		}
		$phar->stopBuffering();

		$sender->sendMessage($server->getName() . " " . $server->getPocketMineVersion() . " Phar file has been created on " . $pharPath);
		$sender->sendMessage("[DevTools] Done in " . round(microtime(true) - $start, 3) . "s");

		return true;
	}
}
