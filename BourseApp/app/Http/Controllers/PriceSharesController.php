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
        foreach(['share', 'option', 'indice','fund', 'tracker'] as $type) {
            if(isset($shares[$type])) {
                foreach ($shares[$type] as $share) {
                    $priceShareMaxDate = PriceShares::where('share_id', $share->id)->max('date');
                    if ($priceShareMaxDate) {
                        $startDate = Carbon::createFromTimeString($priceShareMaxDate)->addDay(1)->startOfDay();
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
          CURLOPT_URL => "https://www.abcbourse.com/download/historiques",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "__VIEWSTATE=/wEPDwUKLTYyNTE5ODYxNWQYAQUeX19Db250cm9sc1JlcXVpcmVQb3N0QmFja0tleV9fFikFFmN0bDAwJEJvZHlBQkMkZXVyb2xpc3QFHGN0bDAwJEJvZHlBQkMkYWN0aW9uc2luZGljZXMFGmN0bDAwJEJvZHlBQkMkYWN0aW9uc2luZHVzBRVjdGwwMCRCb2R5QUJDJGNvbXBsZXQFG2N0bDAwJEJvZHlBQkMkY29tcGxldG5vd2FycgUSY3RsMDAkQm9keUFCQyRzcmRwBRhjdGwwMCRCb2R5QUJDJGluZGljZXNta3AFGWN0bDAwJEJvZHlBQkMkaW5kaWNlc3NlY3AFGGN0bDAwJEJvZHlBQkMkZXVyb2xpc3RhcAUYY3RsMDAkQm9keUFCQyRldXJvbGlzdGJwBRhjdGwwMCRCb2R5QUJDJGV1cm9saXN0Y3AFFGN0bDAwJEJvZHlBQkMkZXVyb2dwBRRjdGwwMCRCb2R5QUJDJGV1cm9hcAUUY3RsMDAkQm9keUFCQyR0cmFja3AFE2N0bDAwJEJvZHlBQkMkb2JsMnAFEmN0bDAwJEJvZHlBQkMkb2JscAUWY3RsMDAkQm9keUFCQyR3YXJyYW50cwUXY3RsMDAkQm9keUFCQyRvcGN2bTM2MHAFFWN0bDAwJEJvZHlBQkMkeGNhYzQwcAUWY3RsMDAkQm9keUFCQyR4c2JmMTIwcAUVY3RsMDAkQm9keUFCQyR4Y2FjYXRwBRZjdGwwMCRCb2R5QUJDJHhjYWNuMjBwBRhjdGwwMCRCb2R5QUJDJHhjYWNzbWFsbHAFFWN0bDAwJEJvZHlBQkMkeGNhYzYwcAUWY3RsMDAkQm9keUFCQyR4Y2FjbDYwcAUVY3RsMDAkQm9keUFCQyR4Y2FjbXNwBRVjdGwwMCRCb2R5QUJDJHhiZWwyMGcFFWN0bDAwJEJvZHlBQkMkeGFleDI1bgURY3RsMDAkQm9keUFCQyRkanUFEmN0bDAwJEJvZHlBQkMkbmFzdQUUY3RsMDAkQm9keUFCQyRzcDUwMHUFFmN0bDAwJEJvZHlBQkMkZ2VybWFueWYFEmN0bDAwJEJvZHlBQkMkdXNhdQURY3RsMDAkQm9keUFCQyR1a2UFEmN0bDAwJEJvZHlBQkMkYmVsZwUTY3RsMDAkQm9keUFCQyRob2xsbgUVY3RsMDAkQm9keUFCQyRpdGFsaWFpBRVjdGwwMCRCb2R5QUJDJGxpc2JvYWwFEmN0bDAwJEJvZHlBQkMkZGV2cAUVY3RsMDAkQm9keUFCQyRvbmVTaWNvBRNjdGwwMCRCb2R5QUJDJGNiWWVzQBUbankDQfDPut9hLeUwmgZ+ndI%3D&__VIEWSTATEGENERATOR=0EFFD687&__EVENTVALIDATION=/wEdADZY/lQ1VbAQaRan4E3F8m9hE4nQ3ZZKaNp1PLaBt422ojt20EeIGI5psEuMuMm7J99ysS2ch4X+DI3v5dlsbGTvKG6Mpl/Cv2sH6QGHawEZG6IvMtA9WimvGEQ27mZPYQlQfpBN4GWLYqVM3t48zQzQjBARDFhRDnPjRxG0m8PBbzRWHV8gFLsbevtI22XOTcJtRg9EKKE5vey1FPRlCqZyDgvrv9lvrlshrlzdNlGwoiScZo6eZpFvA3jP6HCOVvFpr1zAysJmK7wSgu6Z1lALVnKTKFbEpjgjnZ5VyHKF7hs2GHULoUz7A1s0kmfr7E4FobglHvNHUGGj3kfcAJCxvkYLOMZtGPFq8wOue+L3aBK7Gzh9U1V6eP1sHA0y133ZehnR+AAXHRVhGmBS5vHpoZmrulbiQC7mbx9vlyLTOCaFj1P1ns1k7saP20CtOv7yE4oeSMQcqAToKX4+VY/oKwHPZ3LL3fdWqV0S/vWmetYHl+XtIMfr4sJ5HoKPeEGaXWKkENsUVjCs33ftb+k6Vh68XGlO5A7hLzsl2zmozVHKtnVHMqNjuSl/VTLUSxGOrSXMajdQMItHxDOD4gI5oZA/rQy55rsm3Yy+uTl0/nRrfHed0TzZAp/+y2dFmxusO8axFlSjvdrqSAJF9oAESNvpV6G124LKs01uIQT+zPLtwgDb4ZnV8AzgWlnJDQlBhudEBAhKHZIsMbDqQKObxt6eBSEoHlSQ0h6eQsjG3PQLfrpgs2Etrj6F6v3VSQdVNRGPAtf9BhmQxdFVjtqGE3LKYP0CSBKO8s+AkdN+2qCyv8bmJMXg1Cdo8hAobgkSMkbr0ssfhDNp48vrt1aTxk0DNvk6fiscQ/0fLCCFgbYiFYBGCMxIZG/SpWGZsnu0oBlATBk5dHluQD+sCPAfyBUKx3EKxejXS0cGHce94AWGWPWBnGJFzzuQT+RpcXOg4BwUf2y4rgDoIClK6HFIQ24GiJO+IQ3OMlnjaVX9YpZV32pUuCD//LG58s7l7wBvI8R+QihN5Aw48W4LvXc3PG4w7HJzwL19dAmqRoCii1/27p2srBHkgTINJPeQ/6/p81+kE7Z2rpWliSuX9l6PQze7UQLbQ7Ri0FTVMSOPzRrqfXKoG/eBbQYV684ncHUcU7mxheZcq1thvLStQQYq8FKczA%3D%3D&ctl00%24BodyABC%24strDateDeb=" . $startString . "&ctl00%24BodyABC%24strDateFin=". $endString . "&ctl00%24BodyABC%24oneSico=on&ctl00%24BodyABC%24txtOneSico=". $codeISIN ."&ctl00%24BodyABC%24dlFormat=w&ctl00%24BodyABC%24listFormat=isin&ctl00%24BodyABC%24Button1=T%E9l%E9charger",
          CURLOPT_HTTPHEADER => array(
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
            "Accept-Encoding: gzip, deflate, br",
            //"Cache-Control: no-cache",
            //"Content-Length: 3015",
            "Content-Type: application/x-www-form-urlencoded",
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
        if (count($priceShareData["index"]) == 1) {
            $priceShareData["previous"] = $priceShareData["index"][$currentShareIndex];
            $priceShareData["next"] = $priceShareData["index"][$currentShareIndex];
        } elseif($currentShareIndex == 0) {
            // first share in the list
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
        foreach(['share', 'option', 'indice','fund', 'tracker'] as $type) {
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
