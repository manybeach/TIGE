<?php

namespace TimelineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HotsController extends Controller
{
    public function indexAction()
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            $currentUser = $this->get('security.token_storage')->getToken()->getUser();
            var_dump($currentUser);
            exit();
        }
    }

    /**
     * @param $playerName
     * @return array
     */
    public function getDataFromHots($playerName,$idHots, $accountId)
    {

        /// ************ INIT CONNEXION ************
        $host = "http://www.hotslogs.com/";
        $urlNext = "PlayerSearch?Name=";
        $name = $playerName;
        $arrayAllGame = array();
        $dns = $host . $urlNext . $name;

        $curl = curl_init($dns);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_COOKIESESSION, true);

        $result = curl_exec($curl);
        curl_close($curl);

        //************ RECOVER ID PLAYER *************
        preg_match_all('#\/Player\/Profile\?PlayerID=(.*)\">here#ui', $result, $arrayId);

        if (!empty($arrayId)) {
            $id = $arrayId[1][0];
        }

        $urlNext = "Player/MatchHistory?PlayerID=" . $id;
        $dns = $host . $urlNext;

        $curl2 = curl_init($dns);
        curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl2, CURLOPT_COOKIESESSION, true);

        $result = curl_exec($curl2);
        curl_close($curl2);

        $arrayTableData = explode("<tbody>", $result);

        $arrayTableTr = explode('<tr', $arrayTableData[1]);

        for ($i = 1; $i <= 2; $i++) {
            $arrayGame = array();
            preg_match_all("#<td>(.*?)<\/td>#is", $arrayTableTr[$i], $td);
            preg_match_all("#<a title=\"(.*)\" href=\"#is",$td[1][2],$arrayNameHero);

            $heroName = $arrayNameHero[1][0];
            $mapName = $td[1][0];
            $timeGame = $td[1][1];
            $levelPlayer = $td[1][3];
            $currentMmr = $td[1][4];
            $varMmr = $td[1][5];
            $dateMatch = strtotime($td[1][6]);
            ($varMmr > 0) ? $resultGame = 'WIN' : $resultGame = 'LOOSE';

            $arrayGame = array('Game'=>$idHots,'idComm'=>$dateMatch,
                'player'=>$playerName,
                'accountId' =>$accountId,
                'heroName'=>$heroName,'mapName' => $mapName, 'timeGame' => $timeGame,
                'levelPlayer' => $levelPlayer, 'currentMmr' => $currentMmr, 'varMmr' => $varMmr,
                'createDate' => $dateMatch, 'resultGame' => $resultGame);

            array_push($arrayAllGame, $arrayGame);
        }

        return $arrayAllGame;
    }

}