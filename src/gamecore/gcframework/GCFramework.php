<?php

namespace gamecore\gcframework;

use ifteam\CustomPacket\CPAPI;
use ifteam\CustomPacket\DataPacket;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class GCFramework extends PluginBase implements Listener{

	const PACKET_TYPE_GAME_FINISH = 0;
	const PACKET_TYPE_GET_DESCRIPTION = 1;
	const PACKET_TYPE_GET_GAME_RANK = 2;
	const PACKET_TYPE_GET_WHOLE_RANK = 3;
	const PACKET_TYPE_POST_DESCRIPTION = 4;
	const PACKET_TYPE_POST_GAME_RANK = 5;
	const PACKET_TYPE_POST_WHOLE_RANK = 6;
	const PACKET_TYPE_POST_GAME_MESSAGE = 7;

	private static $framework = null;

	/** @var GCFramework  */
	private static $instance = null;

	/** @var RespawnableGame[]*/
	private static $respawnableGames = [];

	public function onEnable(){
		self::$instance = $this;
		$this->getLogger()->info(TextFormat::BLUE."GameCore gaming framework main loaded.");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		if(!self::isFrameworkAttatched()){
			$sender->sendMessage(TextFormat::RED."No framework attatched!");
			return true;
		}

		switch($command->getName()){
			case "rank":
				if(count($args) < 1) {
					self::getFramework()->broadcastWholeRankTo($sender, 1);
					return true;
				}

				if(is_numeric($args[0])){
					self::getFramework()->broadcastWholeRankTo($sender, $args[0]);
					return true;
				}

				return false;

			case "grank":

				if(count($args) < 1) return false;

				if(count($args) < 2) {
					self::getFramework()->broadcastRankTo($sender, $args[0], 1);
					return true;
				}

				if(!is_numeric($args[count($args) - 1])) return false;

				$size = $args[count($args) - 1];

				array_pop($args);
				self::getFramework()->broadcastRankTo($sender, implode(" ", $args), $size);

				break;

			case "desc":
				if(count($args) < 1) return false;
				$name = implode(" ", $args);

				self::getFramework()->broadcastDescriptionTo($sender, $name);
				break;
		}

		return true;
	}

	public function onPlayerRespawn(PlayerRespawnEvent $event){
		$isMoveRequired = true;
		foreach(self::$respawnableGames as $game){
			if($game->isPlayerPlaying($event->getPlayer()->getName())){
				$isMoveRequired = false;
				break;
			}
		}

		if($isMoveRequired){
			$event->setRespawnPosition(Server::getInstance()->getDefaultLevel()->getSpawnLocation());
		}
	}

	public static function attatchFramework(Framework $framework){
		self::$framework = $framework;
		self::$instance->getLogger()->info(TextFormat::BLUE."[GCFramework] Framework attatched!");
	}

	public static function attatchRespawnableGame(RespawnableGame $game, $gameName){
		self::$respawnableGames[strtolower($gameName)] = $game;
	}

	/** @return Framework */
	public static function getFramework(){
		return self::$framework;
	}

	public static function sendPacket($ip, $port, array $data){
		CPAPI::sendPacket(new DataPacket($ip, $port, json_encode($data)));
	}

	public static function isFrameworkAttatched(){
		return (self::getFramework() !== null);
	}
}
