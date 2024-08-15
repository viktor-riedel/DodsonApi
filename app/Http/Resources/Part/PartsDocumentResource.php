<?php

namespace App\Http\Resources\Part;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartsDocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'folder' => $this->carPdr->car->make . ' ' .
                $this->carPdr->car->model . ' ' .
                $this->carPdr->car->chassis . ' ' .
                $this->carPdr->car->modifications->generation . ' ' .
                $this->carPdr->car->modifications->years_string,
            'id' => $this->id,
            'make' => $this->carPdr->car->make,
            'model' => $this->carPdr->car->model,
            'chassis' => $this->carPdr->car->chassis,
            'generation' => $this->carPdr->car->modifications->generation,
            'contr_agent' => $this->carPdr->car->contr_agent_name,
            'header' => $this->carPdr->car->modifications->header,
            'created_by' => $this->carPdr->car->createdBy->name,
            'parts_for_sale' => $this->carPdr->car->carFinance->parts_for_sale,
            'item_name_eng' => $this->item_name_eng,
            'item_name_ru' => $this->item_name_ru,
            'ic_number' => $this->ic_number,
            'ic_description' => $this->ic_description,
            'buying_price' => $this->card->priceCard->buying_price,
            'selling_price' => $this->card->priceCard->buying_price,
            'client' => $this->client,
        ];
    }
}
