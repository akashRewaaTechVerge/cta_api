<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        if(is_countable($this->document) > 0)
        {
            foreach($this->document as $doc)
            {
                if($doc->type == 'document')
                {
                    $doc->document = asset('/document/'.$doc->document);
                }
                if($doc->type == 'license_and_passport')
                {
                    $doc->license_and_passport = asset('/license_and_passport/'.$doc->license_and_passport);
                }
                if($doc->type == 'passport')
                {
                    $doc->passport = asset('/passport/'.$doc->passport);
                }
            }
        }

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'created_by' => $this->created_by,
            'company_name' => $this->company_name,
            'company_address' => $this->company_address,
            'formation_type' => $this->formation_type,
            'state_of_formation' => $this->state_of_formation,
            'tin_ein_number' => $this->tin_ein_number,
            'foreign_based_company_us' => $this->foreign_based_company_us,
            'company_registration_number_or_code' => $this->company_registration_number_or_code,
            'country_of_formation' => $this->country_of_formation,
            'foreign_state_of_formation' => $this->foreign_state_of_formation,
            'foreign_tin_ein_number' => $this->foreign_tin_ein_number,
            'foreign_based_company' => $this->foreign_based_company,
            'foreign_company_registration_number_or_code' => $this->foreign_company_registration_number_or_code,
            'owner_name' => isset($this->projectOwner) ? $this->projectOwner->owner_name : null,    
            // 'license_and_passport' => $this->license_and_passport,
            // 'passport' => $this->passport,
            'dob' => isset($this->projectOwner) ? $this->projectOwner->dob : null, 
            'address' => isset($this->projectOwner) ? $this->projectOwner->address : null,
            'unique_type' => isset($this->projectOwner) ? $this->projectOwner->unique_type : null, 
            'document' => isset($this->document) ? $this->document : null,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
