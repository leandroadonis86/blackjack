<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class User extends Resource
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
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'nickname' => $this->nickname,
            'admin' => $this->admin,
            'blocked' => $this->blocked,
            'total_points' => $this->total_points,
            'total_games_played' => $this->total_games_played,
        ];
    }
}
