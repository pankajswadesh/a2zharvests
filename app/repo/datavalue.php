<?php
namespace App\repo;
use App\Model\ParameterModel;
use App\Model\SettingModel;
use App\User;
use Auth;
use Exception;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

/**
 * Created by PhpStorm.
 * User: WEB CALLING
 * Date: 24-04-2018
 * Time: 10:08
 */
class datavalue
{
    public function getUniqueCode($TABLE=''){
        $user = ParameterModel::select("parameter.*"
            ,DB::raw("CONCAT(CONCAT(CONCAT(CONCAT(`PREFIX`,`DVDR`), LPAD(CAST(`STNO` AS CHAR CHARSET UTF8), `NUMBERPAD`, 0)),`DVDR`), `SUFFIX`) AS MSTCODE"))
            ->where('PARAM',$TABLE)
            ->first();
            ParameterModel:: where('PARAM',$TABLE)->update([
                'STNO' => $user->STNO + 1,
            ]);
            return $user->MSTCODE;
    }

    public function upload_pic($file, $new_path)
    {
        if(is_string($file)){
            try {
                $position = strpos( $file,';base64');
               if($position==true) {
                   $image_parts = explode(";base64", $file);
                   $image_type_aux = explode("image/", $image_parts[0]);
                   $image_type = $image_type_aux[1];
                   $image_base64 = base64_decode($image_parts[1]);
                   $imageName = str_random(6) . '_' . time() . '.' . $image_type;
                   $destinationPath = public_path("images/$new_path");
                   $thumb_img = Image::make($image_base64)->resize(200, 200);
                   $thumb_img->save($destinationPath . '/' . $imageName, 80);
                   $msg = 'File Uploaded Successfully.';
                   return array('status' => 'success', 'file_name' => $imageName, 'msg' => $msg);
               }else{
                   $decoded_file = base64_decode($file); // decode the file
                   $mime_type = finfo_buffer(finfo_open(), $decoded_file, FILEINFO_MIME_TYPE); // extract mime type
                   $extension = $this->mime2ext($mime_type); // extract extension from mime type
                   $imageName = str_random(6) . '_' . time() .'.'. $extension; // rename file as a unique name
                   $path = public_path('/images/'.$new_path.'/'.$imageName); // add the specific path to save the file
                   try {
                       $supported_image = array('jpg','jpeg','png','bmp','gif');
                       if (in_array($extension, $supported_image)){
                           $destinationPath = public_path("images/$new_path");  // add the specific path to save the file
                           $thumb_img = Image::make($decoded_file)->resize(200, 200); // resize image
                           $thumb_img->save($destinationPath . '/' . $imageName, 80); //save
                       }else {
                           file_put_contents($path, $decoded_file); // save
                       }
                       $msg = 'File Uploaded Successfully.';
                       return array('status' => 'success', 'file_name' => $imageName, 'msg' => $msg);
                   } catch (Exception $e) {
                       $msg='File Not Uploaded.';
                       return array('status'=>'error','msg'=>$msg);
                   }
               }
            }catch(Exception $e) {
                $msg='File Not Uploaded.';
                return array('status'=>'error','msg'=>$msg);
            }

        }else{
            $msg='Not String';
            return array('status'=>'error','msg'=>$msg);
        }
    }

