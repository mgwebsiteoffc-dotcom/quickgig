<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; use App\Models\AuditLog; use Illuminate\Http\Request;
class AuditLogController extends Controller { public function index(Request $r){ $logs=AuditLog::with('user')->latest()->paginate(40)->withQueryString(); return view('admin.audit.index',compact('logs')); } }
