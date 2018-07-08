<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\Statistics as StatisticResource;

class StatisticsControllerAPI extends Controller {

    public function generalStats(Request $request) {
        
        $usercount = DB::table('users')
                    ->select('nickname')
                    ->count();

        $totalplayedgames = DB::table('games')
                            ->select('status')
                            ->where('status', 'terminated')
                            ->count();

        $top5maxgames = DB::table('users')
                        ->select('nickname','total_games_played')
                        ->orderBy('total_games_played', 'desc')
                        ->take(5)
                        ->get();

        $top5maxpoints = DB::table('users')
                        ->select('nickname','total_points')
                        ->orderBy('total_points', 'desc')
                        ->take(5)
                        ->get();


        $top5besavg = DB::table('users')
                    ->select('nickname', 'total_points')
                    ->where('total_points', 'avg(total_points)')
                    ->orderBy('total_points', 'desc')
                    ->take(5)
                    ->get();

        $stat['usercount']= $usercount;
        $stat['totalplayedgames']= $totalplayedgames;
        $stat['top5maxgames']= $top5maxgames;
        $stat['top5maxpoints']= $top5maxpoints;
        $stat['top5besavg']= $top5besavg;

        return response()->json($stat);
    }

    public function userStats($id) {

        $totalgamesplayed = DB::table('users')
                            ->select('total_games_played')
                            ->where('id', $id)
                            ->get();

        $totalpoints  = DB::table('users')
                        ->select('total_points')
                        ->where('id', $id)
                        ->get();

        $totalwins = DB::table('game_user')
                        ->select('user_id', 'winner')
                        ->where(['user_id' => $id, 'winner' => 1])
                        ->count();

        $totalLoseorDie = DB::table('game_user')
                        ->select('user_id', 'winner')
                        ->where(['user_id' => $id, 'winner' => 0])
                        ->count();

        if($totalwins+$totalLoseorDie)
            $totalpointsavg  = ($totalwins+$totalLoseorDie)/$totalgamesplayed;
        else 
            $totalpointsavg = 0;

        $stat['totalgamesplayed']= ($totalgamesplayed[0]==null)? 0: $totalgamesplayed;
        $stat['totalpoints']= ($totalpoints[0]==null)? 0: $totalpoints;
        $stat['totalwins']= ($totalwins[0]==null)? 0: $totalwins;
        $stat['totalLoseorDie']= ($totalLoseorDie[0]==null)? 0: $totalLoseorDie;
        $stat['totalpoints']= ($totalpoints[0]==null)? 0: $totalpoints;
        $stat['totalpointsavg']= ($totalpointsavg[0]==null)? 0: $totalpointsavg;

        return response()->json($stat);
    }

}