    function mime2ext($mime){
        $all_mimes = '{"png":["image\/png","image\/x-png"],"bmp":["image\/bmp","image\/x-bmp",
    "image\/x-bitmap","image\/x-xbitmap","image\/x-win-bitmap","image\/x-windows-bmp",
    "image\/ms-bmp","image\/x-ms-bmp","application\/bmp","application\/x-bmp",
    "application\/x-win-bitmap"],"gif":["image\/gif"],"jpeg":["image\/jpeg",
    "image\/pjpeg"],"xspf":["application\/xspf+xml"],"vlc":["application\/videolan"],
    "wmv":["video\/x-ms-wmv","video\/x-ms-asf"],"au":["audio\/x-au"],
    "ac3":["audio\/ac3"],"flac":["audio\/x-flac"],"ogg":["audio\/ogg",
    "video\/ogg","application\/ogg"],"kmz":["application\/vnd.google-earth.kmz"],
    "kml":["application\/vnd.google-earth.kml+xml"],"rtx":["text\/richtext"],
    "rtf":["text\/rtf"],"jar":["application\/java-archive","application\/x-java-application",
    "application\/x-jar"],"zip":["application\/x-zip","application\/zip",
    "application\/x-zip-compressed","application\/s-compressed","multipart\/x-zip"],
    "7zip":["application\/x-compressed"],"xml":["application\/xml","text\/xml"],
    "svg":["image\/svg+xml"],"3g2":["video\/3gpp2"],"3gp":["video\/3gp","video\/3gpp"],
    "mp4":["video\/mp4"],"m4a":["audio\/x-m4a"],"f4v":["video\/x-f4v"],"flv":["video\/x-flv"],
    "webm":["video\/webm"],"aac":["audio\/x-acc"],"m4u":["application\/vnd.mpegurl"],
    "pdf":["application\/pdf","application\/octet-stream"],
    "pptx":["application\/vnd.openxmlformats-officedocument.presentationml.presentation"],
    "ppt":["application\/powerpoint","application\/vnd.ms-powerpoint","application\/vnd.ms-office",
    "application\/msword"],"docx":["application\/vnd.openxmlformats-officedocument.wordprocessingml.document"],
    "xlsx":["application\/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application\/vnd.ms-excel"],
    "xl":["application\/excel"],"xls":["application\/msexcel","application\/x-msexcel","application\/x-ms-excel",
    "application\/x-excel","application\/x-dos_ms_excel","application\/xls","application\/x-xls"],
    "xsl":["text\/xsl"],"mpeg":["video\/mpeg"],"mov":["video\/quicktime"],"avi":["video\/x-msvideo",
    "video\/msvideo","video\/avi","application\/x-troff-msvideo"],"movie":["video\/x-sgi-movie"],
    "log":["text\/x-log"],"txt":["text\/plain"],"css":["text\/css"],"html":["text\/html"],
    "wav":["audio\/x-wav","audio\/wave","audio\/wav"],"xhtml":["application\/xhtml+xml"],
    "tar":["application\/x-tar"],"tgz":["application\/x-gzip-compressed"],"psd":["application\/x-photoshop",
    "image\/vnd.adobe.photoshop"],"exe":["application\/x-msdownload"],"js":["application\/x-javascript"],
    "mp3":["audio\/mpeg","audio\/mpg","audio\/mpeg3","audio\/mp3"],"rar":["application\/x-rar","application\/rar",
    "application\/x-rar-compressed"],"gzip":["application\/x-gzip"],"hqx":["application\/mac-binhex40",
    "application\/mac-binhex","application\/x-binhex40","application\/x-mac-binhex40"],
    "cpt":["application\/mac-compactpro"],"bin":["application\/macbinary","application\/mac-binary",
    "application\/x-binary","application\/x-macbinary"],"oda":["application\/oda"],
    "ai":["application\/postscript"],"smil":["application\/smil"],"mif":["application\/vnd.mif"],
    "wbxml":["application\/wbxml"],"wmlc":["application\/wmlc"],"dcr":["application\/x-director"],
    "dvi":["application\/x-dvi"],"gtar":["application\/x-gtar"],"php":["application\/x-httpd-php",
    "application\/php","application\/x-php","text\/php","text\/x-php","application\/x-httpd-php-source"],
    "swf":["application\/x-shockwave-flash"],"sit":["application\/x-stuffit"],"z":["application\/x-compress"],
    "mid":["audio\/midi"],"aif":["audio\/x-aiff","audio\/aiff"],"ram":["audio\/x-pn-realaudio"],
    "rpm":["audio\/x-pn-realaudio-plugin"],"ra":["audio\/x-realaudio"],"rv":["video\/vnd.rn-realvideo"],
    "jp2":["image\/jp2","video\/mj2","image\/jpx","image\/jpm"],"tiff":["image\/tiff"],
    "eml":["message\/rfc822"],"pem":["application\/x-x509-user-cert","application\/x-pem-file"],
    "p10":["application\/x-pkcs10","application\/pkcs10"],"p12":["application\/x-pkcs12"],
    "p7a":["application\/x-pkcs7-signature"],"p7c":["application\/pkcs7-mime","application\/x-pkcs7-mime"],"p7r":["application\/x-pkcs7-certreqresp"],"p7s":["application\/pkcs7-signature"],"crt":["application\/x-x509-ca-cert","application\/pkix-cert"],"crl":["application\/pkix-crl","application\/pkcs-crl"],"pgp":["application\/pgp"],"gpg":["application\/gpg-keys"],"rsa":["application\/x-pkcs7"],"ics":["text\/calendar"],"zsh":["text\/x-scriptzsh"],"cdr":["application\/cdr","application\/coreldraw","application\/x-cdr","application\/x-coreldraw","image\/cdr","image\/x-cdr","zz-application\/zz-winassoc-cdr"],"wma":["audio\/x-ms-wma"],"vcf":["text\/x-vcard"],"srt":["text\/srt"],"vtt":["text\/vtt"],"ico":["image\/x-icon","image\/x-ico","image\/vnd.microsoft.icon"],"csv":["text\/x-comma-separated-values","text\/comma-separated-values","application\/vnd.msexcel"],"json":["application\/json","text\/json"]}';
        $all_mimes = json_decode($all_mimes,true);
        foreach ($all_mimes as $key => $value) {
            if(array_search($mime,$value) !== false) return $key;
        }
        return false;
    }



