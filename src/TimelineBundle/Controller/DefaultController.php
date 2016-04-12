<?php

namespace TimelineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('TimelineBundle:Default:index.html.twig');
    }
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDataFromLolAction()
    {
        $arrayAllStats      = array();

        $server             = 'euw';

        // $this->addGamers();
        $gamers = $this->getDoctrine()
            ->getRepository('TimelineBundle\Entity\Users')
            ->findAll();

        $arrayAllPlayers = array();
        foreach($gamers as $gamer) {
            $summonerName = strtolower($gamer->getName());
            $resultSummonerInfo = $this->getInfoSummoner($summonerName, $server);
            $summonerId = $resultSummonerInfo[$summonerName]['id'];
            $resultListMatch = $this->getMatchList($summonerId, $server);

            $profileIconId = $resultSummonerInfo[$summonerName]["profileIconId"];
            $summonerLevel = $resultSummonerInfo[$summonerName]["summonerLevel"];

            $arrayAllStats = array($summonerName => array());

            foreach ($resultListMatch["games"] as $arrayGame) {
                $arrayDataBySum = array(
                    'summonerId' => $summonerId,
                    'profileIconId' => $profileIconId,
                    'summonerLevel' => $summonerLevel,
                    'win' => $arrayGame["stats"]["win"],
                    'createDate' => $arrayGame["createDate"],
                    'championId' => $arrayGame["championId"],
                    'goldEarned' => $arrayGame["stats"]["goldEarned"],
                    'numDeaths' => $arrayGame["stats"]["numDeaths"],
                    'championsKilled' => $arrayGame["stats"]["championsKilled"],
                    'minionsKilled' => $arrayGame["stats"]["minionsKilled"],
                    'assists' => $arrayGame["stats"]["assists"],
                    'timePlayed' => $arrayGame["stats"]["timePlayed"]
                );
                array_push($arrayAllStats[$summonerName], $arrayDataBySum);
            }
            array_push($arrayAllPlayers, $arrayAllStats);
        }
        return $this->render('TimelineBundle:Default:index.html.twig', array('AllData' => $arrayAllPlayers));
    }

    /**
     * @param $summonerId
     * @param $server
     * @return arrayOfSummonnersInfo
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
        if($resultJson != false)
        {
            $result = json_decode($resultJson, true);
        }
        return $result;
    }

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
}
