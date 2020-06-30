<?php
/**
 * Created by PhpStorm.
 * User: XpoHo
 * Date: 15.08.2019
 * Time: 15:05
 */
namespace App\StaticClasses;

use App\Repositories\v1\UsersRepository;

class ProfileStatic
{
    //
    public static $user_id = null;
    public static $user_role_code = null;
    public static $ref_code = null;
    //
    public static $user = null;
    public static $flag_active = null;

    function __construct()
    {
        $user_repo = new UsersRepository();


        if(\request()->user() != NULL){
            $user_id = \request()->user()->id;

            $model_user = $user_repo->getSingleClear(['where' => ['users.id' => $user_id]]);

            if($model_user != null){
                ProfileStatic::$user_id = $model_user->id;
                ProfileStatic::$user = $model_user;
                ProfileStatic::$user_role_code = $model_user->role_code;
                ProfileStatic::$flag_active = $model_user->flag_active;
                ProfileStatic::$ref_code = $model_user->ref_code;

            }
        }
    }

    public static function IsAdmin(){
        $result = false;
        if(ProfileStatic::$user_role_code != null){
            if(ProfileStatic::$user_role_code == 'ADMIN'){
                $result = true;
            }
        }

        return $result;
    }

    public static function IsExpert(){
        $result = false;
        if(ProfileStatic::$user_role_code != null){
            if(ProfileStatic::$user_role_code == 'ADMIN' || ProfileStatic::$user_role_code == 'EXPERT'){
                $result = true;
            }
        }

        return $result;
    }
}
