<?php
namespace App\Support;
use App\Models\Company;
use App\Models\Creator;
use App\Models\User;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationFacade;
class Notifier
{
 public static function toUserOf(?Company $company, Notification $notification): void
 {
  $user=User::where('company_id',$company?->id)->first(); $email=$user?->email ?: $company?->email; self::send($email,$notification); self::whatsapp($company?->phone ?: $user?->phone, self::message($notification));
 }
 public static function toFreelancer(?Creator $creator, Notification $notification): void { self::send($creator?->email,$notification); self::whatsapp($creator?->phone,self::message($notification)); }
 public static function send(?string $email, Notification $notification): void
 {
  if (! setting('notifications.email_enabled', true) || ! $email || ! filter_var($email,FILTER_VALIDATE_EMAIL)) return;
  try { NotificationFacade::route('mail',$email)->notify($notification); } catch (\Throwable $e) { Log::warning('notification.failed',['to'=>$email,'error'=>$e->getMessage()]); }
 }
 public static function whatsapp(?string $phone, string $message): void
 {
  if (!setting('notifications.whatsapp_enabled',false) || !filled($phone) || !filled(setting('notifications.whatify_api_key'))) return;
  $digits=preg_replace('/\D+/','',(string)$phone); if(strlen($digits)===10) $digits='91'.$digits;
  try {
   $request=Http::withHeaders(['X-API-Key'=>(string)setting('notifications.whatify_api_key')])->timeout(8);
   $payload=['phone'=>$digits,'message'=>\Illuminate\Support\Str::limit($message,4096)];
   if(filled($id=setting('notifications.whatify_account_id'))) $payload['whatsapp_account_id']=(int)$id;
   $response=$request->post('https://whatify.in/api/v1/external/send-message',$payload);
   if($response->failed()) Log::warning('whatsapp.failed',['status'=>$response->status(),'body'=>\Illuminate\Support\Str::limit($response->body(),200)]);
  } catch(\Throwable $e) { Log::warning('whatsapp.exception',['error'=>$e->getMessage()]); }
 }
 private static function message(Notification $notification): string
 {
  $name=class_basename($notification); $order=$notification->order ?? null; $payout=$notification->payout ?? null;
  if($payout) return 'GIG60: payout '.$payout->uid.' for ₹'.number_format($payout->amount).' has been released/queued.';
  if($order) return 'GIG60: order '.$order->uid.' update — '.($order->service->title ?? 'your service').'. Check your dashboard for details.';
  return 'GIG60: '.$name.' update. Check your dashboard for details.';
 }
}
