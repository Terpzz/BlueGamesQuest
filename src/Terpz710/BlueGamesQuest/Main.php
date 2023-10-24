<?php

namespace Terpz710\BlueGamesQuest;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use Terpz710\BlueGamesQuest\EventListener;
use Terpz710\BlueGamesQuest\Form\QuestForm;
use Terpz710\BlueGamesQuest\Command\QuestCommand;

class Main extends PluginBase implements Listener {

    private $questConfig;
    private $eventListener;

    public function onEnable(): void {
        $this->saveResource("quest.yml");
        $this->questConfig = new Config($this->getDataFolder() . "quest.yml", Config::YAML);

        $this->eventListener = new EventListener($this->questConfig); // Initialize the eventListener

        $this->getServer()->getPluginManager()->registerEvents($this->eventListener, $this);
        $this->getServer()->getCommandMap()->register("quest", new QuestCommand($this));
    }

    public function getQuests() {
        return $this->questConfig->get("quests", []);
    }
}
