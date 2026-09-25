<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request; use App\Models\AuditLog;
class AuditAdminActions { public function handle(Request $request, Closure $next){ $response=$next($request); if(auth()->check() && $request->is('admin/*') && in_array($request->method(),['POST','PUT','PATCH','DELETE'])) { AuditLog::create(['user_id'=>auth()->id(),'action'=>$request->method().' '.$request->path(),'route'=>$request->route()?->getName(),'ip'=>$request->ip(),'metadata'=>collect($request->except(['password','password_confirmation','_token']))->take(30)->all()]); } return $response; } }
