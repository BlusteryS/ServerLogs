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
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {

	public function onEnable() : void {
		if ($this->getServer()->getPluginManager()->getPlugin("FormAPI") === null || $this->getServer()->getPluginManager()->getPlugin("FormAPI")->isDisabled()) {
			$this->getLoggerr()->info("§c§lFormAPI не установлен!");
		}
		writeServer("Сервер был включен.");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onDisable() : void {
		writeServer("Сервер был выключен.");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onCommand(CommandSender $s, Command $cmd, string $label, array $args) : bool {
		if (!($s instanceof Player)) {
			$s->sendMessage("Только в игре!");
			return false;
		}
		if ($cmd->getName() == "info") {
			$text = "";
			if (isset($args[0])) {
				if (!empty($args[0])) {
					if (file_exists($this->getDataFolder() . strtolower($args[0]) . ".txt")) {
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
						$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
						if ($api === null || $api->isDisabled()) {
							$s->sendMessage("§eFormAPI не установлен!");
							return false;
						}
						$form = $api->createSimpleForm(
							function(Player $s, $data = 0) {
								$result = $data;
								if ($result === null) {
									return;
								}
								switch ($result) {
									case 0:
										$s->sendMessage("§eМеню успешно закрыто!");
										return;
								}
						});
						$form->setTitle("§0§lИНФОРМАЦИЯ О ИГРОКЕ");
						$form->setContent("§eИнформация о игроке §c" . $args[0] . "§e:\n\n§f" . $text);
						$form->addButton("Закрыть");
						$form->sendToPlayer($s);
					} else {
						$s->sendMessage("§eИгрок не найден!");
					}
				} else {
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
					$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
					if ($api === null || $api->isDisabled()) {
						$s->sendMessage("§eFormAPI не установлен!");
						return false;
					}
					$form = $api->createSimpleForm(
						function(Player $s, $data = 0) {
							$result = $data;
							if ($result === null) {
								return;
							}
							switch ($result) {
								case 0:
									$s->sendMessage("§eМеню успешно закрыто!");
									return;
							}
					});
					$form->setTitle("§0§lИНФОРМАЦИЯ О СЕРВЕРЕ");
					$form->setContent("§eЧтобы посмотреть информацию о игроке, добавьте в последний аргумент игрока.\n§eИнформация о §cсервере§e:\n\n§f" . $text);
					$form->addButton("Закрыть");
					$form->sendToPlayer($s);
				}
			} else {
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
				$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
				if ($api === null || $api->isDisabled()) {
					$s->sendMessage("§eFormAPI не установлен!");
					return false;
				}
				$form = $api->createSimpleForm(
					function(Player $s, $data = 0) {
						$result = $data;
						if ($result === null) {
							return;
						}
						switch ($result) {
							case 0:
								$s->sendMessage("§eМеню успешно закрыто!");
								return;
						}
				});
				$form->setTitle("§0§lИНФОРМАЦИЯ О СЕРВЕРЕ");
				$form->setContent("§eЧтобы посмотреть информацию о игроке, добавьте в последний аргумент игрока.\n§eИнформация о §cсервере§e:\n\n§f" . $text);
				$form->addButton("Закрыть");
				$form->sendToPlayer($s);
			}
		}
		return true;
	}

	public function onCommandPreprocess(PlayerCommandPreprocessEvent $event) {
		if (strpos($event->getMessage(), '/') === 0) {
			writePlayer("Выполнил команду: " . $event->getMessage() . ".", $event->getPlayer()->getName());
		} else {
			writePlayer("Написал сообщение: " . $event->getMessage() . ".", $event->getPlayer()->getName());
		}
	}

	public function onJoin(PlayerJoinEvent $event) {
		foreach ($this->getServer()->getOnlinePlayers() as $players) {
			$players->sendMessage("§7(§c§lВХОД§r§7) §fИгрок §c" . $event->getPlayer()->getName() . "§f зашёл на сервер!");
		}
		writeServer("Игрок " . $event->getPlayer()->getName() . " зашёл на сервер.");
		writePlayer("Зашёл на сервер.", $event->getPlayer()->getName());
	}

	public function onQuit(PlayerQuitEvent $event) {
		foreach ($this->getServer()->getOnlinePlayers() as $players) {
			$players->sendMessage("§7(§c§lВХОД§r§7) §fИгрок §c" . $event->getPlayer()->getName() . "§f вышел с сервера!");
		}
		writeServer("Игрок " . $event->getPlayer()->getName() . " вышел с сервера.");
		writePlayer("Вышел с сервера.", $event->getPlayer()->getName());
	}

	public function onBreak(BlockBreakEvent $event) {
		writePlayer("Сломал блок с ID " . $event->getBlock()->getId() . " на координатах: " . $event->getBlock()->getX() . ", " . $event->getBlock()->getY() . ", " . $event->getBlock()->getZ() . ".", $event->getPlayer()->getName());
	}

	public function onPlace(BlockPlaceEvent $event) {
		writePlayer("Поставил блок с ID " . $event->getBlock()->getId() . " на координатах: " . $event->getBlock()->getX() . ", " . $event->getBlock()->getY() . ", " . $event->getBlock()->getZ() . ".", $event->getPlayer()->getName());
	}

	public function writePlayer($text, $player) {
		file_put_contents($this->getDataFolder() . strtolower($player) . ".txt", "[" . date("M d.Y h:i:s A") . "] " . $text . ".\n", FILE_APPEND);
	}

	public function writeServer($text) {
		file_put_contents($this->getDataFolder() . "server.txt", "[" . date("M d.Y h:i:s A") . "] " . $text . ".\n", FILE_APPEND);
	}
}