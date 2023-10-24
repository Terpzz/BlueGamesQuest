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

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents(new QuestEventListener($this->questConfig), $this);
        $this->getServer()->getCommandMap()->register("quest", new QuestCommand($this));
        $this->saveResource("quest.yml");
        $this->questConfig = new Config($this->getDataFolder() . "quest.yml", Config::YAML);
    }

    public function getQuests() {
        return $this->questConfig->get("quests", []);
    }
}
