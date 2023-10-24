<?php

namespace Terpz710\BlueGamesQuest\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Terpz710\BlueGamesQuest\Form\QuestForm;
use Terpz710\BlueGamesQuest\Main;

class QuestCommand extends Command {

    private $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("quest", "See the list of quests!", "/quest");
        $this->setPermission("quest.cmd");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if ($sender instanceof Player) {
            $eventListener = $this->plugin->eventListener;
            $questForm = new QuestForm($eventListener);
            $questForm->sendQuestList($sender, $this->plugin->getQuests());
        } else {
            $sender->sendMessage("This command can only be used in-game.");
        }
        return true;
    }
}

