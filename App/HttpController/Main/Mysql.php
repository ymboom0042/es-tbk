<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/11
 * Time: 15:17
 */
namespace App\HttpController\Main;

use App\Jwt\Jwt;
use App\Model\User;
use EasySwoole\Component\Di;
use EasySwoole\Http\AbstractInterface\Controller;

class Mysql extends Controller
{
    public function test()
    {
//        $res = User::create() -> get(['id' => 1]);
//
//        $di = Di::getInstance();
//
//        $di -> set('jwt', new Jwt());
//
//        $jwt = $di -> get('jwt');
//
////        $token = $jwt -> getToken($res);
//
//         $jwt ->deToken('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE1OTE4NzAxMTcsInN1YiI6bnVsbCwibmJmIjoxNTkxODY2NTE3LCJhdWQiOnsiaWQiOjEsIm5hbWUiOiLlsI_nlarojIQiLCJjcmVhdGVkX2F0IjoiMjAyMC0wNi0xMSAxNToyNToxOCIsInVwZGF0ZWRfYXQiOm51bGx9LCJpYXQiOjE1OTE4NjY1MTcsImp0aSI6IjAzYWRiNGIwMmM0ZDgxY2JjZDlmNzhkYzU4OGFlZDljIiwic3RhdHVzIjoxLCJkYXRhIjpudWxsfQ.dEMoEiP_jGwDvh5-tj-rNchz3M4iVLfOKuZTV61tnlM');
//
//        $di -> delete('jwt');

    }
}