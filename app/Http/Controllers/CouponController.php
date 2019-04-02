<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Coupon;
use App\UserCoupon;
/*use Illuminate\Support\Facades\DB;*/

class CouponController extends Controller
{
    /**
     * Метод, отдающий json с описанием ошибки и её статусом
     * @param $text
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    private function fail($text, $status = 400){
        return response()->json(array( 'status' => 'fail', 'text' => $text), $status);
    }

    /**
     * Метод работы с входными данными
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function set(Request $request){
        $data = $request->input('data');
        if(empty($data['coupons']) && empty($data['user_coupons'])) return $this->fail('Данные о купонах не переданы');

        $date_pattern = '/(\d{1,2})\.(0[1-9]{1}|1[1-2]{1})\.(\d{4})/';
        if(!empty($data['coupons'])){
            Coupon::where('id', '>', 0)->delete();

            foreach ($data['coupons'] as $coupon){
                if(!is_array($coupon)) $coupon = (array) $coupon;

                if(empty($coupon['barcode']) || !preg_match('/\d{13}/', $coupon['barcode']))
                    return $this->fail('Невернвый штрих-код ('.$coupon['barcode'].')');
                if(empty($coupon['name']) || strlen($coupon['name']) > 100)
                    return $this->fail('Слишком длинное наименование акции');
                if(empty($coupon['offer']) || iconv_strlen($coupon['offer']) > 20)
                    return $this->fail('Слишком длинное обозначение скидки ('.$coupon['offer'].')');
                if(empty($coupon['start_date']) || !preg_match($date_pattern, $coupon['start_date']))
                    return $this->fail('Не валидная дата начала акции ('.$coupon['start_date'].')');
                if(!empty($coupon['finish_date']) && !preg_match($date_pattern, $coupon['finish_date']))
                    return $this->fail('Не валидная дата окончания акции ('.$coupon['finish_date'].')');

                $coupon['start_date'] = date('Y-m-d', strtotime($coupon['start_date']));
                $coupon['finish_date'] = date('Y-m-d', strtotime($coupon['finish_date']));

                Coupon::create($coupon);
            }
        }
        if(!empty($data['user_coupons'])){
            UserCoupon::where('id', '>', 0)->delete();

            foreach ($data['user_coupons'] as $user){
                if(empty($user['login']) || !preg_match('/7\d{10}/', $user['login'])) return $this->fail('Неправильный номер телефона ('.$user['login'].')');

                foreach ($user['coupons'] as $coupon){
                    if(!is_array($coupon)) $coupon = (array) $coupon;

                    if(empty($coupon['barcode']) || !preg_match('/\d{13}/', $coupon['barcode']))
                        return $this->fail('Невернвый штрих-код('.$coupon['barcode'].')');
                    if(!is_bool($coupon['active']))
                        return $this->fail('Активность купона '.$coupon['barcode'].' не указана');

                    $coupon['login'] = $user['login'];
                    UserCoupon::create($coupon);
                }
            }
        }

        return response()->json(array( 'status' => 'ok', 'text' => 'Данные сохранены'), 200);
    }

    /**
     * Тестовый метод. Возвращает массив для тестирования
     * @return \Illuminate\Http\JsonResponse
     */
    public function test(){
        return response()->json(
            array(
                'data' => array(
                    'coupons' => array(
                        array(
                            'barcode'       => '1234567891012',
                            'name'          => 'Акция №1',
                            'offer'         => 'Первое описание',
                            'image_id'      => '',
                            'start_date'    => '31.01.2017',
                            'finish_date'   => '04.01.2018'
                        ),
                        array(
                            'barcode'       => '4589652237481',
                            'name'          => 'Акция №2',
                            'offer'         => 'Второе описание',
                            'image_id'      => '3',
                            'start_date'    => '24.08.2018',
                            'finish_date'   => '24.09.2018'
                        ),
                        array(
                            'barcode'       => '6532489572354',
                            'name'          => 'Акция №3',
                            'offer'         => 'Третье описание',
                            'image_id'      => '94',
                            'start_date'    => '07.03.2019',
                            'finish_date'   => '09.03.2019'
                        )
                    ),
                    'user_coupons' => array(
                        array(
                            'login'     => '78965523698',
                            'coupons'   => array(
                                array(
                                    'barcode' => '5562874510224',
                                    'active'  => true
                                ),
                                array(
                                    'barcode' => '9048570300189',
                                    'active'  => false
                                )
                            )
                        ),
                        array(
                            'login'     => '79651123696',
                            'coupons'   => array(
                                array(
                                    'barcode' => '8524475912287',
                                    'active'  => true
                                )
                            )
                        )
                    )
                )
            ), 200);
    }
}
