<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employee;
use App\Location;
use App\UserSetting;
use Auth;
use Illuminate\Support\Facades\Redirect;


class LocationController extends Controller
{

    public function ShowGoogleMap($id)
    {
        $location = Location::where('employee_id', $id)->first();
        if(!$location) {
            return 'notfound3';
        }
        
        return Redirect::intended("https://maps.google.com/maps?q=" . $location->latitude . "," . $location->longitude);
    }

    public function UpdateLocation(Request $request)
    {
        $number = $request->number;
        $number = str_replace('+63','0',$number);
        if(strlen($number) != 11){
            return 'invalidnumber';
        }
        $employee = Employee::where('contactnumber',$number)->first();
        if(!$employee) {
           // return App::abort(404, 'User record does not exist');
            return 'notfound';
        }
        $location = Location::where('employee_id', $employee->id)->first();
        if(!$location) {
            return 'notfound2';
        }

        $data = base64_decode($request->data);
        $accuracy = self::get_string_between($data, "Accuracy: ", "Latitude:");
        $accuracy = str_replace('m','',$accuracy);
        $latitude = self::get_string_between($data, "Latitude: ", "Longitude:");
        $longitude = self::get_string_between($data, "Longitude: ", ";");

        $location->accuracy = $accuracy;
        $location->latitude = trim($latitude);
        $location->longitude = trim($longitude);
        
        if($location->save()){
            return $location;
        }
        return 'failed';
    }

    public function TrackEmployee($id) {
        $employee = Employee::where('id',$id)->first();
        if(!$employee) {
           // return App::abort(404, 'User record does not exist');
            return 'notfound';
        }
        $number = $employee->contactnumber;
        return self::TrackNumber($number);
    }

    public function TrackNumber($num) {
        $number = str_replace('+63','0',$num);
        /*return self::send_notification('fqWcqJDQ9Hk:APA91bHUINbwVX0pmOklNsuglzPCmad2sYuPSVSv1vn49HLK6czc_QY1USf9aQ7EHHeAp_4G7uO76dCKCe_FNKvNY_NbBr6OqezhoRGHVEt9c7BVigW0kzjYMDxphBAyBSTp2xre6el48mz-WGryzWYIPNXIKlXJhQ',
                                        $number);*/
        $usersetting = UserSetting::where('user_id', Auth::id())->first();
        
        self::send_notification($usersetting->device_token,
        $number);
        return redirect('/employee')->with('success','Message Sent!');
    }


    function send_notification($to, $message)
{
	
define( 'API_ACCESS_KEY', 'AAAARrRd328:APA91bHnLn303WjjSkP9qyRNxVLVUh3tDl1rZyRc0N6gARrZQhCR2znShsVAP735SvgHe814j8p9xbnuDGwcSJjH2t1d70SbsWyW0SIa_71ndFgpVIavCrQev9v5NSUWLzz3hrSn3W-1rQN57XPdIq03Bgj3nHCXaw');
 //   $registrationIds = ;
#prep the bundle
     $msg = array
          (
		'body' 	=> $message,
		'title'	=> 'ppsitrackercmd',
             	
          );
	$fields = array
			(
				'to'		=> $to,
				'notification'	=> $msg
			);
	
	
	$headers = array
			(
				'Authorization: key=' . API_ACCESS_KEY,
				'Content-Type: application/json'
			);
#Send Reponse To FireBase Server	
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch );
		return $result;
		curl_close( $ch );
}

    private function sendGCM($message, $id) {


        $url = 'https://fcm.googleapis.com/fcm/send';
    
        $fields = array (
            'data' => array (
                "message" => $message
            ),
            'to' => $id
        );
            /*
                'registration_ids' => array (
                        $id
                ),
                'data' => array (
                        "message" => $message
                )*/
    
        $fields = json_encode ( $fields );
    
        $headers = array (
                'Authorization: key=' . "AAAARrRd328:APA91bHnLn303WjjSkP9qyRNxVLVUh3tDl1rZyRc0N6gARrZQhCR2znShsVAP735SvgHe814j8p9xbnuDGwcSJjH2t1d70SbsWyW0SIa_71ndFgpVIavCrQev9v5NSUWLzz3hrSn3W-1rQN57XPdIq03Bgj3nHCXaw",
                'Content-Type: application/json'
        );
    
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );
    
        $result = curl_exec ( $ch );
        return $result;
        curl_close ( $ch );
        
    }
    

    public function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

}
