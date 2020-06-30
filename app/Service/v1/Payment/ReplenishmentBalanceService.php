<?php


namespace App\Service\v1\Payment;


use App\Helpers\General\ResponseObject;
use App\Models\ReplenishmentModel;
use App\Models\TransactionsModel;
use App\Service\v1\BaseService;
use App\Services\v1\PaymentService;
use App\StaticClasses\ProfileStatic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReplenishmentBalanceService extends BaseService
{

    public function prepareRequestData($data)
    {
        $request_data['mrh_login'] = config('app.payment_login');
        $request_data['mrh_pass1'] = config('app.payment_password_1');
        $request_data['mrh_pass2'] = config('app.payment_password_2');
        $request_data['description'] = config('app.payment_description');
        $request_data['inv_id'] = $data['inv_id'];
        $request_data['sum'] = $data['sum'];
        $request_data['is_test'] = config('app.payment_is_test');
        $prepare_string_md5 =
            $request_data['mrh_login'] .
            ':' . $request_data['sum'] .
            ':' . $request_data['inv_id'] .
            ':' . $request_data['mrh_pass1'];
        $request_data['crc'] = md5($prepare_string_md5);

        $request_data['url'] = 'https://auth.robokassa.ru/Merchant/Index.aspx?';
        $request_data['url'] = $request_data['url'] . 'MerchantLogin=' . $request_data['mrh_login'];
        $request_data['url'] = $request_data['url'] . '&OutSum=' . $request_data['sum'];
        $request_data['url'] = $request_data['url'] . '&InvoiceID=' . $request_data['inv_id'];
        $request_data['url'] = $request_data['url'] . '&Description=' . $request_data['description'];
        $request_data['url'] = $request_data['url'] . '&SignatureValue=' . $request_data['crc'];
        $request_data['url'] = $request_data['url'] . '&IsTest=' . $request_data['is_test'];
        $request_data['sum'] = number_format($request_data['sum'], 6, '.', '');
        $request_data['confirm_crc_1'] = strtoupper(md5($request_data['sum'] . ':' . $request_data['inv_id'] . ':' . $request_data['mrh_pass1']));
        $request_data['confirm_crc_2'] = strtoupper(md5($request_data['sum'] . ':' . $request_data['inv_id'] . ':' . $request_data['mrh_pass2']));
        $request_data['user_id'] = ProfileStatic::$user_id;
        return $request_data;
    }

    public function validation($data,$required = true)
    {
        $pattern = [
            'sum' => ['string'],
            //'inv_id' => ['integer']
        ];
        return $this->validator($pattern,$data,$required);
    }


    public function createRequest(Request $request)
    {
        $response = new ResponseObject();
        $replenishment_model = new ReplenishmentModel();
        $data = $request->all();
        $validator = $this->validation($data);
        if(!$validator->success){
            return $validator;
        }
        $save = $replenishment_model->save();

        // если ошибка при ПЕРВОНАЧАЛЬНОМ сохранении
        if (!$save) {
            $response->setIsError('ERROR');
            return $response;
        }

        $data['inv_id'] = $replenishment_model->id;
        $request_data = $this->prepareRequestData($data);
        $replenishment_model->fill($request_data);

        // открываем транзакцию
        DB::beginTransaction();

        $save = $replenishment_model->save();

        // если ошибка при ПОЛНОМ сохранении
        if (!$save) {
            // если есть ошибки, откатываем
            DB::rollBack();

            $response->setIsError('ERROR');
            return $response;
        }
        else{

            $transactions_model = new TransactionsModel();
            $transactions_model->initiator = '{"reason":"Пополнение баланса"}';
            $transactions_model->user_id = ProfileStatic::$user_id;
            $transactions_model->type = 'PLUS';
            $transactions_model->sum = $replenishment_model->sum;
            $transactions_model->status = true;
            $transactions_model->flag_completed = true;
            $transactions_model->save();

            $user_model = ProfileStatic::$user;
            $user_model->balance = $user_model->balance + intval($transactions_model->sum);
            $user_model->save();

            // если всё удачно, то сохраняем
            DB::commit();
        }

        $response->setIsSuccess('SUCCESS', ['item' => ['url' => $replenishment_model->url]]);
        return $response;

    }


}
