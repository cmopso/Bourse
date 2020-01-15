<?php

namespace App\Http\Controllers;

use App\PriceShares;
use App\Share;
use App\Order;

use Illuminate\Http\Request;
use Carbon\Carbon;

class PriceSharesController extends Controller
{
    public static function loadPricesAPI() {

        $resultmessage = PriceSharesController::loadPrices(true);
        return response()->json(['message' => $resultmessage], 200);
    } 

    // load prices for all shares from last (or site start date) request to today's date
    public static function loadPrices($apicall = false) 
    {    
        $results = [];
        $siteStartDate = Carbon::today()->subYear(2); //Carbon::createFromFormat("!d/m/y", "01/12/19");
        $endDate = Carbon::today()->startOfDay();
        // ISO weekday from 1 (Monday) to 7 (Sunday) 
        // if today is the week end, we consider last friday 
        if ($endDate->isoWeekday() >5) {
            $endDate->subDay($endDate->isoWeekday() - 5);
        }
        $shares = Share::all()->groupBy("type")->sortBy('name');
        foreach(['share','indice','fund', 'tracker'] as $type) {
            if(isset($shares[$type])) {
                foreach ($shares[$type] as $share) {
                    $priceShareMaxDate = PriceShares::where('share_id', $share->id)->max('date');
                    if ($priceShareMaxDate) {
                        $startDate = Carbon::createFromTimeString($priceShareMaxDate)->addDay(1);
                    } else {
                        $startDate = $siteStartDate;
                    }
                    if ($endDate->greaterThanOrEqualTo($startDate)) {
                        $oneResults = PriceSharesController::getABCBourseData($share->codeISIN, $startDate, $endDate);
                        $results[$share->id]['name'] = $share->name;
                        $results[$share->id]['codeISIN'] = $share->codeISIN;
                        $results[$share->id]['end'] = $endDate->format("d/m/Y");
                        $results[$share->id]['start'] = $startDate->format("d/m/Y");
                        $results[$share->id]['imported'] = $oneResults['imported'];
                        $results[$share->id]['error'] = $oneResults['error'];
                    }
                }
            }
        }
        if ($apicall == true) 
            return $results;
        else
            return view('share.load', compact('results'));
    }

