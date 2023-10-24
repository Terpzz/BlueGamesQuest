<?php

namespace Terpz710\BlueGamesQuest\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Terpz710\BlueGamesQuest\Form\QuestForm;
use Terpz710\BlueGamesQuest\Main;

class QuestCommand extends Command implements CommandExecutor {

    private $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("quest", "See the list of quest!", "/quest");
        $this->setPermission("quest.cmd");
        $this->setExecutor($this);
        $this->plugin = $plugin;
    }

    public function onCommand(CommandSender $sender, string $commandLabel, array $args): bool {
        if ($sender instanceof Player) {
            QuestForm::sendQuestList($sender, $this->plugin);
        } else {
            $sender->sendMessage("This command can only be used in-game.");
        }
        return true;
    }
}
