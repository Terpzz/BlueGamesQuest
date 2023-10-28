<?php

namespace Terpz710\BlueGamesQuest;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\item\StringToItemParser;
use pocketmine\item\Item;
use jojoe77777\FormAPI\CustomForm;

class EventListener implements Listener {

    private $questConfig;

    public function __construct(Config $questConfig) {
        $this->questConfig = $questConfig;
    }

    public function onPlayerJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $this->handleQuests($player);
    }

    public function handleQuests(Player $player) {
        $quests = $this->questConfig->get("quests", []);

        foreach ($quests as $questName => $questData) {
            $rewardString = $questData["reward"];
            $rewardItem = StringToItemParser::getInstance()->parse($rewardString);

            if ($this->hasCompletedQuest($player, $questName)) {
                continue;
            }

            if ($this->hasMetQuestRequirements($player, $questData)) {
                $player->sendMessage("You've completed the quest: $questName");
                $this->markQuestAsCompleted($player, $questName);
                // Reward the player
                $player->getInventory()->addItem($rewardItem);
            }
        }
    }

    private function hasMetQuestRequirements(Player $player, $questData) {
        $requiredItem = $questData["required_item"];

        if ($requiredItem !== null) {
            $requiredItem = StringToItemParser::getInstance()->parse($requiredItem);
            if ($requiredItem instanceof Item) {
                if ($player->getInventory()->contains($requiredItem)) {
                    return true;
                }
            }
            return false;
        }

        return true;
    }

    private function hasCompletedQuest(Player $player, $questName) {
        $completedQuests = $this->questConfig->get("completed_quests", []);
        return in_array($questName, $completedQuests);
    }

    private function markQuestAsCompleted(Player $player, $questName) {
        $completedQuests = $this->questConfig->get("completed_quests", []);
        $completedQuests[] = $questName;
        $this->questConfig->set("completed_quests", $completedQuests);
        $this->questConfig->save();
    }
}
