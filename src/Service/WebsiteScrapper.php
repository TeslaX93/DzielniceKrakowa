<?php

namespace App\Service;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use App\Entity\District;
use App\Repository\DistrictRepository;

class WebsiteScrapper
{
    private const URLBIP = "https://www.bip.krakow.pl";

    /**
     * @return District[]
     */
    public function scrapper(): array
    {
        $districtsList = $this->listOfSubpagesScrapper();
        $districtsInfo = [];
        foreach ($districtsList as $district) {
            $districtsInfo[] = $this->getDistrictsInfo($district);
        }
        return $districtsInfo;
    }

    /**
     * @throws \Exception
     */
    private function listOfSubpagesScrapper(): array
    {
        $urlDistrictsList = "/?bip_id=1&mmi=453";
        $httpCode = $this->getHttpCode($this::URLBIP . $urlDistrictsList);

        if ($httpCode !== 200) {
            throw new \Exception("District Links Site not available");
        } else {
            $html = file_get_contents($this::URLBIP . $urlDistrictsList);

            $crawler = new Crawler($html);
            $nodeValues = $crawler->filter(".left-nav .nav-link")->each(function (Crawler $node) {
                return [$node->text() => $node->extract(['href'])[0]];
            });

            $links = [];
            foreach ($nodeValues as $value) {
                if (str_starts_with(array_key_first($value), "Dzielnica ")) {
                    $links[] = reset($value);
                }
            }
            return $links;
        }
    }

    /**
     * @param string $districtLink
     * @return District
     * @throws \Exception
     */
    private function getDistrictsInfo(string $districtLink): District
    {
            $httpCode = $this->getHttpCode($this::URLBIP . $districtLink);
        if ($httpCode !== 200) {
            throw new \Exception("Error while getting data about " . $districtLink);
        } else {
            $html = file_get_contents($this::URLBIP . $districtLink);

            $crawler = new Crawler($html);
            $districtName = $crawler->filter("h1")->eq(0)->text();
            $nodeValues = $crawler->filter("#newsblock p")->each(function (Crawler $node) {
                return $node->text();
            });
            $districtArea = $nodeValues[1];
            $districtPopulation = $nodeValues[2];

            $districtName = explode(" ", $districtName, 3)[2];
            $districtArea = (float) str_replace(",", ".", explode(" ", $districtArea)[2]);
            $districtPopulation = explode(" ", $districtPopulation);
            $districtPopulation = (int) end($districtPopulation);

            $district = new District();
            $district->setName($districtName);
            $district->setPopulation($districtPopulation);
            $district->setArea($districtArea);
            $district->setCity("Kraków");

            return $district;
        }
    }

    private function getHttpCode($url): int
    {
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handle);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);
        return $httpCode;
    }
}
