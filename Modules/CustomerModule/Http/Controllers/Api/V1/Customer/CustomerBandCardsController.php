<?php

namespace Modules\CustomerModule\Http\Controllers\Api\V1\Customer;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Modules\CustomerModule\Entities\UserBankCard;
use Illuminate\Support\Facades\Validator;


class CustomerBandCardsController extends Controller
{

    public function index(){
        $user = auth('api')->user();
        $userBankCards = UserBankCard::where('user_id', $user->id)->get();
        return response()->json(response_formatter(DEFAULT_200, $userBankCards), 200);
    }
    public function store(Request $request)
    {
        $user = auth('api')->user();
        $validator = Validator::make($request->all(), [
            'card_name' =>'required|string',
            'card_type' =>'required|string',
            'card_number' =>'required|string|unique:user_bankcards,number',
            'card_company' =>'required|string',
            'token' =>'required|string',
            'create_token_date' =>'required',
            'update_token_date' =>'required',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $userBankCard = UserBankCard::create([
            'user_id' => $user->id,
            'name' => $request['card_name'],
            'type' => $request['card_type'],
            'number' => $request['card_number'],
            'company' => $request['card_company'],
            'token' => $request['token'],
            'create_token_date' => $request['create_token_date'],
            'update_token_date' => $request['update_token_date'],
        ]);
        return response()->json(response_formatter(DEFAULT_200, $userBankCard), 200);
    }


    public function delete($id){
        $userBankCard = UserBankCard::find($id);
        if (!$userBankCard) {
            return response()->json(response_formatter(DEFAULT_404), 200);
        }
        $userBankCard->delete();
        return response()->json(response_formatter(DEFAULT_200), 200);
    }
}
