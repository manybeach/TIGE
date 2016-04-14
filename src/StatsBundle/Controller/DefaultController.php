<?php

namespace StatsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('StatsBundle:Default:index.html.twig');
    }


    public function getStatsData()
    {
        $arrayAllStats = array();

        $server = 'euw';


        $summonerName = strtolower('BabyKywa');
        $resultSummonerInfo = $this->getInfoSummoner($summonerName, $server);
        $summonerId = $resultSummonerInfo[$summonerName]['id'];
        $resultStatsGamer = $this->getStatsGamer($summonerId, $server);

        $arrayAllStats = array($summonerName => array());

        foreach ($resultStatsGamer["playerStatSummaries"] as $arrayStats) {

            if (empty($arrayGame["stats"]["win"])) {
                $arrayGame["stats"]["win"] = 0;
            }
            $arrayDataBySum = array(
                'wins' => $arrayStats["wins"],
                'losses' => $arrayStats["losses"],
            );
            array_push($arrayAllStats[$summonerName], $arrayDataBySum);
        }
        array_push($arrayAllPlayers, $arrayAllStats);

        $games = $this->sortGames($arrayAllPlayers);

        return $this->render('TimelineBundle:Default:index.html.twig', array('AllData' => $games));

    }

    /**
     * @param $summonerName
     * @param $server
     * @return arrayOfSummonnersInfo
     * @internal param $summonerId
     */
    private function getInfoSummoner($summonerName, $server)
    {
        $url = 'https://'.$server.'.api.pvp.net/api/lol/'.$server.'/v1.4/summoner/by-name/'.$summonerName.'?api_key=0610f47d-dba7-46ff-84c7-fc9eeee8b788';
        $resultJson = file_get_contents($url);
        $result = json_decode($resultJson, true);

        return $result;
    }


    private function getStatsGamer($summonerId, $server)
    {
        $url = 'https://'.$server.'.api.pvp.net/api/lol/'.$server.'/v1.3/stats/by-summoner/'.$summonerId.'?api_key=0610f47d-dba7-46ff-84c7-fc9eeee8b788';
        $resultJson = file_get_contents($url);
        $result = json_decode($resultJson, true);

        return $result;
    }
}
