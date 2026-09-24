<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = collect([
            ['id'=>1,'company'=>'Avante Studio','person'=>'Rohan Sharma','email'=>'rohan@avante.studio','phone'=>'98765 43210','plan'=>'Pro','orders'=>42,'spent'=>'₹1.8L','joined'=>'2024-01-15','initials'=>'AS'],
            ['id'=>2,'company'=>'BrandScale Media','person'=>'Priya Kapoor','email'=>'priya@brandscale.in','phone'=>'98765 43211','plan'=>'Team','orders'=>28,'spent'=>'₹96k','joined'=>'2024-02-20','initials'=>'BS'],
            ['id'=>3,'company'=>'GrowthX Labs','person'=>'Aman Verma','email'=>'aman@growthx.in','phone'=>'98765 43212','plan'=>'Starter','orders'=>11,'spent'=>'₹34k','joined'=>'2024-03-10','initials'=>'GX'],
            ['id'=>4,'company'=>'ConcertPass','person'=>'Karan Mehta','email'=>'karan@concertpass.in','phone'=>'98765 43213','plan'=>'Pro','orders'=>18,'spent'=>'₹72k','joined'=>'2024-04-01','initials'=>'CP'],
        ]);
        $q = $request->query('q');
        if ($q) $companies = $companies->filter(fn($c)=> str_contains(strtolower($c['company'].$c['person']), strtolower($q)));
        return view('admin.companies.index', compact('companies','q'));
    }
}
