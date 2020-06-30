<?php


namespace App\Service\v1\Payment;


use App\Helpers\General\ResponseObject;
use App\Models\ReplenishmentModel;
use App\Models\TransactionsModel;
use App\Models\WithdrawModel;
use App\Service\v1\BaseService;
use App\Services\v1\PaymentService;
use App\StaticClasses\ProfileStatic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawBalanceService extends BaseService
{

    public function prepareRequestData($data)
    {
        $request_data = [];
        $request_data['card_number'] = $data['card_number'];
        $request_data['sum'] = $data['sum'];
        $request_data['flag_close'] = false;
        $request_data['admin_email'] = config('app.admin_email');
        $request_data['user_id'] = ProfileStatic::$user_id;

        return $request_data;
    }

    public function validation($data,$required = true)
    {
        $pattern = [
            'card_number' => ['numeric', 'digits:16'],
            'sum' => ['numeric'],
        ];

        $messages = [
            'card_number.required' => 'Поле "Номер карты" обязательно для заполнения',
            'card_number.numeric' => 'Поле "Номер карты" должно содержать только цифры',
            'card_number.digits' => 'Поле "Номер карты" должно содержать 12 символов',
            'sum.required' => 'Поле "Сумма" обязательно для заполнения',
            'sum.numeric' => 'Поле "Сумма" должна состоять из цифр',
        ];


        return $this->validator($pattern, $data, $required, $messages);
    }


    public function createRequest(Request $request)
    {
        $response = new ResponseObject();
        $withdraw_model = new WithdrawModel();

        $data = $request->all();
        $validator = $this->validation($data);
        if(!$validator->success){
            return $validator;
        }


        $user_model = ProfileStatic::$user;
        if($user_model->balance - intval($data['sum']) < 0){
            $response->setIsError('ERROR', [], ['ERROR'=> ['Недостаточно средств на балансе']]);
            return $response;
        }

        $request_data = $this->prepareRequestData($data);
        $withdraw_model->fill($request_data);

        // открываем транзакцию
        DB::beginTransaction();

        $save_result = $withdraw_model->save();

        // если ошибка при ПЕРВОНАЧАЛЬНОМ сохранении
        if (!$save_result) {
            // если есть ошибки, откатываем
            DB::rollBack();

            $response->setIsError('ERROR');
            return $response;
        }
        else{

            $transactions_model = new TransactionsModel();
            $transactions_model->initiator = '{"reason":"Списание с баланса и вывод на карту"}';
            $transactions_model->user_id = ProfileStatic::$user_id;
            $transactions_model->type = 'MINUS';
            $transactions_model->sum = $withdraw_model->sum;
            $transactions_model->status = true;
            $transactions_model->flag_completed = true;
            $transactions_model->save();

            $user_model->balance = intval($user_model->balance) - intval($data['sum']);
            $user_model->save();

            // если всё удачно, то сохраняем
            DB::commit();
        }


        $response->setIsSuccess('SUCCESS', ['item' => ['message' => 'Заявка на вывод средств создана']]);
        return $response;

    }


}
