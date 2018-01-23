<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Game extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'player1' => $this->player1, //playerList[0]
            'player2' => $this->player2,
            'player3' => $this->player3,
            'player4' => $this->player4,
            'winner' => $this->winner,
            'winnerName' => $this->getWinnerName(),
        ];
        return parent::toArray($request);
    }
}
