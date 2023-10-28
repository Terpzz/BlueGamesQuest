<?php

namespace Terpz710\BlueGamesQuest\Form;

use pocketmine\player\Player;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\item\StringToItemParser;
use Terpz710\BlueGamesQuest\EventListener;

class QuestForm {

    private $eventListener;

    public function setEventListener(EventListener $eventListener) {
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
        if ($this->eventListener === null) {
            return;
        }

        $description = $questDetails["description"];
        $reward = $questDetails["reward"];
        $requiredItem = $questDetails["required_item"];

        $form = new SimpleForm(null);

        $form->setTitle("Quest Details: " . $questDetails["name"]);
        $form->setContent("Description: $description\nReward: $reward");

        $form->addButton("Claim", 0, "path/to/image.png");

        $form->sendToPlayer($player);
    }
}
