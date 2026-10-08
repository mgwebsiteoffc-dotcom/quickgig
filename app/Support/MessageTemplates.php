<?php
namespace App\Support;
class MessageTemplates {
 public static function get(string $key,string $subject,string $body,array $vars=[]):array {
  $subject=(string)setting('notifications.templates.'.$key.'.subject',$subject); $body=(string)setting('notifications.templates.'.$key.'.body',$body);
  foreach($vars as $name=>$value){$subject=str_replace('{{'.$name.'}}',(string)$value,$subject);$body=str_replace('{{'.$name.'}}',(string)$value,$body);}
  return compact('subject','body');
 }
}
