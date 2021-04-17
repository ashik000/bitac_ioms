<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StationCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:1',
            'description' =>'required|min:1' ,
            'station_group_id' => 'required|exists:station_groups,id',
            'oee_threshold' => 'required'
        ];
    }
}
