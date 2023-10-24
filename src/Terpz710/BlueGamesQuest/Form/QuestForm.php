<?php

namespace Terpz710\BlueGamesQuest\Form;

use pocketmine\player\Player;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\item\StringToItemParser;
use Terpz710\BlueGamesQuest\Main;
use Terpz710\BlueGamesQuest\EventListener;

class QuestForm {

    public static function sendQuestList(Player $player, Main $plugin) {
        $quests = $plugin->getQuests();

        $form = new SimpleForm(function (Player $player, $data) use ($quests, $plugin) {
            if ($data === null) {
                return;
            }

            $questName = $data;

            if (isset($quests[$questName])) {
                $questDetails = $quests[$questName];
                self::sendQuestDetails($player, $questName, $questDetails, $plugin);
            }
        });

        $form->setTitle("Quests");
        $form->setContent("Select a quest to view details:");

        foreach (array_keys($quests) as $questName) {
            $form->addButton($quests[$questName]["name"]);
        }

        $form->sendToPlayer($player);
    }

    public static function sendQuestDetails(Player $player, $questName, $questDetails, Main $plugin) {
        $description = $questDetails["description"];
        $reward = $questDetails["reward"];
        $requiredItem = $questDetails["required_item"];

        $form = new SimpleForm(function (Player $player, $data) use ($questName, $requiredItem, $plugin) {
            if ($data === null) {
                return;
            }

            if ($data === 0) {
                if (self::hasRequiredItem($player, $requiredItem)) {
                    self::handleQuestCompletion($player, $questName, $plugin);
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

    private static function hasRequiredItem(Player $player, $requiredItem) {
        if ($requiredItem !== null) {
            $parsedItem = StringToItemParser::getInstance()->parse($requiredItem);
            return $player->getInventory()->contains($parsedItem);
        }
        return true;
    }

    private static function handleQuestCompletion(Player $player, $questName, Main $plugin) {
        $player->addTag("Quest: $questName");
        $plugin->markQuestAsCompleted($player, $questName);
    }
}
