<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserImageModel extends Model
{
    protected $table = 'user_images';

    protected $fillable = [

    ];

    public function setSoftDelete(){
        $this->deleted_at = date('Y-m-d H:i:s');
    }

    const IMAGE_TYPES = ['passport_img_1','passport_img_2','inn_img','snils_img','img_avatar','img_other'];
}
