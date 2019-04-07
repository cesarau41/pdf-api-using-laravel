<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PDF_File extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        //filter solumns
        return [
            'id'=>$this->id,
            'title'=>$this->title,
            'filename'=>$this->filename,
            'user_id'=>$this->user_id,
        ];
    }

    public function with($request){
        return [
            'version' => '1.0.0',
            //'author_url' => url('https://google.com')
        ];
    }
}
