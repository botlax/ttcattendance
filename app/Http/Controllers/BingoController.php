<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\BiServer;
use App\BiPlayers;
use App\BiCards;
use App\BiItems;
use App\User;
use Hash;

class BingoController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('bingoPlayer');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userId = \Auth::user()->id;
        $player = BiPlayers::where('user_id',$userId)->get();
        if(!empty($player->toArray())){
            //dd($player->first()->status);
            if($player->first()->status == 'admin'){
                //dd($player->first()->biserver()->first()->id);
                return redirect('bingo/server/'.$player->first()->biserver()->first()->id);
            }
        }
        $servers = BiServer::all();
        return view('bingo.home',compact('servers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetchServers()
    {
        $servers = BiServer::all()->toArray();
        echo json_encode($servers);
        die();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createServer(Request $request)
    {
        $this->validate($request,[
            'name' => 'required|unique:bi_server,name',
            'password' => 'required|confirmed'
        ]);

        $request->flash();

        $userId = \Auth::user()->id;
        $name = \Auth::user()->name;
        $data = $request->all();
        $data['start'] = 'nogame';
        $data['mode'] = '';
        $data['winners'] = 0;
        $server = BiServer::create($data);
        $playerAdmin = new BiPlayers(['name'=>$name,'user_id'=>$userId,'status'=>'admin']);
        //dd($server);
        $server->biplayers()->save($playerAdmin);
        session(['status'=>'admin']);
        return redirect('bingo/server/'.$server->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function serverForm()
    {
        $userId = \Auth::user()->id;
        $player = BiPlayers::where('user_id',$userId)->get();
        if(!empty($player->toArray())){
            //dd($player->first()->status);
            if($player->first()->status == 'admin'){
                //dd($player->first()->biserver()->first()->id);
                return redirect('bingo/server/'.$player->first()->biserver()->first()->id);
            }
        }
        $servers = BiServer::all();
        return view('bingo.server_form',compact('servers'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function serverRoom($id)
    {
        $server = BiServer::findOrFail($id);
        $userId = \Auth::user()->id;
        $player = BiPlayers::where('user_id',$userId);
        //dd($server->biplayers()->where('user_id',$userId)->first());

        //if player belongs to this server
        if($server->biplayers()->where('user_id',$userId)->first()){
            //if player belongs to server
            return view('bingo.server',compact('id','server'));
        }
        else{//if player does not belong to server
            return redirect('bingo/server/'.$id.'/login');
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function serverRoomAuth($id)
    {
        if($this->isServerPlayer($id)){
            return redirect('/bingo/server/'.$id);
        }
        else{
            $server_id = $id;
            $servers = BiServer::all();
            return view('bingo.auth',compact('server_id','servers'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function serverRoomAuthPost($id,Request $request)
    {
        $name = \Auth::user()->name;
        $userId = \Auth::user()->id;
        $server = BiServer::findOrFail($id);
        $passwordInput = $request->input('password');
        $password = BiServer::find($id)->password;
        if (Hash::check($passwordInput, $password)) {
            $player = new BiPlayers(['name'=>$name,'user_id'=>$userId,'status'=>'player']);
            $server->biplayers()->save($player);
            session(['status'=>'player']);
            return redirect('bingo/server/'.$id);
        }
        else{
            return redirect('bingo/server/'.$id.'/login');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getNewGame($id)
    {
        $server = BiServer::findOrFail($id);
        $players = $this->getPlayers($id);
        $players = $players->lists('name','id')->toArray();
        $noOfPlayers = count($players);
        $winners = range(1, $noOfPlayers);
        if($this->isServerAdmin($id)){
            if($server->mode == ''){
                return view('bingo.new_game',compact('id','players','winners','server'));
            }
            else{
                return redirect('bingo/server/'.$id);
            }
        }
        elseif($this->isServerPlayer($id)){
            return redirect('/bingo/server/'.$id);
        }
        else{
            return redirect('/bingo');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function postNewGame($id,Request $request)
    {
        $players = $this->getPlayers($id);
        foreach($players as $player){
            if($player->status != 'admin'){
                $user = $player;
                $user->status = 'player';
                $user->save(); 
            }
        }
        $baller = BiPlayers::find($request->input('baller'));
        if($baller->status != 'admin'){
            $baller->status = 'baller';
            $baller->save();
        }

        $server = BiServer::findOrFail($id);
        $server->mode = $request->input('mode');
        $server->winners = $request->input('winners') + 1;
        $server->start = 'pending';
        $server->save();

        $players = $this->getPlayers($id);
        $players = $players->lists('name','id')->toArray();
        $noOfPlayers = count($players);
        $winners = range(1, $noOfPlayers);
        
        return redirect('bingo/server/'.$id);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancelGame($id,Request $request)
    {
        $server = BiServer::findOrFail($id);
        $server->biitems()->detach();
        $server->mode = '';
        $server->winners = 0;
        $server->start = 'cancelled';
        $server->save();
        $players = $server->biplayers()->get();
        foreach ($players as $player) {
            if($player->status != 'admin'){
                $user = $player;
                $user->status = 'player';
                $user->save();
            }

            $cards = $player->bicards()->orderBy('id','ASC')->get();

            if(!empty($cards->toArray())){
                foreach($cards as $card){
                    $card->biitems()->detach();
                }
            }
            $player->bicards()->delete();
        }
        $request->session()->forget('combos');
        $request->session()->forget('thicked');
        return redirect('/bingo/server/'.$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restartGame($id)
    {
        $server = BiServer::findOrFail($id);
        $server->biitems()->detach();
        $cards = $server->bicards()->orderBy('id','ASC')->get();
        if(!empty($cards->toArray())){
            foreach($cards as $card){
                foreach($card->biitem as $item){
                    $holder = $item->pivot;
                    $holder->thicked = 'no';
                    $holder->save();
                }
            }
        }
        return redirect('/bingo/server/'.$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function closeServer($id,Request $request)
    {
        $server = BiServer::findOrFail($id);
        $cards = $server->bicards()->orderBy('id','ASC')->get();

        if(!empty($cards->toArray())){
            foreach($cards as $card){
                $card->biitems()->detach();
            }
        }

        $server->bicards()->delete();
        $server->biitems()->detach();
        $server->biplayers()->delete();

        BiServer::destroy($id);

        $request->session()->forget('combos');
        $request->session()->forget('thicked');

        return redirect('/bingo');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function leaveServer($id,Request $request)
    {

        $server = BiServer::findOrFail($id);
        $userId = \Auth::user()->id;
        $player = BiPlayers::where('user_id',$userId)->first();
        $cards = $player->bicards()->orderBy('id','ASC')->get();

        if(!empty($cards->toArray())){
            foreach($cards as $card){
                $card->biitems()->detach();
            }
        }
        $player->bicards()->delete();
        $server->biplayers()->where('user_id',$userId)->delete();
        $request->session()->forget('combos');
        $request->session()->forget('thicked');
        return redirect('/bingo');
    }



    //---------------------------------ajax functions


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxGetGameStatus()
    {
        $id = \Input::get('id');

        $server = BiServer::findOrFail($id);

        echo json_encode(['mode' => $server->mode,'started' => $server->start]);
        die();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxGetGameStart()
    {
        $id = \Input::get('id');

        $server = BiServer::findOrFail($id);

        echo $server->start;
        die();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxStartGame()
    {
        
        $id = \Input::get('id');
        $userId = \Auth::user()->id;
        $server = BiServer::findOrFail($id);
        $player = BiPlayers::where('user_id',$userId)->first();
        $cards = [];
        foreach($player->bicards as $card){
            $cards[$card->id] = [];
            foreach ($card->biitems as $item) {
                $cards[$card->id][] = $item->item;
            }
        }
        
        $server->start = 'ongoing';
        $server->save();
        echo json_encode($cards);
        die();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxSetCards()
    {
        
        $id = \Input::get('id');
        $userId = \Auth::user()->id;
        $server = BiServer::findOrFail($id);
        $cardCount = \Input::get('cardNo');

        if(!BiPlayers::where('user_id',$userId)->first()->bicards()->first()){
            $cards = $this->generateCards($cardCount);

            foreach($cards as $card){
                $newCard = new BiCards();
                BiPlayers::where('user_id',\Auth::user()->id)->first()->bicards()->save($newCard);
                foreach($card as $letter){
                    foreach($letter as $item){
                        $playerCard = BiCards::orderBy('id','DESC')->first();
                        $playerCard->biitems()->attach($item);
                    }
                }
            }


            $player = BiPlayers::where('user_id',$userId)->first();
            $playerCards = [];
            foreach($player->bicards as $card){
                $playerCards[$card->id] = [];
                foreach ($card->biitems as $item) {
                    $playerCards[$card->id][] = $item->item;
                }
            }


            $combo = [];
            $thicked = [];
            $x = 1;
            foreach($cards as $card){
                if($server->mode == 'normal'){
                    $combo['card'.$x][] = $card['B'];
                    $combo['card'.$x][] = $card['I'];
                    $combo['card'.$x][] = $card['N'];
                    $combo['card'.$x][] = $card['G'];
                    $combo['card'.$x][] = $card['O'];

                    $combo['card'.$x][] = [$card['B'][0],$card['I'][0],$card['N'][0],$card['G'][0],$card['O'][0]];
                    $combo['card'.$x][] = [$card['B'][1],$card['I'][1],$card['N'][1],$card['G'][1],$card['O'][1]];
                    $combo['card'.$x][] = [$card['B'][2],$card['I'][2],$card['N'][2],$card['G'][2],$card['O'][2]];
                    $combo['card'.$x][] = [$card['B'][3],$card['I'][3],$card['N'][3],$card['G'][3],$card['O'][3]];
                    $combo['card'.$x][] = [$card['B'][4],$card['I'][4],$card['N'][4],$card['G'][4],$card['O'][4]];

                    $combo['card'.$x][] = [$card['B'][0],$card['I'][1],$card['N'][2],$card['G'][3],$card['O'][4]];
                    $combo['card'.$x][] = [$card['B'][4],$card['I'][3],$card['N'][2],$card['G'][1],$card['O'][0]];

                    $combo['card'.$x][] = [$card['B'][0],$card['B'][4],$card['O'][0],$card['O'][4],$card['N'][2]];
                }
                else{
                    foreach($card as $letter){
                        foreach($letter as $item){
                            $combo['card'.$x][] = $item;
                        }
                    }
                }
                $thicked['card'.$x][] = $card['N'][2];
                $x++;
            }

            session(['combos'=>$combo]);
            session(['thicked' => $thicked]);

            echo json_encode($playerCards);
        }
        else{
            $player = BiPlayers::where('user_id',$userId)->first();
            $cards = [];
            foreach($player->bicards as $card){
                $cards[$card->id] = [];
                foreach ($card->biitems as $item) {
                    $cards[$card->id][] = $item->item;
                }
            }

            echo json_encode($cards);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxPlayerStatus()
    {
        
        $id = \Input::get('id');

        $server = BiServer::findOrFail($id);

        $players = $server->biplayers()->orderBy('id','ASC')->get();
        $status = [];
        foreach($players as $player){
            if($player->status != 'admin'){
                if($player->bicards()->first()){
                    $status[$player->name] = 'Ok';
                }
                else{
                    $status[$player->name] = 'Pending';
                }
            }
        }

        echo json_encode($status);
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxGetCardCount()
    {
        $id = \Input::get('id');

        $server = BiServer::findOrFail($id);

        $player = BiPlayers::where('user_id',\Auth::user()->id)->first();
        if($player->bicards()->first()){
            echo 'yes';
        }else{
            echo 'no';
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxThick()
    {
        $id = \Input::get('id');
        $cardId = \Input::get('cardId');
        $cardNo = \Input::get('cardNo');
        $num = \Input::get('bingoNum');
        $server = BiServer::findOrFail($id);
        $balls = $server->biitems()->lists('item')->toArray();

        $playerCard = BiCards::find($cardId);

        $good = false;
        if(in_array(intval($num), $balls)){
            $good = true;
        }

        if($good){
            $cardItem = $playerCard->biitems()->find(intval($num));
            $pivot = $cardItem->pivot;
            $pivot->thicked = 'yes';
            $pivot->save();

            $thicked = session('thicked');
            if(!in_array(intval($num), $thicked['card'.$cardNo])){
                $thicked['card'.$cardNo][] = intval($num);
            }
            session(['thicked' => $thicked]);

            $response = 'good';
        }
        else{
            $response = 'bad';
        }
        echo $response;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxBall()
    {
        $id = \Input::get('id');

        $server = BiServer::findOrFail($id);
        $ballCount = count($server->biitems()->lists('item')->toArray());

        if($ballCount != 75){
            $ball = $this->generateBall($id);
            $server->biitems()->attach($ball);
        }
        else{
            $ball = 'done';
        }
        

        echo $ball;
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxBingo()
    {
        $id = \Input::get('id');
        $cardId = \Input::get('cardId');
        $cardNo = \Input::get('cardNo');

        $server = BiServer::findOrFail($id);

        $thicked = session('thicked');
        $thicked = $thicked['card'.$cardNo];
        $combos = session('combos');

        $winners = $server->winners;
        $response['bingo'] = false;
        $response['game'] = 'continue';
        
        if($server->mode == 'normal'){
            foreach($combos['card'.$cardNo] as $combo){
                $match = array_intersect($combo, $thicked);
                if(count($match) == 5){
                    $response['bingo'] = true;
                    if(intval($winners) == 1){
                        $response['game'] = 'gameover';
                        $server->winners = intval($winners) - 1;
                        $server->mode = '';
                        $server->start = 'nogame';
                        $server->save();
                    }
                    else{
                        $server->winners = intval($winners) - 1;
                        $server->save();
                    }
                    break;
                }
            }
        }
        else{
            if(count(array_intersect($combos['card'.$cardNo], $thicked)) == 25){
                $response['bingo'] = true;
                if(intval($winners) == 1){
                    $response['game'] = 'gameover';
                    $server->winners = intval($winners) - 1;
                    $server->mode = '';
                    $server->start = 'nogame';
                    $server->save();
                }
                else{
                    $server->winners = intval($winners) - 1;
                    $server->save();
                }
            }
        }
        echo json_encode($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxFetchWinners()
    {
        $id = \Input::get('id');

        $server = BiServer::findOrFail($id);
        
        if($server->winners == 0){
            echo 0;
            die();
        }
        echo 1;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxReset(Request $request)
    {
        $id = \Input::get('id');

        $server = BiServer::findOrFail($id);

        if(session('status') == 'admin'){
            $server->mode = '';
            $server->winners = 0;
            $server->start = 'nogame';
            $server->save();

            $server->biitems()->detach();
            $server->bicards()->delete();

            $players = $this->getPlayers($id);
            foreach($players as $player){
                if($player->status != 'admin'){
                    $user = $player;
                    $user->status = 'player';
                    $user->save(); 
                }
            }
        }
        $request->session()->forget('combos');
        $request->session()->forget('thicked');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxCheckStatus(Request $request)
    {
        $id = \Input::get('id');

        $server = BiServer::findOrFail($id);

        $player = BiPlayers::where('user_id',\Auth::user()->id)->first();

        echo $player->status;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxFetchBalls(Request $request)
    {
        $id = \Input::get('id');

        $server = BiServer::findOrFail($id);

        $balls = $server->biitems()->lists('item')->toArray();

        echo json_encode($balls);
    }
    //----------------------------------------------------------------------------
    public function isServerPlayer($serverId){
        $userId = \Auth::user()->id;
        $server = BiServer::findOrFail($serverId);
        if($server->biplayers()->where('user_id',$userId)->first()){
            return true;
        }
        else{
            return false;
        }
    }

    public function isServerAdmin($serverId){
        $userId = \Auth::user()->id;
        $server = BiServer::findOrFail($serverId);
        $player = $server->biplayers()->where('user_id',$userId)->first();
        if($player && $player->status == 'admin'){
            return true;
        }
        else{
            return false;
        }
    }

    public function getPlayers($serverId){
        return BiServer::findOrFail($serverId)->biplayers()->orderBy('id','ASC')->get();
    }

    public function generateCards($cardNo){
        
        $cards = [];
        $bingo = ['B','I','N','G','O'];
        for($x = 0;$x < $cardNo;$x++){
            for($y = 0;$y<5;$y++){
                $cards['card'.strval($x+1)][$bingo[$y]] = [];
                for($z = 0;$z<5;$z++){
                    do{
                        $item = rand(($y*15)+1,($y+1)*15);
                    }while(in_array($item, $cards['card'.strval($x+1)][$bingo[$y]]));
                    $cards['card'.strval($x+1)][$bingo[$y]][] = $item;
                }
            }
        }

        return $cards;
    }

    public function generateBall($serverId){
        $server = BiServer::findOrFail($serverId);

        $balls = $server->biitems()->lists('item')->toArray();

        do{
            $randomNum = rand(1,75);
        }while(in_array($randomNum, $balls));

        return $randomNum;
    }

}

