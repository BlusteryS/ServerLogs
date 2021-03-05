<?php

declare(strict_types=1);

namespace System\Main;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {

	public function onEnable(): void {
		if ($this->getServer()->getPluginManager()->getPlugin("FormAPI") === null || $this->getServer()->getPluginManager()->getPlugin("FormAPI")->isDisabled()) {
			$this->getLogger()->info("§c§lFormAPI is not installed!");
		}
		$this->writeServer("The server has been shutdown.");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onDisable(): void {
		$this->writeServer("The server has been enabled.");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onCommand(CommandSender $s, Command $cmd, string $label, array $args): bool {
		if (!($s instanceof Player)) {
			$s->sendMessage("Only in the game!");
			return false;
		}
		if ($cmd->getName() !== "info") {
			return false;
		}
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		if ($api === null || $api->isDisabled()) {
			$s->sendMessage("§eFormAPI не установлен!");
			return false;
		}
		$form = $api->createSimpleForm(
			function (Player $s, ?int $data) {
				$s->sendMessage("§eThe menu was closed!");
			}
		);
		$text = "";
		if (isset($args[0])) {
			if (!file_exists($this->getDataFolder() . strtolower($args[0]) . ".txt")) {
				$s->sendMessage("§ePlayer not found!");
				return true;
			}
			$file = file($this->getDataFolder() . strtolower($args[0]) . ".txt");
			if (count($file) < 40) {
				for ($i = 0; $i < count($file); $i++) {
					$text .= $file[$i];
				}
			} else {
				for ($i = max(0, count($file) - 41); $i < count($file); $i++) {
					$text .= $file[$i];
				}
			}
			$form->setTitle("§0§lPLAYER INFO");
			$form->setContent("§ePlayer info: §c" . $args[0] . "§e:\n\n§f" . $text);
			$form->addButton("§c§lEXIT");
			$form->sendToPlayer($s);
			return true;
		}
		$file = file($this->getDataFolder() . "server.txt");
		if (count($file) < 40) {
			for ($i = 0; $i < count($file); $i++) {
				$text .= $file[$i];
			}
		} else {
			for ($i = max(0, count($file) - 41); $i < count($file); $i++) {
				$text .= $file[$i];
			}
		}
		$form->setTitle("§0§lSERVER INFO");
		$form->setContent("§eServer info:\n\n§f" . $text);
		$form->addButton("§c§lEXIT");
		$form->sendToPlayer($s);
		return true;
	}

	public function onCommandPreprocess(PlayerCommandPreprocessEvent $event): void {
		if (strpos($event->getMessage(), '/') === 0) {
			$this->writePlayer("Executed the command: " . $event->getMessage(), $event->getPlayer()->getName());
		} else {
			$this->writePlayer("Wrote the message: " . $event->getMessage(), $event->getPlayer()->getName());
		}
	}

	public function onJoin(PlayerJoinEvent $event): void {
		$this->writeServer("Player " . $event->getPlayer()->getName() . " has joined the server.");
		$this->writePlayer("Has joined the server.", $event->getPlayer()->getName());
	}

	public function onQuit(PlayerQuitEvent $event): void {
		$this->writeServer("Player  " . $event->getPlayer()->getName() . " has leaved the server.");
		$this->writePlayer("Has leaved the server.", $event->getPlayer()->getName());
	}

	public function onBreak(BlockBreakEvent $event): void {
		$this->writePlayer("Broke the block #" . $event->getBlock()->getId() . " (" . $event->getBlock()->x . ", " . $event->getBlock()->y . ", " . $event->getBlock()->z . ")", $event->getPlayer()->getName());
	}

	public function onPlace(BlockPlaceEvent $event): void {
		$this->writePlayer("Placed the block #" . $event->getBlock()->getId() . " (" . $event->getBlock()->x . ", " . $event->getBlock()->y . ", " . $event->getBlock()->z . ")", $event->getPlayer()->getName());
	}

	public function writePlayer(string $text, string $player): void {
		file_put_contents($this->getDataFolder() . strtolower($player) . ".txt", "[" . date("M d.Y h:i:s A") . "] " . $text . ".\n", FILE_APPEND);
	}

	public function writeServer(string $text): void {
		file_put_contents($this->getDataFolder() . "server.txt", "[" . date("M d.Y h:i:s A") . "] " . $text . ".\n", FILE_APPEND);
	}
}
