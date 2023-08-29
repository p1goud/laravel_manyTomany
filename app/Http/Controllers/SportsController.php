<?php

namespace App\Http\Controllers;

use App\Models\Sport;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SportsController extends Controller
{
    public function create()
    {
        $sports = Sport::all();
        $countries = Country::all();

        return view('sports.create', compact('sports', 'countries'));
    }

    public function store(Request $request)
    {   
        
        foreach ($request->input() as $key => $value) {
            if($key !== '_token'){
                $sports = Sport::where('name',$key)->first();
                $gold_country = Country::where('short_code',$value[0])->first();
                $silver_country = Country::where('short_code',$value[1])->first();
                $bronze_country = Country::where('short_code',$value[2])->first();

                $sports->sports_country()->attach($gold_country->id,['type'=>'Gold']);
                $sports->sports_country()->attach($silver_country->id,['type'=>'Silver']);
                $sports->sports_country()->attach($bronze_country->id,['type'=>'Bronze']);
            }
        }

        return redirect()->route('show');
    }

    public function show()
    {
        // Add your code here
        $country = Country::has('sports_country')
            ->select('name')->withCount([
            'sports_country as gold' => function ($query) {
                $query->where('type','Gold');
            },
            'sports_country as silver' => function ($query) {
                $query->where('type','Silver');
            },
            'sports_country as bronze' => function ($query) {
                $query->where('type','Bronze');
            }
        ])->get();
        
    
        return view('sports.show',compact('country'));
    }
}
