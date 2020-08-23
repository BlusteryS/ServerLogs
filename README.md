# ServerLogs
Записывает действия игроков на ваших серверах.

# Информация
* Версия: 1.11 и выше
* Ядро: [PMMP](https://github.com/pmmp/PocketMine-MP/)
* Версия плагина: 0.0.1

## API
`$plugin = $this->getServer()->getPluginManager()->getPlugin("SystemLogs");`
* `$plugin->writePlayer("Текст", "Ник"); // Записать действие игрока.`
* `$plugin->writeServer("Текст"); // Записать действие сервера.`