    public static function getABCBourseData($codeISIN, $start, $end)
    {
        $results = [];
        $results['imported'] = 0;
        $results['error'] = 0;
        
        $startString = $start->day . "%2F" . $start->month ."%2F" . $start->year ;
        $endString = $end->day . "%2F" . $end->month ."%2F" . $end->year ;

        $curl = curl_init();
   
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://www.abcbourse.com/download/historiques.aspx",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "__VIEWSTATE=%2FwEPDwUJMzc2NjkzOTI1D2QWAmYPZBYEAgEPZBYCZg8WAh4HVmlzaWJsZWdkAgUPZBYCAgIPFgIfAGdkGAEFHl9fQ29udHJvbHNSZXF1aXJlUG9zdEJhY2tLZXlfXxYpBRZjdGwwMCRCb2R5QUJDJGV1cm9saXN0BRxjdGwwMCRCb2R5QUJDJGFjdGlvbnNpbmRpY2VzBRpjdGwwMCRCb2R5QUJDJGFjdGlvbnNpbmR1cwUVY3RsMDAkQm9keUFCQyRjb21wbGV0BRtjdGwwMCRCb2R5QUJDJGNvbXBsZXRub3dhcnIFEmN0bDAwJEJvZHlBQkMkc3JkcAUYY3RsMDAkQm9keUFCQyRpbmRpY2VzbWtwBRljdGwwMCRCb2R5QUJDJGluZGljZXNzZWNwBRhjdGwwMCRCb2R5QUJDJGV1cm9saXN0YXAFGGN0bDAwJEJvZHlBQkMkZXVyb2xpc3RicAUYY3RsMDAkQm9keUFCQyRldXJvbGlzdGNwBRRjdGwwMCRCb2R5QUJDJGV1cm9ncAUUY3RsMDAkQm9keUFCQyRldXJvYXAFFGN0bDAwJEJvZHlBQkMkdHJhY2twBRNjdGwwMCRCb2R5QUJDJG9ibDJwBRJjdGwwMCRCb2R5QUJDJG9ibHAFFmN0bDAwJEJvZHlBQkMkd2FycmFudHMFF2N0bDAwJEJvZHlBQkMkb3Bjdm0zNjBwBRVjdGwwMCRCb2R5QUJDJHhjYWM0MHAFFmN0bDAwJEJvZHlBQkMkeHNiZjEyMHAFFWN0bDAwJEJvZHlBQkMkeGNhY2F0cAUWY3RsMDAkQm9keUFCQyR4Y2FjbjIwcAUYY3RsMDAkQm9keUFCQyR4Y2Fjc21hbGxwBRVjdGwwMCRCb2R5QUJDJHhjYWM2MHAFFmN0bDAwJEJvZHlBQkMkeGNhY2w2MHAFFWN0bDAwJEJvZHlBQkMkeGNhY21zcAUVY3RsMDAkQm9keUFCQyR4YmVsMjBnBRVjdGwwMCRCb2R5QUJDJHhhZXgyNW4FEWN0bDAwJEJvZHlBQkMkZGp1BRJjdGwwMCRCb2R5QUJDJG5hc3UFFGN0bDAwJEJvZHlBQkMkc3A1MDB1BRZjdGwwMCRCb2R5QUJDJGdlcm1hbnlmBRJjdGwwMCRCb2R5QUJDJHVzYXUFEWN0bDAwJEJvZHlBQkMkdWtlBRJjdGwwMCRCb2R5QUJDJGJlbGcFE2N0bDAwJEJvZHlBQkMkaG9sbG4FFWN0bDAwJEJvZHlBQkMkaXRhbGlhaQUVY3RsMDAkQm9keUFCQyRsaXNib2FsBRJjdGwwMCRCb2R5QUJDJGRldnAFFWN0bDAwJEJvZHlBQkMkb25lU2ljbwUTY3RsMDAkQm9keUFCQyRjYlllczrRq73kjFvAHnV8BmqSZ6NlQLuP&__VIEWSTATEGENERATOR=0EFFD687&__EVENTVALIDATION=%2FwEdADagtKeJUKvjdIu0E9H5iROfE4nQ3ZZKaNp1PLaBt422ojt20EeIGI5psEuMuMm7J99ysS2ch4X%2BDI3v5dlsbGTvKG6Mpl%2FCv2sH6QGHawEZG6IvMtA9WimvGEQ27mZPYQlQfpBN4GWLYqVM3t48zQzQjBARDFhRDnPjRxG0m8PBbzRWHV8gFLsbevtI22XOTcJtRg9EKKE5vey1FPRlCqZyDgvrv9lvrlshrlzdNlGwoiScZo6eZpFvA3jP6HCOVvFpr1zAysJmK7wSgu6Z1lALVnKTKFbEpjgjnZ5VyHKF7hs2GHULoUz7A1s0kmfr7E4FobglHvNHUGGj3kfcAJCxvkYLOMZtGPFq8wOue%2BL3aBK7Gzh9U1V6eP1sHA0y133ZehnR%2BAAXHRVhGmBS5vHpoZmrulbiQC7mbx9vlyLTOCaFj1P1ns1k7saP20CtOv7yE4oeSMQcqAToKX4%2BVY%2FoKwHPZ3LL3fdWqV0S%2FvWmetYHl%2BXtIMfr4sJ5HoKPeEGaXWKkENsUVjCs33ftb%2Bk6Vh68XGlO5A7hLzsl2zmozVHKtnVHMqNjuSl%2FVTLUSxGOrSXMajdQMItHxDOD4gI5oZA%2FrQy55rsm3Yy%2BuTl0%2FnRrfHed0TzZAp%2F%2By2dFmxusO8axFlSjvdrqSAJF9oAESNvpV6G124LKs01uIQT%2BzPLtwgDb4ZnV8AzgWlnJDQlBhudEBAhKHZIsMbDqQKObxt6eBSEoHlSQ0h6eQsjG3PQLfrpgs2Etrj6F6v3VSQdVNRGPAtf9BhmQxdFVjtqGE3LKYP0CSBKO8s%2BAkdN%2B2qCyv8bmJMXg1Cdo8hAobgkSMkbr0ssfhDNp48vrt1aTxk0DNvk6fiscQ%2F0fLCCFgbYiFYBGCMxIZG%2FSpWGZsnu0oBlATBk5dHluQD%2BsCPAfyBUKx3EKxejXS0cGHce94AWGWPWBnGJFzzuQT%2BRpcXOg4BwUf2y4rgDoIClK6HFIQ24GiJO%2BIQ3OMlnjaVX9YpZV32pUuCD%2F%2FLG58s7l7wBvI8R%2BQihN5Aw48W4LvXc3PG4w7HJzwL19dAmqRoCii1%2F27p2srBHkgTINJPeQ%2F6%2Fp81%2BkE7Z2rpWliSuX9l6PQze7UQLbQ7Ri0FTVMSOPzRrqfXKoG%2FeBbQYV684ncHUl3SAjaBt2uRFLUwRA%2Bpn0TyF6dg%3D%3D&ctl00%24BodyABC%24strDateDeb=" . $startString . "&ctl00%24BodyABC%24strDateFin=". $endString . "&ctl00%24BodyABC%24oneSico=on&ctl00%24BodyABC%24txtOneSico=". $codeISIN ."&ctl00%24BodyABC%24dlFormat=w&ctl00%24BodyABC%24listFormat=isin&ctl00%24BodyABC%24Button1=T%C3%A9l%C3%A9charger",
          CURLOPT_HTTPHEADER => array(
            //"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            //"Accept-Encoding: gzip, deflate, br",
            //"Cache-Control: no-cache",
            //"Content-Length: 3015",
            //"Content-Type: application/x-www-form-urlencoded",
            //"Cookie: ASP.NET_SessionId=qdbuuvwygqwrv5uyqtcjepl2",
            //"Host: www.abcbourse.com",
            //"Postman-Token: ca4595c3-4e57-4156-b1da-1478f7ccd82d,c3a22040-0f98-4644-a0be-db39db197975",
            //"User-Agent: PostmanRuntime/7.20.1",
            //"cache-control: no-cache"
          ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err) {
          dd("cURL Error #:" . $err);
        } else {
            $lines = explode(PHP_EOL, $response);
            $array = array();
            foreach ($lines as $line) {
                if ($line <> "") {
                    $oneResult = PriceSharesController::addSharePrice(str_getcsv($line,";"));
                    $results['imported'] = $results['imported'] + $oneResult['imported'];
                    $results['error'] = $results['error'] + $oneResult['error'];
                }
            }
        }
        return $results;
    }

