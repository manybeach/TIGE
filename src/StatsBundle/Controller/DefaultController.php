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


        return true;
    }

    public function victoryChartAction(){
                $sellsHistory = array(
            array(
                 "name" => "Bénéfices total",
                 "data" => array(9.1, 10.3, 6.5, 12.2, 5.3, 9.1, 11.1)
            ),
            array(
                 "name" => "Bénéfices pour la France",
                 "data" => array(6.6, 8.2, 0.76, 4.6, 2.1, 4.1, 3.9)
            ),

        );

        $dates = array(
            "21/06", "22/06", "23/06", "24/06", "25/06", "26/06", "27/06"
        );

        $ob = new Highchart();
        // ID de l'élement de DOM que vous utilisez comme conteneur
        $ob->chart->renderTo('barchart');
        $ob->title->text('Bénéfices du 21/06/2013 au 27/06/2013');
        $ob->chart->type('column');

        $ob->yAxis->title(array('text' => "Bénéfices (millions d'euros)"));

        $ob->xAxis->title(array('text' => "Date du jours"));
        $ob->xAxis->categories($dates);

        $ob->series($sellsHistory);

        return $this->render('StatsBundle:Default:index.html.twig', array(
            'barchart' => $ob
        ));
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
