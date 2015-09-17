<?php

namespace gamecore\gcframework;

use pocketmine\command\CommandSender;

interface Framework {
	public function onGameFinish($gameName, array $winners, $message = null);

	public function broadcastWholeRankTo(CommandSender $sender, $page);
	public function broadcastRankTo(CommandSender $sender, $gameName, $page);
	public function broadcastDescriptionTo(CommandSender $sender, $gameName);
}