    public static function addSharePrice($dataArray) 
    {
        $results = [];
        $results['imported'] = 0;
        $results['error'] = 0;

        if (count($dataArray) <> 7) {
            $results['error'] = 1;
        } else {
            $codeISIN   = $dataArray[0];
            $validatedData['date'] = Carbon::createFromFormat("!d/m/y", $dataArray[1]);
            $validatedData['open'] = $dataArray[2];
            $validatedData['highest'] = $dataArray[3];
            $validatedData['lowest'] = $dataArray[4];
            $validatedData['close'] = $dataArray[5];
            $validatedData['volume'] = $dataArray[6];

            $share = Share::where('codeISIN', $codeISIN)->first();
            if ($share) {
                $validatedData['share_id'] = $share->id;
                $priceShare = PriceShares::where('share_id', $share->id)
                ->where('date', $validatedData['date'])
                ->first();
                if (!$priceShare) {
                    $pricsShare = PriceShares::create($validatedData);
                    $results['imported'] = 1;
                } 
            } else {
                dd("error : codeISIN inconnu " . $codeISIN);
            } 
        } 
        return $results; 
    }
    
    // return an array of arrays 
    // [price] = share prices index is YYYY-MM-DD value is price
    // [min] = min for each month
    // [max] = max for each month
    // [buy] & [sale]
    // [cac]
    // [same]
    // [color]
    // [history]
    public static function getAllPrices($share) 
    {
        $priceShareData = [];
        $shareColor = [
            "#007bff", "#003D80", "#003D80","#FF8400","#CC5100",
            "#000000", 
            "#007BFF", "#0400FF","#8400FF","#3f51b5","#FF00FB", "#FF007B","#FF0400",
            "#009688","#4caf50","#8bc34a","#cddc39",
            "#ffeb3b", "#ffc107", "#ff9800","#ff5722","#795548","#9e9e9e",
            "#607d8b",
            "#EBDEF0","#D4E6F1","#D1F2EB","#FCF3CF","#EDBB99",
            "#C0392B", "#9B59B6", "#2980B9","#1ABC9C","#F1C40F","#E67E22",
            "#D98880","#EBDEF0","#D4E6F1","#D1F2EB","#FCF3CF","#EDBB99",
            ];
        $indexShareColor = 0;

        // get share data prices, min, max, buy and sales
        $prices = PriceShares::where("share_id", $share->id)->orderBy("date")->get();
        if ($prices->count() == 0) return $priceShareData;
        
        $day = $prices[0]->date->day;
        $month = $prices[0]->date->month;
        $year = $prices[0]->date->year;
        $minMonth = $prices[0]->close;
        $maxMonth = $prices[0]->close;
        
        foreach ($prices as $key => $price) {
            if  ($month == $price->date->month) {
                // still the samme month, we update min & max
                if ($minMonth > $price->close)  $minMonth =  $price->close;
                if ($maxMonth < $price->close)  $maxMonth =  $price->close;
                $day = $price->date->day;
            } else {
                // we change of month, we initialize min & max + month and save value to the array
                $priceShareData["min"][$year . "-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-" . str_pad($day, 2, "0", STR_PAD_LEFT)] = $minMonth;
                $priceShareData["max"][$year . "-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-" . str_pad($day, 2, "0", STR_PAD_LEFT)] = $maxMonth;

                $minMonth = $price->close;
                $maxMonth = $price->close;
                $day = $price->date->day;
                $month = $price->date->month;
                $year = $price->date->year;
            }
            $priceShareData["price"][$price->date->toDateString()] = $price->close;
        }
        // we save the last month
        $priceShareData["min"][$year . "-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-" . str_pad($day, 2, "0", STR_PAD_LEFT)] = $minMonth;
        $priceShareData["max"][$year . "-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-" . str_pad($day, 2, "0", STR_PAD_LEFT)] = $maxMonth;

        $orders = $share->orders;
        foreach ($orders as $order) {
            $year = $order->passedOn->year;
            $month = $order->passedOn->month;
            $day = $order->passedOn->day;
            $priceShareData[$order->type][$year . "-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-" . str_pad($day, 2, "0", STR_PAD_LEFT)] = $order->price;
        }
        
        // create history data
        $lastKey = array_key_last($priceShareData["price"]);
        $today = carbon::today();

        foreach(['1er Janvier' => 0, '1 semaine' => 5, '1 mois' => 30, '3 mois' => 91, '6 mois' => 182, '1 an' => 365] as $key => $gap) {
            $lastDate = Carbon::createFromFormat('Y-m-d', $lastKey);
            $lastPrice = $priceShareData["price"][$lastKey];
            
            if ($gap == 0) 
                $nextDate = Carbon::createFromFormat('Y-m-d', $lastDate->year - 1 . "-12-31");
            else   
                $nextDate = $lastDate->subDay($gap);

            if ($nextDate->isWeekend()) $nextDate = $nextDate->previousWeekday();
            
            if (isset($priceShareData["price"][$nextDate->year . "-" . str_pad($nextDate->month, 2, "0", STR_PAD_LEFT) . "-" . str_pad($nextDate->day, 2, "0", STR_PAD_LEFT)]))
                $newPrice = $priceShareData["price"][$nextDate->year . "-" . str_pad($nextDate->month, 2, "0", STR_PAD_LEFT) . "-" . str_pad($nextDate->day, 2, "0", STR_PAD_LEFT)];
            else
                $newPrice = 0;
            if ($newPrice != 0) {
                $priceShareData["history"]["value"][$key] = $newPrice;
                $priceShareData["history"]["percent"][$key] = ($lastPrice - $newPrice) / $newPrice * 100;
            } else  {
                $priceShareData["history"]["value"][$key] = "-";
                $priceShareData["history"]["percent"][$key] = "-";
            }
        }
        $lastPrice = end($priceShareData["price"]);
        $previousPrice = prev($priceShareData["price"]);
        $priceShareData["history"]["value"]["Veille"] = $previousPrice;
        $priceShareData["history"]["percent"]["Veille"] = ($lastPrice - $previousPrice) / $previousPrice * 100; 
        //dd($previousPrice);
        //dd($priceShareData["price"]);

        // get CAC 40
        $indice = Share::where("codeISIN", "FR0003500008")->first();
        $indicePrices = PriceShares::where("share_id", $indice->id)->orderBy("date")->get();
        foreach ($indicePrices as $indicePrice) {
            $priceShareData["cac"][$indicePrice->date->toDateString()] = $indicePrice->close;
        }

        // we add color
        $priceShareData["color"]['prices'] = $shareColor[0];
        $priceShareData["color"]['min'] = $shareColor[1];
        $priceShareData["color"]['max'] = $shareColor[2];
        $priceShareData["color"]['Achat'] = $shareColor[3];
        $priceShareData["color"]['Vente'] = $shareColor[4];
        $priceShareData["color"]['cac'] = $shareColor[5];
        $indexShareColor = 6;

        // get all shares of the same type for comparison
        $priceShareData["index"] = [];
        $sameShares = Share::where("type", $share->type)->orderBy("name")->get();
        foreach ($sameShares as $sameShare) {
            array_push($priceShareData["index"],$sameShare->id);
            $priceShareData["name"][$sameShare->id] = $sameShare->name;
            $priceShareData["color"][$sameShare->id] = $shareColor[$indexShareColor];
            $indexShareColor++;
            $sameSharePrices = PriceShares::where("share_id", $sameShare->id)->orderBy("date")->get();
            foreach ($sameSharePrices as $sameSharePrice) {
                $priceShareData["same"][$sameShare->id][$sameSharePrice->date->toDateString()] = $sameSharePrice->close;
            }
        }

        $currentShareIndex = array_search($share->id, $priceShareData["index"]);
        if($currentShareIndex == 0) {
            // first sahre in the list
            $priceShareData["previous"] = $priceShareData["index"][count($priceShareData["index"])-1];
            $priceShareData["next"] = $priceShareData["index"][$currentShareIndex+1];
        } elseif ($currentShareIndex == count($priceShareData["index"])-1) {
            // last share in the list
            $priceShareData["previous"] = $priceShareData["index"][$currentShareIndex-1];
            $priceShareData["next"] = $priceShareData["index"][0];
        } else {
            $priceShareData["previous"] = $priceShareData["index"][$currentShareIndex-1];
            $priceShareData["next"] = $priceShareData["index"][$currentShareIndex+1];
        }
    
        //dd($priceShareData);
        return $priceShareData;
    }


