<div style="margin: 0; padding: 0;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 10px 0 30px 0;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border: 1px solid #cccccc; border-collapse: collapse;">
                    <tr>
                        <td align="center" bgcolor="#00a8ff" style="padding: 40px 0 30px 0; color: #ffffff; font-size: 28px; font-weight: bold; font-family: Arial, sans-serif;">
                            <img src="https://api.finexpert.online/images/email-header.jpg" alt="Email header image" width="600" height="300" style="display: block;" />
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="color: #153643; font-family: Arial, sans-serif; font-size: 24px;">
                                        <b>Заказ №{{ $data['order']['global_number'] }} оплачен</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="padding-top: 20px; padding-bottom: 10px;  color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px; font-weight: bold;">
                                        Покупатель
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
                                        {{ $data['user']['last_name'] }} {{ $data['user']['first_name'] }} {{ $data['user']['second_name'] }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
                                        Телефон: {{ $data['user']['phone'] }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="padding-bottom: 30px; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
                                        Email: {{ $data['user']['email'] }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="padding-bottom: 10px; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px; font-weight: bold;">
                                        Список услуг:
                                    </td>
                                </tr>
                                <tr style="margin-bottom: 5px; border-bottom: 1px dotted #ccc;">
                                    <td style="padding: 10px 0 10px 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px; border-bottom: 1px dotted #ccc;">
                                        Название услуги
                                    </td>
                                    <td style="padding: 10px 0 10px 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px; border-bottom: 1px dotted #ccc;">
                                        Цена за 1
                                    </td>
                                    <td style="min-width:60px; padding: 10px 10px 10px 10px; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px; border-bottom: 1px dotted #ccc;">
                                        Кол-во
                                    </td>
                                    <td style="padding: 10px 0 10px 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px; border-bottom: 1px dotted #ccc;">
                                        Сумма
                                    </td>
                                </tr>
                                <tr style="margin-bottom: 10px;">

                                    @foreach($data['order_items'] as $item)
                                        <td style="padding: 20px 0 30px 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
                                            {{ $data['services'][$item['service_id']]['name'] }}
                                        </td>
                                        <td style="padding: 20px 0 30px 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
                                            {{ $item['price'] }} руб
                                        </td>
                                        <td style="padding: 20px 0 30px 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
                                            {{ $item['count'] }} шт
                                        </td>
                                        <td style="padding: 20px 0 30px 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
                                            {{ $item['total_sum_by_item'] }} руб
                                        </td>
                                    @endforeach

                                </tr>
                                <tr>
                                    <td style="padding: 20px 0 30px 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px; font-weight: bold;">
                                        Сумма заказа: {{ $data['order']['total_sum'] }} руб
                                        <br/>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#ee4c50" style="padding: 30px 30px 30px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="color: #ffffff; font-family: Arial, sans-serif; font-size: 14px;" width="75%">
                                        &reg; Финансовые эксперты, 2020<br/>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>