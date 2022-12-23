<?php

namespace App\Http\Controllers;

use App\Models\RetsPropertyData;
use App\Models\SqlModel\BlogModel;
use App\Models\SqlModel\Pages;
use App\Models\SqlModel\RetsPropertyDataPurged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class GenerateXMLFile extends Controller
{
    //
    public function index() {
        $data["pageTitle"] = "XML Files";
        $data['sitemaplinks'] = [
            [
                "name" => "Blogs Site map",
                "links" => env('WEDUURL') . "blog-sitemap.xml",
                "generateUrl" => env('APP_URL') . "agent/sitemap/generateBlogXml"
            ], [
                "name" => "City Site map",
                "links" => env('WEDUURL') . "city-sitemap.xml",
                "generateUrl" => env('APP_URL') . "agent/sitemap/generateCityXml"
            ], [
                "name" => "Site map",
                "links" => env('WEDUURL') . "sitemap.xml",
                "generateUrl" => env('APP_URL') . "agent/sitemap/generateSitemapXml"
            ], [
                "name" => "Property Site map",
                "links" => env('WEDUURL') . "property-sitemap1.xml",
                "generateUrl" => env('APP_URL') . "agent/sitemap/generatePropertyXml"
            ],[
                "name" => "Pages Site map",
                "links" => env('WEDUURL') . "pages-sitemap.xml",
                "generateUrl" => env('APP_URL') . "agent/sitemap/generatePageXml"
            ],[
                "name" => "Sold Properties map",
                "links" => env('WEDUURL') . "sold-property-sitemap1.xml",
                "generateUrl" => env('APP_URL') . "agent/sitemap/generateSoldListingXml"
            ]
        ];
        return view('agent.sitemap.index',$data);
    }

    /*public function generatePropertyXml() {
        $file = fopen(env('FRONTENDFILEPATHFORSITEMAP')."property-sitemap.xml","w");
        $xml_txt =  '<?xml version="1.0" encoding="UTF-8"?>';
        $xml_txt .= '<urlset
        xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
        $data['propertydata'] = RetsPropertyData::select("id","SlugUrl")->get();
        $lastmod =   date("Y-m-d h:i:s");
        $datetime = new \DateTime($lastmod);
        $result = $datetime->format('Y-m-d\TH:i:sP');
        foreach ($data['propertydata'] as $propertydata) {
            $propertyDetailsLink = env('WEDUURL')."propertydetails/";
            $xml_txt .= '
                        <url>
                          <loc>'.$propertyDetailsLink.strip_tags(preg_replace("/&(?!#?[a-z0-9]+;)/", "&amp;",$propertydata["SlugUrl"])).'</loc>
                          <changefreq>daily</changefreq>
                          <lastmod>'.$result.'</lastmod>
                          <priority>1.00</priority>
                        </url>
               ';
        }
        $xml_txt .= '</urlset>';
        echo fwrite($file,$xml_txt);
        fclose($file);
        return \redirect('agent/sitemap/')->with('success-sitemap', 'Thanks XML has Generated Successfully.');
    }*/
    public function generateSitemapXml() {
        /*$file = fopen(env('FRONTENDFILEPATHFORSITEMAP')."sitemap.xml","w");
        $xml_txt =  '<?xml version="1.0" encoding="UTF-8"?>';
        $xml_txt .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $xml_txt .= '
                <sitemap>
                  <loc>'.env('WEDUURL').'blog-sitemap.xml</loc>
                </sitemap>
                <sitemap>
                  <loc>'.env('WEDUURL').'city-sitemap.xml</loc>
                </sitemap>
                <sitemap>
                  <loc>'.env('WEDUURL').'property-sitemap.xml</loc>
                </sitemap>
		<sitemap>
		  <loc>'.env('WEDUURL').'pages-sitemap.xml</loc>
                </sitemap>
    ';
        $xml_txt .= '</sitemapindex>';
        echo fwrite($file,$xml_txt);
        fclose($file);*/
        $this->updateSitemapXml("");
        return \redirect('agent/sitemap/')->with('success-sitemap', 'Thanks XML has Generated Successfully.');
    }
    public function generateBlogXml() {
        $file = fopen(env('FRONTENDFILEPATHFORSITEMAP')."blog-sitemap.xml","w");
        $xml_txt =  '<?xml version="1.0" encoding="UTF-8"?>';
        $xml_txt .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $data['blogdata'] = BlogModel::select("id","Url")->get();
        $lastmod =   date("Y-m-d h:i:s");
        $datetime = new \DateTime($lastmod);
        $result = $datetime->format('Y-m-d\TH:i:sP');
        foreach ($data['blogdata'] as $propertydata) {
            $propertyDetailsLink = env('WEDUURL')."blogs/";
            $xml_txt .= '
                        <url>
                          <loc>'.$propertyDetailsLink.strip_tags(preg_replace("/&(?!#?[a-z0-9]+;)/", "&amp;",$propertydata["Url"])).'</loc>
                          <changefreq>daily</changefreq>
                          <lastmod>'.$result.'</lastmod>
                          <priority>1.0</priority>
                        </url>
               ';
        }
        $xml_txt .= '</urlset>';
        echo fwrite($file,$xml_txt);
        fclose($file);
        return \redirect('agent/sitemap/')->with('success-sitemap', 'Thanks XML has Generated Successfully.');
    }
    public function generateCityXml() {
        $file = fopen(env('FRONTENDFILEPATHFORSITEMAP')."city-sitemap.xml","w");
        $xml_txt =  '<?xml version="1.0" encoding="UTF-8"?>';
        $xml_txt .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $data['citydata'] = RetsPropertyData::select("id","City")->groupBy('City')->where("City","!=","")->get();
        $lastmod =   date("Y-m-d h:i:s");
        $datetime = new \DateTime($lastmod);
        $result = $datetime->format('Y-m-d\TH:i:sP');
        foreach ($data['citydata'] as $propertydata) {
            $propertyDetailsLink = env('WEDUURL')."city/";
            $xml_txt .= '
                        <url>
                          <loc>'.$propertyDetailsLink.strip_tags(preg_replace("/&(?!#?[a-z0-9]+;)/", "&amp;",$propertydata["City"])).'</loc>
                          <changefreq>daily</changefreq>
                          <lastmod>'.$result.'</lastmod>
                          <priority>1.0</priority>
                        </url>
               ';
        }
        $xml_txt .= '</urlset>';
        echo fwrite($file,$xml_txt);
        fclose($file);
        return \redirect('agent/sitemap/')->with('success-sitemap', 'Thanks XML has Generated Successfully.');
    }
    public function generatePageXml() {
        $file = fopen(env('FRONTENDFILEPATHFORSITEMAP')."pages-sitemap.xml","w");
        $xml_txt =  '<?xml version="1.0" encoding="UTF-8"?>';
        $xml_txt .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $data['pagesdata'] = Pages::select("PageUrl")->get();
        $lastmod =   date("Y-m-d h:i:s");
        $datetime = new \DateTime($lastmod);
        $result = $datetime->format('Y-m-d\TH:i:sP');
        foreach ($data['pagesdata'] as $propertydata) {
            $propertyDetailsLink = env('WEDUURL');
            $xml_txt .= '
                        <url>
                          <loc>'.$propertyDetailsLink.strip_tags(preg_replace("/&(?!#?[a-z0-9]+;)/", "&amp;",str_replace("/","",$propertydata["PageUrl"]))).'</loc>
                          <changefreq>daily</changefreq>
                          <lastmod>'.$result.'</lastmod>
                          <priority>1.0</priority>
                        </url>
               ';
        }
        $xml_txt .= '</urlset>';
        echo fwrite($file,$xml_txt);
        fclose($file);
        //echo $xml_txt;
        return \redirect('agent/sitemap/')->with('success-sitemap', 'Thanks XML has Generated Successfully.');
    }


    public function generatePropertyXml()
    {
        $countActiveProperties = RetsPropertyData::count();
        $limit = env('ACTIVESITEMAPLIMIT');
        $counter = (int)ceil($countActiveProperties / $limit);
        $activeXmlTxt = '';
        for ($i = 0; $i < $counter; $i++) {
            echo "<br />";
            $offset = $limit * $i;
            echo "offset = " . $i . " limit = " . $limit;
            $file_number = $i;
            $file_number = $file_number + 1;
            echo "fileName = " . $file_number;
            //continue;
            $file = fopen(env('FRONTENDFILEPATHFORSITEMAP') . "property-sitemap" . $file_number . ".xml", "w");
            $xml_txt = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml_txt .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            echo "<br/> up offset = " . $i . " limit = " . $limit;
            $data['propertydata'] = DB::select("select id,SlugUrl,ListPrice from RetsPropertyData where SlugUrl <> ''  limit " . $limit . " offset " . $offset);
            $lastmod = date("Y-m-d");
            //$datetime = new \DateTime($lastmod);
            //$result = $datetime->format('Y-m-d\TH:i:sP');
            $result = $lastmod;
            foreach ($data['propertydata'] as $propertydata) {
                $propertydata = collect($propertydata)->all();
                $propertyDetailsLink = env('WEDUURL') . "propertydetails/";
                $Price = $propertydata["ListPrice"];
                $xml_txt .= '
                        <url>
			 <loc>' . $propertyDetailsLink . strip_tags(preg_replace("/&(?!#?[a-z0-9]+;)/", "&amp;", $propertydata["SlugUrl"])) . '</loc>
                          <changefreq>daily</changefreq>
                          <lastmod>' . $result . '</lastmod>
                           <priority>1.0</priority>
                        </url>
               ';
            }
            $xml_txt .= '</urlset>';
            $activeXmlTxt .= '<sitemap>
                  <loc>' . env('WEDUURL') . 'property-sitemap' . $file_number . '.xml</loc>
                </sitemap>';
            fwrite($file, $xml_txt);
            fclose($file);
        }
        $this->updateSitemapXml($activeXmlTxt);
        return \redirect('agent/sitemap/')->with('success-sitemap', 'Thanks XML has Generated Successfully.');
    }

    public function generateSoldListingXml()
    {
        $countSoldProperties = RetsPropertyDataPurged::count();
        $limit = env('SOLDSITEMAPLIMIT');
        $counter = (int)ceil($countSoldProperties / $limit);
        $soldXmlTxt = '';
        for ($i = 0; $i < $counter; $i++) {
            echo "<br />";
            $offset = $limit * $i;
            echo "offset = " . $i . " limit = " . $limit;
            $file_number = $i;
            $file_number = $file_number + 1;
            echo "fileName = " . $file_number;
            //continue;
            $file = fopen(env('FRONTENDFILEPATHFORSITEMAP') . "sold-property-sitemap" . $file_number . ".xml", "w");
            $xml_txt = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml_txt .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            echo "<br/> up offset = " . $i . " limit = " . $limit;
            $data['propertydata'] = DB::select("select id,SlugUrl from RetsPropertyDataPurged where SlugUrl <> ''  limit " . $limit . " offset " . $offset);
            $lastmod = date("Y-m-d");
            //$datetime = new \DateTime($lastmod);
            //$result = $datetime->format('Y-m-d\TH:i:sP');
            $result = $lastmod;
            foreach ($data['propertydata'] as $propertydata) {
                $propertydata = collect($propertydata)->all();
                $propertyDetailsLink = env('WEDUURL') . "propertydetails/";
                $xml_txt .= '
                        <url>
                         <loc>' . $propertyDetailsLink . strip_tags(preg_replace("/&(?!#?[a-z0-9]+;)/", "&amp;", $propertydata["SlugUrl"])) . '</loc>
			 <changefreq>daily</changefreq>
                          <lastmod>' . $result . '</lastmod>
                          <priority>1.0</priority>
                        </url>
               ';
            }
            $xml_txt .= '</urlset>';
            $soldXmlTxt .= '<sitemap>
                  <loc>' . env('WEDUURL') . 'sold-property-sitemap' . $file_number . '.xml</loc>
                </sitemap>';
            fwrite($file, $xml_txt);
            fclose($file);
        }
        $this->updateSitemapXml($soldXmlTxt);
        return \redirect('agent/sitemap/')->with('success-sitemap', 'Thanks XML has Generated Successfully.');
    }
    public function updateSitemapXml($xml) {
        $countSoldProperties = RetsPropertyDataPurged::count();
        $limit = env('SOLDSITEMAPLIMIT');
        $soldXmlTxt = '';
        $soldCounter = (int)ceil($countSoldProperties / $limit);
        $lastmod = date("Y-m-d");
        //$datetime = new \DateTime($lastmod);
        //$result = $datetime->format('Y-m-d\TH:i:sP');
        $result = $lastmod;
        for ($i = 0; $i < $soldCounter; $i++) {
            $file_number = $i;
            $file_number = $file_number + 1;
            $soldXmlTxt .= '<sitemap>
                  <loc>' . env('WEDUURL') . 'sold-property-sitemap' . $file_number . '.xml</loc>
                  <lastmod>' . $result . '</lastmod>
                </sitemap>';
        }
        $activeXmlTxt = '';
        $countActiveProperties = RetsPropertyData::count();
        $limit = env('ACTIVESITEMAPLIMIT');
        $activeCounter = (int)ceil($countActiveProperties / $limit);
        for ($j = 0; $j < $activeCounter; $j++) {
            $file_number = $j;
            $file_number = $file_number + 1;
            $activeXmlTxt .= '<sitemap>
                  <loc>' . env('WEDUURL') . 'property-sitemap' . $file_number . '.xml</loc>
                  <lastmod>' . $result . '</lastmod>
                </sitemap>';
        }
        $file = fopen(env('FRONTENDFILEPATHFORSITEMAP')."sitemap.xml","w");
        $xml_txt =  '<?xml version="1.0" encoding="UTF-8"?>';
        $xml_txt .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $xml_txt .= '
                <sitemap>
                  <loc>'.env('WEDUURL').'blog-sitemap.xml</loc>
                  <lastmod>' . $result . '</lastmod>
                </sitemap>
                <sitemap>
                  <loc>'.env('WEDUURL').'city-sitemap.xml</loc>
                  <lastmod>' . $result . '</lastmod>
                </sitemap>
		<sitemap>
		  <loc>'.env('WEDUURL').'pages-sitemap.xml</loc>
		  <lastmod>' . $result . '</lastmod>
                </sitemap>
    ';
        $xml_txt .= $activeXmlTxt;
        $xml_txt .= $soldXmlTxt;
        $xml_txt .= '</sitemapindex>';
        echo fwrite($file,$xml_txt);
        fclose($file);
        return \redirect('agent/sitemap/')->with('success-sitemap', 'Thanks XML has Generated Successfully.');
    }
}
