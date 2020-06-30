<?php


namespace App\Service\v1;

use App\Helpers\Errors\CustomErrorLogger;
use App\Helpers\Http\PaginateHelper;
use App\Repositories\v1\RequestsRepository;
use App\Repositories\v1\UsersRepository;
use Illuminate\Http\Request;
use Validator;
use App\Helpers\General\ResponseObject;
use Storage;
use File;

class BaseService
{




    public function sendError($error_code, $messages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'error' => true,
            'result_code' => $error_code,
        ];

        if(!empty($messages)){
            $response['data'] = $messages;
        }
        return response()
            ->json($response, $code);
    }

    public function saveModel($model)
    {
        $result['success'] = true;

        if (!$model->save()) {
            $result['success'] = false;
        }

        if ($result['success'] === true) {
            $result['id'] = $model->id;
        }

        return $result;
    }


    public function baseChangeBalanceUsers($user_id,$sum,$type)
    {

        $user_repo = new UsersRepository();

        $user_model = $user_repo->getSingleClear(['where' => ['users.id' => $user_id]]);

        switch ($type){
            case 'DEBIT':  $user_model->balance -=  $sum; break;
            case 'REPLENISHMENT':  $user_model->balance +=  $sum; break;
        }

        $change_balance = $user_model->save();
        return $change_balance;

    }



    public function getWithPagination($query){
        $model = $query->paginate(PaginateHelper::COUNT_PAGINATE);
        $modelNew = $model->toArray();
        $data['items'] = $modelNew['data'];
        $data['pagination']['current_page'] = $modelNew['current_page'];
        $data['pagination']['total_item_count'] = $modelNew['total'];
        $data['pagination']['last_page'] = $modelNew['last_page'];
        $data['pagination']['per_page'] = $modelNew['per_page'];
        $data['pagination']['from'] = $modelNew['from'];
        $data['pagination']['to'] = $modelNew['to'];
        $data['pagination']['prev_page'] = $modelNew['prev_page_url'];
        $data['pagination']['next_page'] = $modelNew['next_page_url'];
        return $data;
    }

    public function saveItem($model,$params){
        $response_object = new ResponseObject();
        $model->fill($params);
        $save = $this->saveModel($model);
        if(!$save['success']){
            $response_object->setIsError('ERROR_NOT_CREATED_SUCCESS', [], ['item not created']);
            return $response_object;
        }
        $response_object->
        setIsSuccess('OK', ['item_id' => $save['id'],], ['item  create success']);
        return $response_object;
    }


    public function updateItem($repo,$ar_query,$params){

       $response_object = new ResponseObject();
        $query = $repo->constructorQuery($ar_query, 'CLEAR');
        $model = $query->first();
        if(is_null($model)){
            $response_object->setIsError('NOT_EXIST_ITEM',['message'=> 'not exist items with params ','ar_query'=>$ar_query],['message'=> 'not exist items with params ','ar_query'=>$ar_query]);
            return $response_object;
        }
        $model->fill($params);
        $save = $this->saveModel($model);
        if($save['success']){
            $response_object->setIsSuccess('OK',['id'=> $save['id']],['success updated']);
        }
        else{
            $response_object->setIsError('NOT UPDATED',['ar_query'=>$ar_query],['error not updated']);
        }
        return $response_object;
    }


    public function deleteItem($repo,$ar_query){

        $response_object = new ResponseObject();
        $query = $repo->constructorQuery($ar_query,'CLEAR');
        $model = $query->first();
        if(is_null($model)){
            $response_object->setIsError('NOT_EXIST_ITEM',['message'=> 'not exist items with params ','ar_query'=>$ar_query],['message'=> 'not exist items with params ','ar_query'=>$ar_query]);
            return $response_object;
        }
        $model->deleted_at = date('Y-m-d H:i:s');
        $save = $this->saveModel($model);
        if($save['success']){
            $response_object->setIsSuccess('OK',['id'=> $save['id']],['success deleted']);
        }
        else{
            $response_object->setIsError('NOT DELETED',['Ошибка при удалении'],['Ошибка при удалении']);
        }
        return $response_object;
    }

    public function validateImages($data)
    {
        $data = ['file' => $data];
        $pattern = ['file' => 'mimes:jpeg,jpg,png,gif|max:10000'];
        $messages = [
            'file.mimes' => 'Неверный формат файла 1',
        ];
        $validator = $this->validator($pattern, $data,false,$messages);
        return $validator;
    }

    function rusToTranslit($string) {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );
        return strtr($string, $converter);
    }


    public function validateDocuments($data)
    {
        $data = ['file' => $data];
        $pattern = [
            'file' => 'mimes:pdf,doc,docx,xlsx,xls,xlsm,pptx,ppt,pptx,odt,ods,odp,jpeg,jpg,png,gif,mp3,zip,7z,7-Zip,tar,wav|max:10000'
        ];
        $messages = [
            'file.mimes' => 'Неверный формат файла 2',
        ];
        $validator = $this->validator($pattern, $data, false,$messages);
        return $validator;
    }

    public function baseUploadImage($file, $type,$directory) {

        $response = new ResponseObject();
        $response->success = true;
        switch ($type) {
            case 'IMAGE':
                if (!$this->validateImages($file)->success) {
                    return $this->validateImages($file);
                }
                break;
            case 'DOCUMENT':
                if (!$this->validateDocuments($file)->success) {

                    return $this->validateDocuments($file);
                }
                break;
            default :
                $response->setIsError('TYPE_UPLOAD_NOT_CORRECT', [], ['Type not correct']);
                return $response;
        }
        if (empty($file)) {
            $response->setIsError('FILE_EMPTY', [], ['you send empty data']);
            return $response;
        }

        $extension = $file->getClientOriginalExtension();
        $mime_type = $file->getClientMimeType();
        $filename =  $file->getClientOriginalName();
        $path = '/img/'.$directory. md5($filename) . '.' . $extension;
        $saveFile = Storage::disk('public')->put($path, File::get($file));
        $path = '/uploads'.$path;
        if (!$saveFile) {
            $response->setIsError('IMAGE_NOT_UPLOADED', [], ['Image was not uploaded']);
            return $response;
        }
        $data_result = [
            'name' => $filename,
            'type' => $type,
            'mime_type' => $mime_type,
            'url' => $path,
        ];
        $response->setIsSuccess('IMAGE_UPLOADED', $data_result, ['Image is uploaded']);
        return $response;
    }

    public function validator($pattern, $data,$required, $messages = [],$exclusion = [])
    {

        /** VALIDATION */
        $response = new ResponseObject();
        if(count($data) == 0){
            $response->setIsError('EMPTY_DATA', [], ['you_send_empty_data']);
            return $response;
        }
        if($required){
            foreach ($pattern as $k => $v){
                array_unshift($v,'required');
                $pattern[$k] = $v;
            }
        }
        if(count($exclusion) > 0){
            $pattern = array_merge($pattern,$exclusion);
        }
        $validation = Validator::make($data, $pattern, $messages);
        if ($validation->fails()) {

            $response->setIsError('validate_error', [], $validation->errors()->toArray());
        } else {
            $response->setIsSuccess('OK', [], ['VALIDATE_SUCCESS']);
        }
        return $response;

    }

    public function proveOnExistItems($repo, $param)
    {
        $result = true;
        $items = $repo->generateFullQuery()->where([$param])->first();
        if (is_null($items)) {
            $result = false;
        }
        return $result;
    }

}