public function send_sms($phone_no,$otp){
        $user='user';
        $numbers = array($phone_no);
        $numbers = implode(',', $numbers);
        // $apiKey = urlencode('NzI0ODMzNTUzMTUwNmY2YjMzNjQ0OTZkMzM0Mjc3NDM=');
        $apiKey = urlencode('NzI0ODMzNTUzMTUwNmY2YjMzNjQ0OTZkMzM0Mjc3NDM=');
        $sender = urlencode('AZHARV');
        $message = rawurlencode('Dear '.$user.', Your Otp is '.$otp.',Team A2Z Harvests.');
        $data = 'apikey=' . $apiKey . '&numbers=' . $numbers . "&sender=" . $sender . "&message=" . $message;
        // Send the GET request with cURL
        $ch = curl_init('https://api.textlocal.in/send/?' . $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $resp = json_decode($response,true);
        if ($resp["status"] =="success") {
            return ['status'=>'success','msg'=>$resp];
        } else {
            return ['status'=>'fail','msg'=>$resp];
        }
    }
    static function  get_sale_price($price,$discount_name,$discount_value){
        if($discount_name=='%'){
            $sale_price = $price - (($price/100) * $discount_value);
        }elseif($discount_name=='rs'){
            $sale_price = $price - $discount_value;
        }else{
            $sale_price = $price;
        }
        return ceil($sale_price);

    }
    static function getLatLong(){
        if (Auth::check()) {
            $latitude = Auth::user()->latitude;
            $longitude = Auth::user()->longitude;
            $address = Auth::user()->location;
            $status = true;
        } else {
            $location = \Session::get('location');
            if(isset($location["latitude"])){
                $latitude = $location["latitude"];
                $longitude = $location["longitude"];
                $address = $location["address"];
                $status = true;
            }else{
                $latitude = 87.5255825;
                $longitude = 27.5255825;
                $address = "location not set";
                $status = false;
            }

        }
        return array('latitude'=>$latitude,'longitude'=>$longitude,'address'=>$address,'status'=>$status);
    }
    public static function checkAvailability($lat,$lng){

        return true;
        // $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$lng."&sensor=false&key=AIzaSyDLabnBWcC5OeodARQ-i4SbNXW8qb1RF7I";
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $response = curl_exec($ch);
        // curl_close($ch);
        // $returnData =  json_decode($response,true);
        // $districtName = '';
        // if(!empty($returnData["results"])){
        //     foreach ($returnData["results"][0]["address_components"] as $row){
        //         if($row["types"][0]== "locality"){
        //             $districtName = $row["long_name"];
        //         }
        //     }
        //     if($districtName !="Jammu"){
        //         return false;
        //     }else{
        //         return true;
        //     }
        // }else{
        //     return false;
        // }
    }
    static function get_lat_long($address){
         
     if($address=="180001"){
            return ['lat'=>32.7384,'lng'=>74.8653,'response'=>["180001"]];
        }
        $url = "https://maps.google.com/maps/api/geocode/json?address=".urlencode($address)."&sensor=false&key=AIzaSyDLabnBWcC5OeodARQ-i4SbNXW8qb1RF7I";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $responseJson = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($responseJson);
        if ($response->status == 'OK') {
            $latitude = $response->results[0]->geometry->location->lat;
           $longitude = $response->results[0]->geometry->location->lng;
         
            return ['lat'=>$latitude,'lng'=>$longitude,'response'=>$response];
        } else {
            return ['lat'=>'','lng'=>'','response'=>$response];
        }
    }
    public static function getNearbySupplier($latitude,$longitude,$supplier_id=null){
        $distance=SettingModel::where('id',2)->value('value');
        $user_ids = User::select(DB::raw('*, ( 6367 * acos( cos( radians(' . $latitude . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( latitude ) ) ) ) AS distance'))
            ->having('distance', '<=', DB::raw('available_distance'))
            ->orderBy('distance');
            if($supplier_id!=null){
                $user_ids->where('id', '<>',$supplier_id);
            }
        $user_ids = $user_ids->where('status', 'Active')
            ->pluck('id')
            ->toArray();
        return $user_ids;
    }

}
