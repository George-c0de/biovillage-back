<?php

namespace App\Service;

use App\Helpers\DbHelper;
use App\Helpers\Utils;
use App\Models\CatalogSection;
use App\Models\DeliveryIntervalModel;
use App\Models\TagModel;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Storage;

/**
 *
 * Parse KML Map by Google
 *
 * @package App\Service\DeliveryIntervalService
 */
class KmlMapService extends BaseService
{
    /**
     * Find district colors
     * @param $xml
     * @param $placeTag
     * @return string or null
     */
    private function getDistrictColor($xml, $placeTag) {

        try {
            $styleId = (string) $placeTag->styleUrl;
            if(empty($styleId)) {
                return Utils::raise('No style url');
            }
        }
        catch (\Exception $e) {
            return null;
        }

        $styles = $xml->xpath(sprintf(
            "(//di:Document/di:Style[@id='%s-normal']/di:LineStyle)[1]",
            ltrim($styleId, '#')
        ));

        if(empty($styles) or count($styles) != 1) {
            return null;
        }

        $color = (string)$styles[0]->color;
        if(strlen($color) == 8) {
            $color = substr($color, 2);
        }
        return '#' . $color;
    }

    /**
     * Parse zone coordinates
     * @param $tag
     * @return array of array - Coordinates of
     *  polygon vertex Lat,Lon;... NULL else
     */
    private function getDestrictCoords($tag) {
        try {
            $coordStr = (string)$tag->Polygon->outerBoundaryIs->LinearRing->coordinates;
        }
        catch (\Exception $e) {
            return null;
        }
        $vertexes = [];
        foreach(explode( "\n", $coordStr) as $vStr) {
            $vertex = explode(",", $vStr);
            if(count($vertex) < 2 ) {
                continue;
            }
            // lon, lat -> lat, lon
            $vertexes[] = [ floatval($vertex[1]), floatval($vertex[0]) ];
        }
        return $vertexes;
    }

    /**
     * Parse KML file and extract name, color and coordinates of delivery areas
     * @param $filename
     * @return array
     * @throws \Exception
     */
    public function parseKmlFile($filename) {
        if(!file_exists($filename)) {
            Utils::raise('KML File not exists');
        }

        $xml = simplexml_load_file($filename);
        if(!$xml) {
            Utils::raise('KML File not loaded');
        }

        if($xml->getName() != 'kml'){
            Utils::raise('Wrong main tag');
        }

        // Register prefix
        foreach($xml->getDocNamespaces() as $pref => $ns ) {
            if(strlen($pref)==0) {
                $pref = "di";
            }
            $xml->registerXPathNamespace($pref,$ns);
        }

        /**
         * Districts of delivery
         * Structure
         *  name
         *  color
         */
        $districts = [];

        $districtTags = $xml->xpath('//di:Document/di:Folder/di:Placemark');
        foreach($districtTags as $tag) {

            $name = rtrim((string)$tag->name);
            $coords = $this->getDestrictCoords($tag);
            $color = $this->getDistrictColor($xml, $tag);
            if(empty($name) or empty($coords) or empty($color)) {
                continue;
            }
            $districts[] = [
                'name' => $name,
                'color' => $color,
                'polygon' => $coords
            ];
        }

        return $districts;
    }

}
