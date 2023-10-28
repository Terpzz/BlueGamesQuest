<?php

namespace Terpz710\BlueGamesQuest\Form;

use pocketmine\player\Player;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use Terpz710\BlueGamesQuest\EventListener;

class QuestForm {

    private $eventListener;

    public function __construct(EventListener $eventListener) {
        $this->eventListener = $eventListener;
    }

    public function sendQuestList(Player $player, array $quests) {
        if ($this->eventListener === null) {
            return;
        }

        $form = new SimpleForm(function (Player $player, $data) use ($quests) {
            if ($data === null) {
                return;
            }

            $questName = array_keys($quests)[$data];

            if (isset($quests[$questName])) {
                $questDetails = $quests[$questName];
                $this->sendQuestDetails($player, $questName, $questDetails);
            }
        });

        $form->setTitle("Quests");
        $form->setContent("Select a quest to view details:");

        foreach ($quests as $questName => $questData) {
            $form->addButton($questData["name"]);
        }

        $form->sendToPlayer($player);
    }

    public function sendQuestDetails(Player $player, $questName, $questDetails) {
        if ($this->eventListener === null) {
            return;
        }

        $description = $questDetails["description"];
        $reward = $questDetails["reward"];
        $requiredItem = $questDetails["required_item"];

        $form = new SimpleForm(function (Player $player, $data) use ($questName, $questDetails, $requiredItem) {
            if ($data === 0) {
                if ($this->hasMetQuestRequirements($player, $requiredItem)) {
                    $this->claimQuest($player, $questName, $questDetails);
                } else {
                    $player->sendMessage("You don't have the required items to complete this quest.");
                }
            }
        });

        $form->setTitle("Quest Details: " . $questDetails["name"]);
        $form->setContent("Description: $description\nReward: $reward");
        $form->addButton("Claim", 0, "path/to/image.png");

        $form->sendToPlayer($player);
    }

    private function hasMetQuestRequirements(Player $player, $requiredItem) {
        if ($requiredItem !== null) {
            $requiredItem = StringToItemParser::getInstance()->parse($requiredItem);
            if ($requiredItem instanceof Item) {
                return $player->getInventory()->contains($requiredItem);
            }
        }

        return true;
    }

    private function claimQuest(Player $player, $questName, $questDetails) {
        $reward = $questDetails["reward"];
        $player->sendMessage("You've completed the quest: $questName");
        $player->sendMessage("Rewards: $reward");

        $rewardItem = StringToItemParser::getInstance()->parse($reward);
        $player->getInventory()->addItem($rewardItem);

        $this->eventListener->markQuestAsCompleted($player, $questName);
    }
}
