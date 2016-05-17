<?php

namespace StatsBundle\Controller;

use AppBundle;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Ob\HighchartsBundle\Highcharts\ChartInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Controller\FavouritesController;


class DefaultController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('StatsBundle:Default:index.html.twig');
    }

    /**
     *
     * @param $idUser
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayStatAction($idUser)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('AppBundle:champions');

        $me = false;
        $follow = false;
        if ($idUser == false) {
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $idUser = $user->getId();
            $me = true;
        } else {
            $follow = $this->isFavourite($idUser);

        }

        $summonerName = $this->getSummonerNameByUserId($idUser);
        $arrayStatsRankChampions = $this->getStatsDataRankAction($summonerName);
        $arrayStatsSummaryChampions = $this->getStatsDataSummaryAction($summonerName);


        $ob = new Highchart();
        $ob->chart->renderTo('piechart');
        $ob->title->text('Nombre de parties jouées par champion en parties classées');
        $ob->plotOptions->pie(array('allowPointSelect' => true, 'cursor' => 'pointer', 'dataLabels' => array('enabled' => true), 'showInLegend' => false));
        $championsData = array();
        $i = 0;

        foreach ($arrayStatsRankChampions as $hero) {
            $heroName = $repo->findBy(array("idChampion" => $hero['id']));
            (empty($hero["id"])) ? $champName = 'Inconnu': $champName = $this->getChampionName($hero["id"]);
            $arrayStatsRankChampions[$i]['champName']=$champName;

            if (!empty($heroName)) {
                array_push($championsData, array($heroName[0]->getChampionName(), $hero["totalSessionsPlayed"]));
            }
            $i++;
        }
        $ob->series(array(array('type' => 'pie', 'name' => 'Nombre de parties jouées', 'data' => $championsData)));

        //Graphique en barre du nombre de parties gagnées par type de partie
        $arrayWins = array();
        $arrayPlayerstatSummaryType = array();
        foreach ($arrayStatsSummaryChampions as $summary) {
            array_push($arrayWins, $summary['wins']);
            array_push($arrayPlayerstatSummaryType, $summary['playerStatSummaryType']);
        }

        $ob2 = new Highchart();
        $ob2->chart->renderTo('barchart');
        $ob2->title->text('Nombre de parties gagnées par type de partie');
        $ob2->chart->type('column');

        $ob2->yAxis->title(array('text' => 'Nombre de parties gagnées'));

        $ob2->xAxis->title(array('text' => 'Type de partie'));
        $ob2->xAxis->categories($arrayPlayerstatSummaryType);

        $ob2->plotOptions->bar(array('allowPointSelect' => true, 'cursor' => 'pointer', 'dataLabels' => array('enabled' => true), 'showInLegend' => false));

        $ob2->series(array(array('type' => 'bar', 'name' => 'Nombre de parties gagnées', 'data' => $arrayWins)));

        $ob3 = new Highchart();
        $ob3->chart(array('type' => 'heatmap', 'marginTop' => 40, 'marginBottom' => 40,));
        $ob3->title->text('Données par Champions en RNKD');
        $ob3->xAxis->categories($arrayStatsRankChampions);

        $donneesob3 = array('totalSessionsPlayed', 'totalSessionsLost', 'totalSessionsWon', 'totalChampionKills', 'totalMinionKills', 'totalGoldEarned', 'totalDeathsPerSession', 'totalDamageDealt', 'totalDamageTaken',);
        $ob3->yAxis->categories($donneesob3);
        $ob3->yAxis->title(array('text' => 'Données'));
        $ob3->xAxis->title(array('text' => 'Champions'));
        $colorAxis = array('min' => 0, 'minColor' => '#FFFFFF', 'maxColor' => 'Highcharts.getOptions().colors[0]',);
        $ob3->colorAxis(array('min' => 0, 'minColor' => '#FFFFFF', 'maxColor' => 'Highcharts.getOptions().colors[0]',));
        $legend = array('align' => 'right', 'layout' => 'vertical', 'margin' => 0, 'verticalAlign' => 'top', 'y' => 25, 'symbolHeight' => 320,);
        $ob3->legend(array('align' => 'right', 'layout' => 'vertical', 'margin' => 0, 'verticalAlign' => 'top', 'y' => 25, 'symbolHeight' => 320,));
        $ob3->tooltip->formatter('function () {
                return \'<b>\' + this.series.xAxis.categories[this.point.x] + \'</b> sold <br><b>\' +
                    this.point.value + \'</b> items on <br><b>\' + this.series.yAxis.categories[this.point.y] + \'</b>\';
            }');

        $ob3->series(array(array('name' => 'Nombre de parties gagnées', 'data' => $championsData)));
        return $this->render('StatsBundle:Default:index.html.twig', array('piechart' => $ob, 'barchart' => $ob2, 'championsData' => $arrayStatsRankChampions, 'summonerName' => strtoupper($summonerName), 'me' => $me, 'follow' => $follow, 'idUser' => $idUser,

            ));
    }

    /**
     * @param $idUser
     * @return string
     */
    public function getSummonerNameByUserId($idUser)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:AccountName');

        $arrayAccountName = $repository->findBy(array('id' => $idUser,));
        $accountName = $arrayAccountName[0]->getName();
        return $accountName;
    }

    /**
     * @param $summonerName
     * @return array
     */
    public function getStatsDataRankAction($summonerName)
    {
        $arrayStatsRanked = array();
        $server = 'euw';

        // on recupere le summonerID à partir du summonerName
        $summonerId = $this->getSummonerId($summonerName, $server);

        $resultStatsRanked = $this->getStatsRanked($summonerId, $server);

        foreach ($resultStatsRanked["champions"] as $arrayStats) {
            if(!empty($arrayStats['id']))
                $arrayDataBySum = array('id' => $arrayStats["id"], 'totalSessionsPlayed' => $arrayStats["stats"]["totalSessionsPlayed"], 'totalSessionsLost' => $arrayStats["stats"]["totalSessionsLost"], 'totalSessionsWon' => $arrayStats["stats"]["totalSessionsWon"], 'totalChampionKills' => $arrayStats["stats"]["totalChampionKills"], 'totalMinionKills' => $arrayStats["stats"]["totalMinionKills"], 'totalGoldEarned' => $arrayStats["stats"]["totalGoldEarned"], 'totalAssists' => $arrayStats["stats"]["totalAssists"], 'totalDeathsPerSession' => $arrayStats["stats"]["totalDeathsPerSession"], 'totalDamageDealt' => $arrayStats["stats"]["totalDamageDealt"], 'totalDamageTaken' => $arrayStats["stats"]["totalDamageTaken"],

            );
            array_push($arrayStatsRanked, $arrayDataBySum);
        }
        return $arrayStatsRanked;
    }

    /**
     * @param $summonerName
     * @return array
     */
    public function getStatsDataSummaryAction($summonerName)
    {
        $arrayStatsSummary = array();
        $server = 'euw';

        // on recupere le summonerID à partir du summonerName
        $summonerId = $this->getSummonerId($summonerName, $server);

        $resultStatsSummary = $this->getStatsSummary($summonerId, $server);

        foreach ($resultStatsSummary["playerStatSummaries"] as $arrayStats) {

            $arrayDataBySum = array('wins' => $arrayStats["wins"], 'playerStatSummaryType' => $arrayStats["playerStatSummaryType"],

            );
            array_push($arrayStatsSummary, $arrayDataBySum);
        }
        return $arrayStatsSummary;
    }


    /**
     * @param $summonerName
     * @param $server
     * @return arrayOfSummonnersInfo
     * @internal param $summonerId
     */
    private function getSummonerId($summonerName, $server)
    {
        $url = 'https://' . $server . '.api.pvp.net/api/lol/' . $server . '/v1.4/summoner/by-name/' . $summonerName . '?api_key=0610f47d-dba7-46ff-84c7-fc9eeee8b788';
        $resultJson = file_get_contents($url);
        $result = json_decode($resultJson, true);
        $summonerName= strtolower($summonerName);
        $summonerId = $result[$summonerName]['id'];
        return $summonerId;
    }


    /**
     * @param $summonerId
     * @param $server
     * @return mixed
     */
    private function getStatsSummary($summonerId, $server)
    {
        $url = 'https://' . $server . '.api.pvp.net/api/lol/' . $server . '/v1.3/stats/by-summoner/' . $summonerId . '/summary?api_key=0610f47d-dba7-46ff-84c7-fc9eeee8b788';
        $resultJson = file_get_contents($url);
        $result = json_decode($resultJson, true);

        return $result;
    }

    /**
     * @param $summonerId
     * @param $server
     * @return mixed
     */
    private function getStatsRanked($summonerId, $server)
    {
        $url = 'https://' . $server . '.api.pvp.net/api/lol/' . $server . '/v1.3/stats/by-summoner/' . $summonerId . '/ranked?api_key=0610f47d-dba7-46ff-84c7-fc9eeee8b788';
        $resultJson = file_get_contents($url);
        $result = json_decode($resultJson, true);

        return $result;
    }

    /**
     * @param $idUser
     * @return bool
     */
    public function isFavourite($idUser)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $id_user = $user->getId();

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Favourites');

        $arrayFavourites = $repository->findBy(array('idAccount' => $id_user));
        $result = false;

        if (!empty($arrayFavourites)) {
            $mesfavoris = $arrayFavourites[0]->getIdFavourites();
            $favourites = explode(';', $mesfavoris);
            foreach ($favourites as $favourite) {
                if ($idUser == $favourite) $result = true;
            }
        }
        return $result;
    }

    /**
     * @param $idChamp
     * @return string
     */
    public function getChampionName($idChamp)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:champions');
        $champ = $repository->findBy(array("idChampion" => $idChamp));
        $champName = $champ[0]->getChampionName();
        return $champName;
    }
}
