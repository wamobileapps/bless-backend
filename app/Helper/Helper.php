<?php
namespace App\Helper;

use App\Models\Notification;
use App\Models\User;

class Helper
{
    function sendPushNotification($title,$message,$fcm_token,$id=null) {
   $reciver_id =User::where('fcm_token',$fcm_token)->first();
Notification::create(['title'=>$title,'message'=>$message,'sender_id'=>Auth()->user()->id,'reciever_id'=>@$reciver_id->id,'read'=>0 ,"book_id"=>$id]);


$url = "https://fcm.googleapis.com/fcm/send";
        $key = 'AAAAYXQM61Y:APA91bG1a7qtR2KNgeA0uumpz34Ja79umkOCya2kFz-OAnRulxVtv3zS-tYhKk1ZAnqzPHegT-5er_apYIL3Z9hOeEANPe4SzY9SYz0FLkAGO3JWQiCV6JferPWb8nz1tkw_MPIvC4NX';
        $header = [
            'authorization: key=' . $key,
            'content-type: application/json'
        ];

        $postdata = '{
            "to" : "'.$fcm_token.'",
                "notification" : {
                    "title":"' . $title . '",
                    "body" : "' . $message . '"
                },
            "data" : {
                "id" : "'.$id.'",
                "title":"' . $title . '",
                "description" : "' . $message . '",
                "text" : "' . $message . '",
                "is_read": 0
              }
        }';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}