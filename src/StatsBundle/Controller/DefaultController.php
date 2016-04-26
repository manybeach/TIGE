<?php

namespace StatsBundle\Controller;

use AppBundle;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Ob\HighchartsBundle\Highcharts\ChartInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Controller\FavouritesController;


class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('StatsBundle:Default:index.html.twig');
    }

    public function displayStatAction($idUser)
    {
        $me=false;
        if($idUser == false) {
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $idUser = $user->getId();
            $me = true;
        }
        else
        {
            $follow = $this->isFavourite($idUser);
            $summonerName = $this->getSummonerNameByUserId($idUser);
            
        }

        $arrayStatsChampions = $this->getStatsDataAction($summonerName);


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
        foreach ($arrayStatsChampions as $hero) {
            $heroName = $this->getNameChampById($hero["id"]);
            if ($heroName != false)
                array_push($data, array($heroName, $hero["totalSessionsPlayed"]));
        }
        $ob->series(array(array('type' => 'pie', 'name' => 'Browser share', 'data' => $data)));

        return $this->render('StatsBundle:Default:index.html.twig', array(
            'piechart' => $ob,
            'summonerName' => strtoupper($summonerName),
            'me' => $me,
            'follow' =>$follow,
            'idUser' => $idUser
            
        ));
    }

    public function getSummonerNameByUserId($idUser)
    {
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:AccountName');

        $arrayAccountName = $repository->findBy(array(
            'user_id' => $idUser,
            'game_id' => 1
        ));

        $accountName = $arrayAccountName[0]->getName();
        return $accountName;
    }

    public function getStatsDataAction($summonerName)
    {
        $arrayStatsSummary = array();
        $arrayStatsRanked = array();
        $server = 'euw';

        // on recupere le summonerID à partir du summonerName
        $summonerId = $this->getSummonerId($summonerName, $server);

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
        if (!empty($id)) {
            $url = "https://global.api.pvp.net/api/lol/static-data/euw/v1.2/champion/$id?api_key=0610f47d-dba7-46ff-84c7-fc9eeee8b788";
            $resultJson = file_get_contents($url);
            $result = json_decode($resultJson, true);
            $result = $result["name"];

        }

        return $result;

    }

    public function isFavourite($idUser){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $id_user = $user->getId();

        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Favourites');

        $arrayFavourites = $repository->findBy(array(
            'idAccount' => $id_user));
        $result = false;

        if (!empty($arrayFavourites) ){
            $mesfavoris = $arrayFavourites[0]->getIdFavourites();
            $favourites = explode(';', $mesfavoris);
            foreach ($favourites as $favourite) {
                if ($idUser == $favourite)
                    $result = true;
            }
        }
        return $result;
    }
}
