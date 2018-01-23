<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'player1',
        'player2',
        'player3',
        'player4',
        'joinedPlayers',
        'maxPlayers',
        'winner',
    ];
    public function getWinnerName()
    {
		if ($this->winner == 1) {
            return $this->player1;
        } else if ($this->winner == 2) {
            return $this->player2;
        } else if ($this->winner == 3) {
            return $this->player3;
        } else if ($this->winner == 4) {
            return $this->player4;
        } else if (is_null($this->winner)) {
            return '';
        } else if ($this->winner == 0) {
            return '';
        } 
        return "Unknown Winner";
    }
}
