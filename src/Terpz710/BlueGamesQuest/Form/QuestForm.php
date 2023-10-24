<?php

namespace Terpz710\BlueGamesQuest\Form;

use pocketmine\player\Player;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\item\StringToItemParser;
use Terpz710\BlueGamesQuest\EventListener;

class QuestForm {

    private $eventListener;

    public function __construct(EventListener $eventListener) {
        $this->eventListener = $eventListener;
    }

    public function sendQuestList(Player $player, array $quests) {
        $form = new SimpleForm(function (Player $player, $data) use ($quests) {
            if ($data === null) {
                return;
            }

            $questName = $data;

            if (isset($quests[$questName])) {
                $questDetails = $quests[$questName];
                $this->sendQuestDetails($player, $questName, $questDetails);
            }
        });

        $form->setTitle("Quests");
        $form->setContent("Select a quest to view details:");

        foreach (array_keys($quests) as $questName) {
            $form->addButton($quests[$questName]["name"]);
        }

        $form->sendToPlayer($player);
    }

    public function sendQuestDetails(Player $player, $questName, $questDetails) {
        $description = $questDetails["description"];
        $reward = $questDetails["reward"];
        $requiredItem = $questDetails["required_item"];

        $form = new SimpleForm(function (Player $player, $data) use ($questName, $requiredItem) {
            if ($data === null) {
                return;
            }

            if ($data === 0) {
                if ($this->hasRequiredItem($player, $requiredItem)) {
                    $this->handleQuestCompletion($player, $questName);
                } else {
                    $player->sendMessage("You do not have the required items to claim this quest.");
                }
            }
        });

        $form->setTitle("Quest Details: " . $questDetails["name"]);
        $form->setContent("Description: $description\nReward: $reward");

        $form->addButton("Claim");

        $form->sendToPlayer($player);
    }

    private function hasRequiredItem(Player $player, $requiredItem) {
        if ($requiredItem !== null) {
            $parsedItem = StringToItemParser::getInstance()->parse($requiredItem);
            return $player->getInventory()->contains($parsedItem);
        }
        return true;
    }

    private function handleQuestCompletion(Player $player, $questName) {
        $player->addTag("Quest: $questName");
        $this->eventListener->markQuestAsCompleted($player, $questName);
    }
}
