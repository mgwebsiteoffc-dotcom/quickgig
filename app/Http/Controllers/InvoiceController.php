<?php
namespace App\Http\Controllers;
use App\Models\Order;
use Illuminate\Http\Request;
class InvoiceController extends Controller {
 public function show(Request $request, Order $order, string $audience='buyer') {
  abort_unless(in_array($audience,['buyer','creator'],true),404);
  $user=$request->user();
  $allowed=$audience==='buyer' ? ($user->company_id && (int)$user->company_id===(int)$order->company_id) : ($user->creator_id && (int)$user->creator_id===(int)$order->creator_id);
  abort_unless($allowed || $user->isAdmin(),403);
  $order->load(['company','creator','service']);
  return response()->view('invoices.show',compact('order','audience'))->header('Content-Disposition','inline; filename="invoice-'.$order->uid.'-'.$audience.'.html"');
 }
}
