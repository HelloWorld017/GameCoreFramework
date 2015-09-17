<?php

namespace gamecore\gcframework;

interface RespawnableGame {

	/** @param $playerName string name of player
	 *  @return boolean*/
	public function isPlayerPlaying($playerName);
}