<?php

namespace App\Repositories\v1;

use App\Models\UsersModel;
use App\Repositories\v1\Base\BaseRepository;
use App\Services\v1\Permission\PermissionsService;

class UsersRepository extends BaseRepository
{
    const QUERY_FIELDS = [
        'ADMIN' => [
            'users.id as users_id',
            'users.first_name as users_first_name',
            'users.last_name as users_last_name',
            'users.second_name as users_second_name',

            'users.img as users_img',
            'users.description as users_description',

            'users.birthday as users_birthday',
            'users.phone as users_phone',
            'users.email as users_email',
            'users.alt_phone as users_alt_phone',
            'users.alt_email as users_alt_email',
            'users.role_code as users_role_code',
            'users.balance as users_balance',

            'users.place_actual_residence_city as users_place_actual_residence_city',

            /*
            'users.place_birth as users_place_birth',
            'users.address_city_name as users_address_city_name',
            'users.address_street_name as users_address_street_name',
            'users.address_building_number as users_address_building_number',
            'users.address_floor_number as users_address_floor_number',
            'users.place_of_work_company_name as users_place_of_work_company_name',
            'users.place_of_work_city_name as users_place_of_work_city_name',
            'users.place_of_work_start_year as users_place_of_work_start_year',
            'users.place_of_work_start_month as users_place_of_work_start_month',
            'users.place_of_work_position_name as users_place_of_work_position_name',

            'users.passport_series as users_passport_series',
            'users.passport_number as users_passport_number',
            'users.passport_issued_by as users_passport_issued_by',
            'users.passport_issued_date as users_passport_issued_date',
            'users.passport_issued_dept_code as users_passport_issued_dept_code',
            'users.passport_registration_address as users_passport_registration_address',
            'users.inn as users_inn',
            'users.snils as users_snils',
            'users.passport_img_1 as users_passport_img_1',
            'users.passport_img_2 as users_passport_img_2',
            'users.inn_img as users_inn_img',
            'users.snils_img as users_snils_img',

            'users.place_registration_postcode as users_place_registration_postcode',
            'users.place_registration_region as users_place_registration_region',
            'users.place_registration_city as users_place_registration_city',
            'users.place_registration_street as users_place_registration_street',
            'users.place_registration_building as users_place_registration_building',
            'users.place_registration_block as users_place_registration_block',
            'users.place_registration_apartment as users_place_registration_apartment',
            'users.place_registration_registration_date as users_place_registration_registration_date',
            'users.place_registration_name_homeowner as users_place_registration_name_homeowner',

            'users.place_actual_residence_postcode as users_place_actual_residence_postcode',
            'users.place_actual_residence_region as users_place_actual_residence_region',
            'users.place_actual_residence_street as users_place_actual_residence_street',
            'users.place_actual_residence_building as users_place_actual_residence_building',
            'users.place_actual_residence_block as users_place_actual_residence_block',
            'users.place_actual_residence_apartment as users_place_actual_residence_apartment',
            'users.place_actual_residence_duration_stay as users_place_actual_residence_duration_stay',
            'users.place_actual_residence_name_homeowner as users_place_actual_residence_name_homeowner',
            'users.place_actual_residence_flag_is_difference as users_place_actual_residence_flag_is_difference',

            'users.place_temporary_registration_postcode as users_place_temporary_registration_postcode',
            'users.place_temporary_registration_region as users_place_temporary_registration_region',
            'users.place_temporary_registration_city as users_place_temporary_registration_city',
            'users.place_temporary_registration_street as users_place_temporary_registration_street',
            'users.place_temporary_registration_building as users_place_temporary_registration_building',
            'users.place_temporary_registration_block as users_place_temporary_registration_block',
            'users.place_temporary_registration_apartment as users_place_temporary_registration_apartment',
            'users.place_temporary_registration_terms_registration as users_place_temporary_registration_terms_registration',
            'users.place_temporary_registration_name_homeowner as users_place_temporary_registration_name_homeowner',
            'users.place_temporary_registration_flag_is_difference as users_place_temporary_registration_flag_is_difference',

            'users.work_activity_name as users_work_activity_name',
            'users.work_activity_city as users_work_activity_city',
            'users.work_activity_year as users_work_activity_year',
            'users.work_activity_month as users_work_activity_month',
            'users.work_activity_position as users_work_activity_position',

            'users.contacts_phone_registration as users_contacts_phone_registration',
            'users.contacts_phone_residence as users_contacts_phone_residence',
            'users.contacts_phone_mobile as users_contacts_phone_mobile',
            'users.contacts_full_name as users_contacts_full_name',

            'users.contact_person_relation_degree as users_contact_person_relation_degree',
            'users.contact_person_birthday as users_contact_person_birthday',
            'users.contact_person_phone as users_contact_person_phone',
            'users.contact_person_address as users_contact_person_address',

            'users.marital_status_status as users_marital_status_status',
            'users.marital_status_last_name as users_marital_status_last_name',
            'users.marital_status_old_last_name as users_marital_status_old_last_name',
            'users.marital_status_first_name as users_marital_status_first_name',
            'users.marital_status_second_name as users_marital_status_second_name',
            'users.marital_status_birthday as users_marital_status_birthday',

            'users.additional_information_name as users_additional_information_name',
            'users.additional_information_year as users_additional_information_year',
            'users.additional_information_education as users_additional_information_education',
            */

            'users.ref_code as users_ref_code',
            'users.parent_user_id as users_parent_user_id',

            'review_to_users_summary.summary_rate as review_to_users_summary_summary_rate',
        ],
        'EXPERT' => [
            'users.id as users_id',
            'users.first_name as users_first_name',
            'users.last_name as users_last_name',
            'users.second_name as users_second_name',

            'users.img as users_img',
            'users.description as users_description',

            'users.birthday as users_birthday',
            'users.phone as users_phone',
            'users.email as users_email',
            'users.alt_phone as users_alt_phone',
            'users.alt_email as users_alt_email',
            'users.role_code as users_role_code',
            'users.balance as users_balance',

            'users.place_actual_residence_city as users_place_actual_residence_city',

            /*
            'users.place_birth as users_place_birth',
            'users.address_city_name as users_address_city_name',
            'users.address_street_name as users_address_street_name',
            'users.address_building_number as users_address_building_number',
            'users.address_floor_number as users_address_floor_number',
            'users.place_of_work_company_name as users_place_of_work_company_name',
            'users.place_of_work_city_name as users_place_of_work_city_name',
            'users.place_of_work_start_year as users_place_of_work_start_year',
            'users.place_of_work_start_month as users_place_of_work_start_month',
            'users.place_of_work_position_name as users_place_of_work_position_name',

            'users.passport_series as users_passport_series',
            'users.passport_number as users_passport_number',
            'users.passport_issued_by as users_passport_issued_by',
            'users.passport_issued_date as users_passport_issued_date',
            'users.passport_issued_dept_code as users_passport_issued_dept_code',
            'users.passport_registration_address as users_passport_registration_address',
            'users.inn as users_inn',
            'users.snils as users_snils',
            'users.passport_img_1 as users_passport_img_1',
            'users.passport_img_2 as users_passport_img_2',
            'users.inn_img as users_inn_img',
            'users.snils_img as users_snils_img',

            'users.place_registration_postcode as users_place_registration_postcode',
            'users.place_registration_region as users_place_registration_region',
            'users.place_registration_city as users_place_registration_city',
            'users.place_registration_street as users_place_registration_street',
            'users.place_registration_building as users_place_registration_building',
            'users.place_registration_block as users_place_registration_block',
            'users.place_registration_apartment as users_place_registration_apartment',
            'users.place_registration_registration_date as users_place_registration_registration_date',
            'users.place_registration_name_homeowner as users_place_registration_name_homeowner',

            'users.place_actual_residence_postcode as users_place_actual_residence_postcode',
            'users.place_actual_residence_region as users_place_actual_residence_region',
            'users.place_actual_residence_city as users_place_actual_residence_city',
            'users.place_actual_residence_street as users_place_actual_residence_street',
            'users.place_actual_residence_building as users_place_actual_residence_building',
            'users.place_actual_residence_block as users_place_actual_residence_block',
            'users.place_actual_residence_apartment as users_place_actual_residence_apartment',
            'users.place_actual_residence_duration_stay as users_place_actual_residence_duration_stay',
            'users.place_actual_residence_name_homeowner as users_place_actual_residence_name_homeowner',
            'users.place_actual_residence_flag_is_difference as users_place_actual_residence_flag_is_difference',

            'users.place_temporary_registration_postcode as users_place_temporary_registration_postcode',
            'users.place_temporary_registration_region as users_place_temporary_registration_region',
            'users.place_temporary_registration_city as users_place_temporary_registration_city',
            'users.place_temporary_registration_street as users_place_temporary_registration_street',
            'users.place_temporary_registration_building as users_place_temporary_registration_building',
            'users.place_temporary_registration_block as users_place_temporary_registration_block',
            'users.place_temporary_registration_apartment as users_place_temporary_registration_apartment',
            'users.place_temporary_registration_terms_registration as users_place_temporary_registration_terms_registration',
            'users.place_temporary_registration_name_homeowner as users_place_temporary_registration_name_homeowner',
            'users.place_temporary_registration_flag_is_difference as users_place_temporary_registration_flag_is_difference',

            'users.work_activity_name as users_work_activity_name',
            'users.work_activity_city as users_work_activity_city',
            'users.work_activity_year as users_work_activity_year',
            'users.work_activity_month as users_work_activity_month',
            'users.work_activity_position as users_work_activity_position',

            'users.contacts_phone_registration as users_contacts_phone_registration',
            'users.contacts_phone_residence as users_contacts_phone_residence',
            'users.contacts_phone_mobile as users_contacts_phone_mobile',
            'users.contacts_full_name as users_contacts_full_name',

            'users.contact_person_relation_degree as users_contact_person_relation_degree',
            'users.contact_person_birthday as users_contact_person_birthday',
            'users.contact_person_phone as users_contact_person_phone',
            'users.contact_person_address as users_contact_person_address',

            'users.marital_status_status as users_marital_status_status',
            'users.marital_status_last_name as users_marital_status_last_name',
            'users.marital_status_old_last_name as users_marital_status_old_last_name',
            'users.marital_status_first_name as users_marital_status_first_name',
            'users.marital_status_second_name as users_marital_status_second_name',
            'users.marital_status_birthday as users_marital_status_birthday',

            'users.additional_information_name as users_additional_information_name',
            'users.additional_information_year as users_additional_information_year',
            'users.additional_information_education as users_additional_information_education',
            */

            'users.ref_code as users_ref_code',
            'users.parent_user_id as users_parent_user_id',

            'review_to_users_summary.summary_rate as review_to_users_summary_summary_rate',
        ],
        'USER' => [
            'users.id as users_id',
            'users.first_name as users_first_name',
            'users.last_name as users_last_name',
            'users.second_name as users_second_name',

            'users.img as users_img',
            'users.description as users_description',

            'users.birthday as users_birthday',
            'users.phone as users_phone',
            'users.email as users_email',
            'users.alt_phone as users_alt_phone',
            'users.alt_email as users_alt_email',
            'users.role_code as users_role_code',
            'users.balance as users_balance',

            'users.place_actual_residence_city as users_place_actual_residence_city',

            /*
            'users.place_birth as users_place_birth',
            'users.address_city_name as users_address_city_name',
            'users.address_street_name as users_address_street_name',
            'users.address_building_number as users_address_building_number',
            'users.address_floor_number as users_address_floor_number',
            'users.place_of_work_company_name as users_place_of_work_company_name',
            'users.place_of_work_city_name as users_place_of_work_city_name',
            'users.place_of_work_start_year as users_place_of_work_start_year',
            'users.place_of_work_start_month as users_place_of_work_start_month',
            'users.place_of_work_position_name as users_place_of_work_position_name',

            'users.passport_series as users_passport_series',
            'users.passport_number as users_passport_number',
            'users.passport_issued_by as users_passport_issued_by',
            'users.passport_issued_date as users_passport_issued_date',
            'users.passport_issued_dept_code as users_passport_issued_dept_code',
            'users.passport_registration_address as users_passport_registration_address',
            'users.inn as users_inn',
            'users.snils as users_snils',
            'users.passport_img_1 as users_passport_img_1',
            'users.passport_img_2 as users_passport_img_2',
            'users.inn_img as users_inn_img',
            'users.snils_img as users_snils_img',

            'users.place_registration_postcode as users_place_registration_postcode',
            'users.place_registration_region as users_place_registration_region',
            'users.place_registration_city as users_place_registration_city',
            'users.place_registration_street as users_place_registration_street',
            'users.place_registration_building as users_place_registration_building',
            'users.place_registration_block as users_place_registration_block',
            'users.place_registration_apartment as users_place_registration_apartment',
            'users.place_registration_registration_date as users_place_registration_registration_date',
            'users.place_registration_name_homeowner as users_place_registration_name_homeowner',

            'users.place_actual_residence_postcode as users_place_actual_residence_postcode',
            'users.place_actual_residence_region as users_place_actual_residence_region',
            'users.place_actual_residence_street as users_place_actual_residence_street',
            'users.place_actual_residence_building as users_place_actual_residence_building',
            'users.place_actual_residence_block as users_place_actual_residence_block',
            'users.place_actual_residence_apartment as users_place_actual_residence_apartment',
            'users.place_actual_residence_duration_stay as users_place_actual_residence_duration_stay',
            'users.place_actual_residence_name_homeowner as users_place_actual_residence_name_homeowner',
            'users.place_actual_residence_flag_is_difference as users_place_actual_residence_flag_is_difference',

            'users.place_temporary_registration_postcode as users_place_temporary_registration_postcode',
            'users.place_temporary_registration_region as users_place_temporary_registration_region',
            'users.place_temporary_registration_city as users_place_temporary_registration_city',
            'users.place_temporary_registration_street as users_place_temporary_registration_street',
            'users.place_temporary_registration_building as users_place_temporary_registration_building',
            'users.place_temporary_registration_block as users_place_temporary_registration_block',
            'users.place_temporary_registration_apartment as users_place_temporary_registration_apartment',
            'users.place_temporary_registration_terms_registration as users_place_temporary_registration_terms_registration',
            'users.place_temporary_registration_name_homeowner as users_place_temporary_registration_name_homeowner',
            'users.place_temporary_registration_flag_is_difference as users_place_temporary_registration_flag_is_difference',

            'users.work_activity_name as users_work_activity_name',
            'users.work_activity_city as users_work_activity_city',
            'users.work_activity_year as users_work_activity_year',
            'users.work_activity_month as users_work_activity_month',
            'users.work_activity_position as users_work_activity_position',

            'users.contacts_phone_registration as users_contacts_phone_registration',
            'users.contacts_phone_residence as users_contacts_phone_residence',
            'users.contacts_phone_mobile as users_contacts_phone_mobile',
            'users.contacts_full_name as users_contacts_full_name',

            'users.contact_person_relation_degree as users_contact_person_relation_degree',
            'users.contact_person_birthday as users_contact_person_birthday',
            'users.contact_person_phone as users_contact_person_phone',
            'users.contact_person_address as users_contact_person_address',

            'users.marital_status_status as users_marital_status_status',
            'users.marital_status_last_name as users_marital_status_last_name',
            'users.marital_status_old_last_name as users_marital_status_old_last_name',
            'users.marital_status_first_name as users_marital_status_first_name',
            'users.marital_status_second_name as users_marital_status_second_name',
            'users.marital_status_birthday as users_marital_status_birthday',

            'users.additional_information_name as users_additional_information_name',
            'users.additional_information_year as users_additional_information_year',
            'users.additional_information_education as users_additional_information_education',
            */


            'users.ref_code as users_ref_code',
            'users.parent_user_id as users_parent_user_id',
            'review_to_users_summary.summary_rate as review_to_users_summary_summary_rate',
        ]
    ];

