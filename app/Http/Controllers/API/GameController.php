<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GameTurnsRequest;
use Illuminate\Support\Str;

class GameController extends Controller
{
    public function getCurrentPlayers($num_of_players, $first_player)
    {
        $allPlayers = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

        $index = array_search($first_player, $allPlayers);

        $newArr = [];
        $afterIndex = $num_of_players - $index;
        $i = 0;
        $start = 0;
        while ($num_of_players > 0) {
            if ($i < $afterIndex) {
                $newArr[] = $allPlayers[$index++];
                $i++;
            } else    $newArr[] = $allPlayers[$start++];

            $num_of_players--;
        }

        return collect($newArr);
    }


    public function getOrderedTurns($players, $rounds, $num_of_players)
    {
        $ordered_turns =  collect();
        $ordered_turns->push($players->all());
        $reverse = false;
        for ($i = 1; $i < $rounds; $i++) {
            if ($i % $num_of_players == 0 || $reverse) { //reverse
                $ordered_turns->push(array_reverse($ordered_turns[$i - $num_of_players]));
                $reverse = true;
            } else {
                $players->push($players->shift());
                $ordered_turns->push($players->all());
            }
        }

        return $ordered_turns;
    }


    /**
     * @OA\Post(
     * path="/api/game",
     * operationId="getTurns",
     * tags={"getTurns"},
     * summary="Game Turns",
     * description="Game Turns Here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               @OA\Property(property="playersNum", type="number"),
     *               @OA\Property(property="turnsNum", type="number"),
     *               @OA\Property(property="firstPlayer", type="number"),
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Get turns Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Get turns Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */


    public function getTurns(GameTurnsRequest $request)
    {
        try {
            $num_of_players = $request->playersNum ?? 3;
            $firstPlayer = $request->firstPlayer ? Str::upper($request->firstPlayer) : 'A';
            $rounds = $request->turnsNum ?? 3;

            $players = $this->getCurrentPlayers($num_of_players, $firstPlayer);
            $ordered_turns = $this->getOrderedTurns($players, $rounds, $num_of_players);

            return response()->json(['message' => 'Success',  'turns' => $ordered_turns->all()], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed',  'errors' => 'Sorry an error happened! Try again later.'], 422);
        }
    }
}
