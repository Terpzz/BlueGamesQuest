namespace Terpz710\BlueGamesQuest\Form;

use pocketmine\player\Player;
use jojoe77777\FormAPI\SimpleForm;
use Terpz710\BlueGamesQuest\Main;

class QuestForm {

    public static function sendQuestList(Player $player, Main $plugin) {
        $quests = $plugin->getQuests();

        $form = new SimpleForm(function (Player $player, $data) use ($quests) {
            if ($data === null) {
                return;
            }

            $questName = $data;

            if (isset($quests[$questName])) {
                $questDetails = $quests[$questName];
                self::sendQuestDetails($player, $questName, $questDetails);
            }
        });

        $form->setTitle("Quests");
        $form->setContent("Select a quest to view details:");

        foreach (array_keys($quests) as $questName) {
            $form->addButton($questName);
        }

        $form->sendToPlayer($player);
    }

    public static function sendQuestDetails(Player $player, $questName, $questDetails) {
        $description = $questDetails["description"];
        $reward = $questDetails["reward"];

        $player->sendMessage("Quest: $questName");
        $player->sendMessage("Description: $description");
        $player->sendMessage("Reward: $reward");
    }
}
