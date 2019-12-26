<?php

namespace App\Http\Controllers;

use App\PriceShares;
use App\Share;

use Illuminate\Http\Request;
use Carbon\Carbon;

class PriceSharesController extends Controller
{
    // load prices for all shares from last (or site start date) request to today's date
    public static function loadPrices() 
    {
        $results = [];
        $siteStartDate = Carbon::createFromFormat("!d/m/y", "01/01/18");
        $endDate = Carbon::today()->startOfDay();
        // ISO weekday from 1 (Monday) to 7 (Sunday) 
        // if today is the week end, we consider last friday 
        if ($endDate->isoWeekday() >5) {
            $endDate->subDay($endDate->isoWeekday() - 5);
        }
        $shares = Share::all();
        foreach ($shares as $share) {
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
                $results[$share->id]['end'] = $endDate;
                $results[$share->id]['start'] = $startDate;
                $results[$share->id]['imported'] = $oneResults['imported'];
                $results[$share->id]['error'] = $oneResults['error'];
            }
        }
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
    
}
