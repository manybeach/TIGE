<?php

namespace StatsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('StatsBundle:Default:index.html.twig');
    }


    public function getStatsDataAction()
    {
        $arrayStatsSummary = array();
        $arrayStatsRanked =array();
        $server = 'euw';
        $summonerName = strtolower('BabyKywa');

        // on recupere le summonerID à partir du summonerName
        $summonerId = $this->getSummonerId($summonerName,$server);

        // on recupere le résumé des stats du joueur à partir de son ID
        $resultStatsSummary = $this->getStatsSummary($summonerId, $server);


        foreach ($resultStatsSummary["playerStatSummaries"] as $arrayStats) {

            if (empty($arrayStats["wins"])) {
                $arrayStats["wins"] = 0;
            }
            if (empty($arrayStats["losses"])) {
                $arrayStats["losses"] = 0;
            }
            $arrayDataBySum = array(
                'wins' => $arrayStats["wins"],
                'losses' => $arrayStats["losses"],
                'playerStatSummaryType' => $arrayStats["playerStatSummaryType"]
            );
            array_push($arrayStatsSummary, $arrayDataBySum);
        }


        // on recupere les stats ranked du joueur à partir de son ID
        $resultStatsRanked= $this->getStatsRanked($summonerId, $server);
        foreach ($resultStatsRanked["champions"] as $arrayStats) {

            if (empty($arrayStats["wins"])) {
                $arrayStats["wins"] = 0;
            }
            if (empty($arrayStats["losses"])) {
                $arrayStats["losses"] = 0;
            }
            $arrayDataBySum = array(
                'id' => $arrayStats["id"],
                'totalSessionsPlayed' => $arrayStats["stats"]["totalSessionsPlayed"],
                'totalSessionsLost' => $arrayStats["stats"]["totalSessionsLost"],
                'totalSessionsWon' => $arrayStats["stats"]["totalSessionsWon"],
            );
            array_push($arrayStatsRanked, $arrayDataBySum);
        }

    var_dump($arrayStatsRanked);
        return true;
    }




    /**
     * @param $summonerName
     * @param $server
     * @return arrayOfSummonnersInfo
     * @internal param $summonerId
     */
    private
    function getSummonerId(
        $summonerName,
        $server
    ) {
        $url = 'https://'.$server.'.api.pvp.net/api/lol/'.$server.'/v1.4/summoner/by-name/'.$summonerName.'?api_key=0610f47d-dba7-46ff-84c7-fc9eeee8b788';
        $resultJson = file_get_contents($url);
        $result = json_decode($resultJson, true);
        $summonerId = $result[$summonerName]['id'];
        return $summonerId;
    }


    private
    function getStatsSummary(
        $summonerId,
        $server
    ) {
        $url = 'https://'.$server.'.api.pvp.net/api/lol/'.$server.'/v1.3/stats/by-summoner/'.$summonerId.'/summary?api_key=0610f47d-dba7-46ff-84c7-fc9eeee8b788';
        $resultJson = file_get_contents($url);
        $result = json_decode($resultJson, true);

        return $result;
    }

    private function getStatsRanked($summonerId, $server){
        $url = 'https://'.$server.'.api.pvp.net/api/lol/'.$server.'/v1.3/stats/by-summoner/'.$summonerId.'/ranked?api_key=0610f47d-dba7-46ff-84c7-fc9eeee8b788';
        $resultJson = file_get_contents($url);
        $result = json_decode($resultJson, true);

        return $result;
    }
}
