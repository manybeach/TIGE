<?php

namespace StatsBundle\Controller;

use Ob\HighchartsBundle\Highcharts\Highchart;
use Ob\HighchartsBundle\Highcharts\ChartInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('StatsBundle:Default:index.html.twig');
    }

    public function sellsHistoryAction()
    {

        $arrayStatsChampions = $this->getStatsDataAction();

        $ob = new Highchart();
        $ob->chart->renderTo('piechart');
        $ob->title->text('Nombre de parties jouées par champion');
        $ob->plotOptions->pie(array(
            'allowPointSelect' => true,
            'cursor' => 'pointer',
            'dataLabels' => array('enabled' => true),
            'showInLegend' => false
        ));
        $data = array();
        foreach($arrayStatsChampions as $hero)
        {
            $heroName = $this->getNameChampById($hero["id"]);
            if($heroName != false)
                array_push($data,array($heroName, $hero["totalSessionsPlayed"]));
        }
        $ob->series(array(array('type' => 'pie', 'name' => 'Browser share', 'data' => $data)));

        return $this->render('StatsBundle:Default:index.html.twig', array(
            'piechart' => $ob
        ));
    }


    public function getStatsDataAction()
    {
        $arrayStatsSummary = array();
        $arrayStatsRanked = array();
        $server = 'euw';
        $summonerName = strtolower('BabyKywa');

        // on recupere le summonerID à partir du summonerName
        $summonerId = $this->getSummonerId($summonerName, $server);

        /* // on recupere le résumé des stats du joueur à partir de son ID
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
         }*/


        // on recupere les stats ranked du joueur à partir de son ID
        $resultStatsRanked = $this->getStatsRanked($summonerId, $server);

        foreach ($resultStatsRanked["champions"] as $arrayStats) {

            $arrayDataBySum = array(
                'id' => $arrayStats["id"],
                'totalSessionsPlayed' => $arrayStats["stats"]["totalSessionsPlayed"],
                'totalSessionsLost' => $arrayStats["stats"]["totalSessionsLost"],
                'totalSessionsWon' => $arrayStats["stats"]["totalSessionsWon"],
                'totalChampionKills' => $arrayStats["stats"]["totalChampionKills"],
                'totalMinionKills' => $arrayStats["stats"]["totalMinionKills"],
                'totalGoldEarned' => $arrayStats["stats"]["totalGoldEarned"],
                'totalAssists' => $arrayStats["stats"]["totalAssists"],
                'totalDeathsPerSession' => $arrayStats["stats"]["totalDeathsPerSession"],
                'totalDamageDealt' => $arrayStats["stats"]["totalDamageDealt"],
                'totalDamageTaken' => $arrayStats["stats"]["totalDamageTaken"],


            );
            array_push($arrayStatsRanked, $arrayDataBySum);
        }
        return $arrayStatsRanked;
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
    )
    {
        $url = 'https://' . $server . '.api.pvp.net/api/lol/' . $server . '/v1.4/summoner/by-name/' . $summonerName . '?api_key=0610f47d-dba7-46ff-84c7-fc9eeee8b788';
        $resultJson = file_get_contents($url);
        $result = json_decode($resultJson, true);
        $summonerId = $result[$summonerName]['id'];
        return $summonerId;
    }


    private
    function getStatsSummary(
        $summonerId,
        $server
    )
    {
        $url = 'https://' . $server . '.api.pvp.net/api/lol/' . $server . '/v1.3/stats/by-summoner/' . $summonerId . '/summary?api_key=0610f47d-dba7-46ff-84c7-fc9eeee8b788';
        $resultJson = file_get_contents($url);
        $result = json_decode($resultJson, true);

        return $result;
    }

    private function getStatsRanked($summonerId, $server)
    {
        $url = 'https://' . $server . '.api.pvp.net/api/lol/' . $server . '/v1.3/stats/by-summoner/' . $summonerId . '/ranked?api_key=0610f47d-dba7-46ff-84c7-fc9eeee8b788';
        $resultJson = file_get_contents($url);
        $result = json_decode($resultJson, true);

        return $result;
    }

    private function getNameChampById($id)
    {
        $result = false;
        if(!empty($id))
        {
            $url = "https://global.api.pvp.net/api/lol/static-data/euw/v1.2/champion/$id?api_key=0610f47d-dba7-46ff-84c7-fc9eeee8b788";
            $resultJson = file_get_contents($url);
            $result = json_decode($resultJson, true);
            $result = $result["name"];

        }

        return $result;

    }
}
