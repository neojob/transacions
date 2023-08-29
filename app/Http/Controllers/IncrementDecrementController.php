<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class IncrementDecrementController extends Controller
{
    public function actions()
    {
        $user_id = auth()->id();
        $user = User::find($user_id);
        $other_users = User::whereNotIn('id',[$user_id, 1])->get();

        return view('manipulations',compact('other_users','user'));
    }

    /**
     * @param Request $request
     * @return void
     * @throws \Exception
     * Logged user adds money from his balance to another user balance
     */
    public function increment(Request $request) :RedirectResponse
    {
        $amount = $request->price;
        $other_user_id = $request->other_user_id;

        $logged_user_id = auth()->id();
        $logged_user = User::find($logged_user_id);
        $other_user = User::find($other_user_id);

        if(!$logged_user || !$other_user) abort(404);
        $logged_user_balance = $logged_user->balance;
        if($logged_user_balance < $amount) throw new \Exception('You have not enough money!');

        try{
            DB::beginTransaction();

            $logged_user->balance -= $amount;
            $logged_user->save();

            $other_user->balance += $amount;
            $other_user->save();

            $data = [
                'user_id' => $logged_user_id,
                'amount' => $amount,
                'type' => 'decrement',
            ];
            $transaction1 = new Transaction();
            $transaction1->fill($data);
            $transaction1->save();

            $data = [
                'user_id' => $other_user_id,
                'amount' => $amount,
                'type' => 'increment',
            ];
            $transaction2 = new Transaction();
            $transaction2->fill($data);
            $transaction2->save();

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
        }

        return redirect()->route('actions.balance');
    }
    /**
     * @param Request $request
     * @return void
     * @throws \Exception
     * Logged user gets money from another user to his balance
     */
    public function decrement(Request $request):RedirectResponse
    {
        $amount = $request->price;
        $other_user_id = $request->other_user_id;

        $logged_user_id = auth()->id();
        $logged_user = User::find($logged_user_id);

        $other_user = User::find($other_user_id);

        if(!$logged_user || !$other_user) abort(404);

        $other_user_balance = $other_user->balance;
        if($other_user_balance < $amount) throw new \Exception('The user have not enough money!');

        try{
            DB::beginTransaction();

            $logged_user->balance += $amount;
            $logged_user->save();

            $other_user->balance -= $amount;
            $other_user->save();

            $data = [
                'user_id' => $logged_user_id,
                'amount' => $amount,
                'type' => 'increment',
            ];
            $transaction1 = new Transaction();
            $transaction1->fill($data);
            $transaction1->save();

            $data = [
                'user_id' => $other_user_id,
                'amount' => $amount,
                'type' => 'decrement',
            ];
            $transaction2 = new Transaction();
            $transaction2->fill($data);
            $transaction2->save();

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
        }
        return redirect()->route('actions.balance');
    }
}
