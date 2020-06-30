<?php
/**
 * Created by PhpStorm.
 * User: XpoHo
 * Date: 05.12.2019
 */

namespace App\Helpers\Errors;

class ErrorMessages
{
    const ERROR_MESSAGES_BY_CODE = [
        'ORDER_ITEM_NOT_ACCEPTED' => 'Заказ по услуге не подтверждён экспертом',
        'ORDER_ITEM_ALREADY_FINISHED' => 'Заказ по услуге уже завершен',
        'USER_BALANCE_NOT_ENOUGH_VALUE' => 'На балансе нет достаточной суммы для совершение оплаты',
        'PAYMENT_BILL_ITEM_ALREADY_PAYMENT' => 'Счет уже оплачен',
        'PAYMENT_BILL_ITEM_NOT_FOUND' => 'Счет с id=%s не найден',
        'ERROR_ON_CREATE_TRANSACTION_BY_PAYMENT_BILL_ITEM' => 'Не удалось создать оплатить счет с id=%s',

        // order item
        'USER_ROLE_IS_NOT_EXPERT' => 'Пользователь не является экспертом. Отказано в доступе.',
        'ORDER_ITEM_NOT_EXIST' => 'Заказ по услуге с id=%s не найден',
        'ORDER_ITEM_BY_USER_NOT_EXIST' => 'Заказ по услуге с id=%s для пользователя с id=%s не найден',
        'ERROR_ON_CHANGE_STATUS_TO_ACCEPTED_BY_EXPERT' => 'Не удалось обновить статус заказа по услуге с id=%s',
        'ERROR_ON_FINISH_ORDER_ITEM' => 'Не удалось завершить заказ по услуге с id=%s',
        'ORDER_ITEM_STATUS_NOT_AVAILABLE_TO_CHANGE' => 'Неверный статус заказа по услуге для изменения',
        'ORDER_ITEM_NOT_PAYED_FOR_FINISH_BY_CLIENT' => 'Заказ не оплачен для завершения со стороны клиента',

        // payment bill item
        'PAYMENT_BILL_ITEM_CREATE_NOT_VALID_PARAM' => 'Переданы неправильные параметры при создании счета на оплату',
        'ORDER_ITEMS_NOT_EXIST_IN_LIST' => 'Неправильно переданы параметры id заказа по услуге для создания счетов на оплату',
        'CREATE_PAYMENT_BILL_ITEM_SUM_EXCEED_DEBT_SUM' => 'Сумма счета bill_sum=%s для заказа по услуге id=%s превышает сумму долга dept_sum=%s',
        'PAYMENT_BILL_ITEMS_NOT_SAVED' => 'Ошибка при сохранении счетов на оплату',
        'PAYMENT_BILL_ITEM_NOT_ALREADY_PAYMENT' => 'Cчет не оплачен',

        //cancel transaction
        'NOT_CANCEL_TRANSACTIONS' => 'Ошибка в отмене траназкции',
        'TRANSACTION_NON_UNFREEZE' => 'Транзакция не разморожена',



        // profile
        'USER_NOT_FOUND' => 'Пользователь не найден',
        'IMAGE_NOT_FOUND_FOR_DELETE' => 'Изображение для удаления не найдено',
        'ERROR_ON_UPDATE_PROFILE' => 'Ошибка при сохранении данных профиля',

    ];

    public static function getMessageByCode($code, $message_args = []){
        $message = '';

        if(isset(ErrorMessages::ERROR_MESSAGES_BY_CODE[$code])){
            $message = ErrorMessages::ERROR_MESSAGES_BY_CODE[$code];
        }

        if(count($message_args) > 0){
            try{
                $message = vsprintf ($message, $message_args);
            }
            catch (\Exception $exception) {

            }
        }

        $message = str_replace('%s', '???', $message);

        return $message;
    }
}
