<?php

namespace TimelineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        #return $this->render('TimelineBundle:Default:index.html.twig');
        return new Response("OK");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDataFromLolAction()
    {
        $arrayAllStats = array();

        $server = 'euw';

        // $this->addGamers();
        $gamers = $this->getDoctrine()
            ->getRepository('TimelineBundle\Entity\Users')
            ->findAll();

        $arrayAllPlayers = array();
        foreach ($gamers as $gamer) {
            $summonerName = strtolower($gamer->getName());
            $resultSummonerInfo = $this->getInfoSummoner($summonerName, $server);
            $summonerId = $resultSummonerInfo[$summonerName]['id'];
            $resultListMatch = $this->getMatchList($summonerId, $server);

            $profileIconId = $resultSummonerInfo[$summonerName]["profileIconId"];
            $summonerLevel = $resultSummonerInfo[$summonerName]["summonerLevel"];

            $arrayAllStats = array($summonerName => array());

            foreach ($resultListMatch["games"] as $arrayGame) {

                $photo = $this->getPhotoByIdLol($profileIconId);
                $arrayGame = $this->controleArrayLol($arrayGame);
                $arrayDataBySum = array(
                    'summonerId' => $summonerId,
                    'profileIconId' => $profileIconId,
                    'summonerLevel' => $summonerLevel,
                    'photo' => $photo,
                    'win' => $arrayGame["stats"]["win"],
                    'createDate' => date('d/m/Y H:i:s', $arrayGame["createDate"] / 1000),
                    'championId' => $arrayGame["championId"],
                    'goldEarned' => $arrayGame["stats"]["goldEarned"],
                    'numDeaths' => $arrayGame["stats"]["numDeaths"],
                    'championsKilled' => $arrayGame["stats"]["championsKilled"],
                    'minionsKilled' => $arrayGame["stats"]["minionsKilled"],
                    'assists' => $arrayGame["stats"]["assists"],
                    'timePlayed' => date('i:s', $arrayGame["stats"]["timePlayed"]),
                    'kda' => ($arrayGame["stats"]["championsKilled"] + $arrayGame["stats"]["assists"]) / $arrayGame["stats"]["numDeaths"]
                );
                array_push($arrayAllStats[$summonerName], $arrayDataBySum);
            }

            array_push($arrayAllPlayers, $arrayAllStats);
        }
        return $this->render('TimelineBundle:Default:index.html.twig', array('AllData' => $arrayAllPlayers));
    }

    private function getPhotoByIdLol($idPhoto)
    {
        $url = "http://ddragon.leagueoflegends.com/cdn/6.7.1/img/profileicon/$idPhoto.png";
        return $url;
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

    private function controleArrayLol($arrayGame)
    {
        if(empty($arrayGame["stats"]["win"])) $arrayGame["stats"]["win"] = 0;
        if(empty($arrayGame["stats"]["goldEarned"]))$arrayGame["stats"]["goldEarned"] = 0;
        if(empty($arrayGame["stats"]["numDeaths"])) $arrayGame["stats"]["numDeaths"]=0;
        if(empty($arrayGame["stats"]["championsKilled"]))$arrayGame["stats"]["championsKilled"] =0;
        if(empty($arrayGame["stats"]["minionsKilled"]))$arrayGame["stats"]["minionsKilled"] =0;
        if(empty($arrayGame["stats"]["assists"]))$arrayGame["stats"]["assists"]=0;
        if(empty($arrayGame["stats"]["timePlayed"]))$arrayGame["stats"]["timePlayed"]=0;

        return $arrayGame;
    }
}
