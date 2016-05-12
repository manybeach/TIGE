<?php

namespace TimelineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use TimelineBundle\Controller\DefaultController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class LeagueController extends Controller
{

    public function indexAction()
    {
        #return $this->render('TimelineBundle:Default:accueil.html.twig');
        return new Response("OK");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDataFromLolAction($request,$securityContext,$thisB)
    {
        $comments = array();
        $threads = array();
        $displayData = false;
        if (!empty($securityContext)) {
            $server = 'euw';
            $arrayAllPlayers = array();


            $objLolId       = $thisB->getDoctrine()->getManager()->getRepository('AppBundle:Games');
            $arrayLolId     = $objLolId->findBy(array('name'=>'League Of Legend'));
            $lolId          = $arrayLolId[0]->getId();

            $repository = $thisB->getDoctrine()->getManager()->getRepository('AppBundle:AccountName');
            $objAccountName = $repository->findBy(array('user_id' => $securityContext, 'game_id' => $lolId));

            if (!empty($objAccountName)) {

                $summonerName = strtolower($objAccountName[0]->getName());
                $resultSummonerInfo = $this->getInfoSummoner($summonerName, $server);
                $summonerId = $resultSummonerInfo[$summonerName]['id'];
                $resultListMatch = $this->getMatchList($summonerId, $server);

                $profileIconId = $resultSummonerInfo[$summonerName]["profileIconId"];
                $summonerLevel = $resultSummonerInfo[$summonerName]["summonerLevel"];

                $arrayAllStats = array($summonerName => array());

                foreach ($resultListMatch["games"] as $arrayGame) {

                    $photo = $this->getPhotoByIdLol($profileIconId);
                    $arrayGame = $this->controleArrayLol($arrayGame);
                    if ($arrayGame["stats"]["numDeaths"] == 0) $arrayGame["stats"]["numDeaths"] = 1;
                    $arrayDataBySum = array('Game'=>$lolId,'player' => $summonerName, 'summonerId' => $summonerId, 'profileIconId' => $profileIconId, 'summonerLevel' => $summonerLevel, 'photo' => $photo, 'photoChamp' => $arrayGame["championId"], 'win' => $arrayGame["stats"]["win"], 'createDate' =>(int)round($arrayGame["createDate"]/1000), 'idComm' => (int)$arrayGame["createDate"], 'championId' => $arrayGame["championId"], 'goldEarned' => $arrayGame["stats"]["goldEarned"], 'numDeaths' => $arrayGame["stats"]["numDeaths"], 'championsKilled' => $arrayGame["stats"]["championsKilled"], 'minionsKilled' => $arrayGame["stats"]["minionsKilled"], 'assists' => $arrayGame["stats"]["assists"], 'timePlayed' => date('i:s', $arrayGame["stats"]["timePlayed"]), 'kda' => round(($arrayGame["stats"]["championsKilled"] + $arrayGame["stats"]["assists"]) / $arrayGame["stats"]["numDeaths"], 2));
                    array_push($arrayAllStats[$summonerName], $arrayDataBySum);
                }

                array_push($arrayAllPlayers, $arrayAllStats);
                $games = $this->sortGames($arrayAllPlayers);
                $displayData = true;
            } else {
                $displayData = false;
            }
        } else {
            $displayData = false;
        }
        if($displayData==false)
        {
            $games = '';
            $comments='';
            $threads='';
        }
        $dataLeague = array('AllData'=>$games,'comments'=>$comments,'threads'=>$threads);
        return $dataLeague;
    }


    /**
     * @param $arrayGames
     * @return array
     */
    private function sortGames($arrayGames)
    {
        $temp = array();
        foreach ($arrayGames as $player) {
            foreach ($player as $games) {
                foreach ($games as $game) {
                    array_push($temp, $game);
                }
            }
        }
        $tmp = Array();
        foreach ($temp as &$ma) $tmp[] = &$ma["createDate"];
        array_multisort($tmp, SORT_DESC, $temp);

        return $temp;
    }

    private function getNameByIdChampionLol($idChampion)
    {
        $photoChamp = 'bundles/framework/images/LoL/1.png';
        return $photoChamp;

    }

    /**
     * @param $idPhoto
     * @return string (url)
     */
    private function getPhotoByIdLol($idPhoto)
    {
        $url = "http://ddragon.leagueoflegends.com/cdn/6.7.1/img/profileicon/$idPhoto.png";
        return $url;
    }

    /**
     * @param $summonerName
     * @param $server
     * @return arrayOfSummonnersInfo
     * @internal param $summonerId
     */
    private function getInfoSummoner($summonerName, $server)
    {
        $url = 'https://' . $server . '.api.pvp.net/api/lol/' . $server . '/v1.4/summoner/by-name/' . $summonerName . '?api_key=0610f47d-dba7-46ff-84c7-fc9eeee8b788';
        $resultJson = file_get_contents($url);
        $result = json_decode($resultJson, true);
        return $result;
    }

    private function getMatchList($summonerId, $server)
    {
        $url = 'https://' . $server . '.api.pvp.net/api/lol/' . $server . '/v1.3/game/by-summoner/' . $summonerId . '/recent?api_key=0610f47d-dba7-46ff-84c7-fc9eeee8b788';
        $resultJson = file_get_contents($url);
        $result = json_decode($resultJson, true);

        return $result;
    }

    /**
     * Add gamers
     */
    private function addGamers()
    {
        $user = new Users();
        $user->setName('aoren');

        $em = $this->getDoctrine()->getManager();

        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $em->persist($user);

        // actually executes the queries (i.e. the INSERT query)
        $em->flush();
    }

    /**
     * @param $arrayGame
     * @return array
     */
    private function controleArrayLol($arrayGame)
    {
        if (empty($arrayGame["stats"]["win"])) $arrayGame["stats"]["win"] = 0;
        if (empty($arrayGame["stats"]["goldEarned"])) $arrayGame["stats"]["goldEarned"] = 0;
        if (empty($arrayGame["stats"]["numDeaths"])) $arrayGame["stats"]["numDeaths"] = 0;
        if (empty($arrayGame["stats"]["championsKilled"])) $arrayGame["stats"]["championsKilled"] = 0;
        if (empty($arrayGame["stats"]["minionsKilled"])) $arrayGame["stats"]["minionsKilled"] = 0;
        if (empty($arrayGame["stats"]["assists"])) $arrayGame["stats"]["assists"] = 0;
        if (empty($arrayGame["stats"]["timePlayed"])) $arrayGame["stats"]["timePlayed"] = 0;

        return $arrayGame;
    }

    /**
     * @return array
     */
    function array_msort($array, $cols)
    {
        $colarr = array();
        foreach ($cols as $col => $order) {
            $colarr[$col] = array();
            foreach ($array as $k => $row) {
                $colarr[$col]['_' . $k] = strtolower($row[$col]);
            }
        }
        $eval = 'array_multisort(';
        foreach ($cols as $col => $order) {
            $eval .= '$colarr[\'' . $col . '\'],' . $order . ',';
        }
        $eval = substr($eval, 0, -1) . ');';
        eval($eval);
        $ret = array();
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k, 1);
                if (!isset($ret[$k])) $ret[$k] = $array[$k];
                $ret[$k][$col] = $array[$k][$col];
            }
        }
        return $ret;
    }




}