    public static function loadFinancialData() 
    {        
        $results = [];
        
        $shares = Share::all()->groupBy("type")->sortBy('name');
        foreach(['share','indice','fund', 'tracker'] as $type) {
            if(isset($shares[$type])) {
                foreach ($shares[$type] as $share) {
                    $oneResults = PriceSharesController::getYahooFinanceData($share);
                    
                    $results[$share->id]['name'] = $share->name;
                    $results[$share->id]['codeISIN'] = $share->codeISIN;
                    $results[$share->id]['end'] = ""; 
                    $results[$share->id]['start'] = "";
                    $results[$share->id]['imported'] = $oneResults['imported'];
                    $results[$share->id]['error'] = $oneResults['error'];
                }
            }
        }
        return view('share.load', compact('results'));


    }

    public static function getYahooFinanceData($share)
    {
        $results = [];
        $results['imported'] = 0;
        $results['error'] = 0;
        
        if ($share->code != "") {
    
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://apidojo-yahoo-finance-v1.p.rapidapi.com/stock/get-detail?region=FR&lang=fr&symbol=". $share->code,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "x-rapidapi-host: apidojo-yahoo-finance-v1.p.rapidapi.com",
                    "x-rapidapi-key: 4dd320036emsh20e4b406d3c1a54p1a69c5jsna0b109ee9c32"
                ),
            ));
            
            $response = curl_exec($curl);
            $err = curl_error($curl);
            
            curl_close($curl);
            
            if ($err) {
                dd("cURL Error #:" . $err);
                $results['error'] = 1;
            } else {
                $reponseDecode = json_decode($response);
                //dd($reponseDecode);

                $share->dividendDate = $reponseDecode->calendarEvents->exDividendDate->fmt;
                $share->dividendValue = $reponseDecode->summaryDetail->dividendRate->raw;
                $share->fiveYearsAvgDividendYield = $reponseDecode->summaryDetail->fiveYearAvgDividendYield->raw;
                $share->yield = 100 * $reponseDecode->summaryDetail->dividendRate->raw / $reponseDecode->summaryDetail->previousClose->raw;
                $share->save();
                $results['imported'] = 1;
            }
        }
        return $results;
    }
}