    public function __construct()
    {

    }


    /**
     * Получаем количество всех записей
     * @param array $arQuery
     * @return mixed
     */
    public function count($arQuery = ['count' => 9999])
    {
        $query = $this->generateFullQuery();

        $query = $this->addQueryParam($arQuery, $query);
        $result = $query->count();

        return $result;
    }

    /**
     * Формируем запрос со всеми полями
     * @return \Illuminate\Database\Query\Builder
     */
    public function generateFullQuery()
    {
        $query = UsersModel::whereNull('users.deleted_at');

        $query->leftJoin('review_to_users_summary', function ($join) {
            $join->on('review_to_users_summary.id', '=', 'users.id');
            $join->whereNull('review_to_users_summary.deleted_at');
            $join->where('review_to_users_summary.type_review', '=', 'FOR_EXPERT');
        });
      // $query = $query->leftJoin('user_images','user_images.user_id','=','users.id');

        $query_select_fields = PermissionsService::getQueryFields(UsersRepository::QUERY_FIELDS);
        $query->select($query_select_fields);

        return $query;
    }

    public function generateClearQuery()
    {
        $query = UsersModel::whereNull('users.deleted_at');
        $query->select([
            'users.*',
        ]);

        return $query;
    }


    /**
     * Получаем 1 запись
     * @param array $arQuery
     * @return mixed
     */
    public function getSingle($arQuery = [])
    {
        $query = $this->generateFullQuery();

        $query = $this->addQueryParam($arQuery, $query);
        $result = $query->first();

        return $result;
    }

    /**
     * Поиск товаров поставщика по запросу
     * @param array $arQuery
     * @return mixed
     */
    public function search($arQuery = [])
    {
        $query = $this->generateFullQuery();

        $query = $this->addQueryParam($arQuery, $query);
        $result = $query->get();

        return $result;
    }

}
