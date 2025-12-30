<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlaygroundResource extends JsonResource
{

  public function toArray($request)
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'code' => $this->code,
      'discount' => $this->discount,
      'discount_factory' => $this->discount_factory,
      'items' => $this->items->map(function ($item) {
        return [
          'id' => $item->id,
          'name' => $item->name,
          'code' => $item->code,
          'picture' => $item->picture,
          'amount' => $item->amount,
          'amountFabric' => $item->amountFabric,
          'pivot_id' => $item->pivot->id,
          'quantity' => $item->pivot->quantity,
        ];
      }),
      'images' => $this->images->map(fn($img) => [
        'id' => $img->id,
        'url' => $img->url,
        'filename' => $img->filename,
        'is_thumb' => $img->is_thumb,
      ]),
    ];
  }
}
