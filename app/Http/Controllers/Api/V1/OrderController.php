<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\ApiController;
use App\Http\Resources\V1\OrderResource;
use Database\Repositories\UserRepo;
use Illuminate\Http\Request;
use Services\UserService;

class OrderController extends ApiController
{
    /**
     * @OA\Post(
     *     path="/api/v1/orders/follower-request",
     *     summary="Request followers",
     *     tags={"Orders"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *           name="follower_count",
     *           in="query",
     *           description="Follower Count",
     *           required=true,
     *           @OA\Schema(type="integer", default=10)
     *       ),
     *     @OA\Response(response="201", description="Followers ordered successfully"),
     *     @OA\Response(response="422", description="Your balance is low for this request!"),
     *     @OA\Response(response="401", description="Unauthenticated")
     * )
     */
    public function followerRequest(Request $request)
    {
        // Validate input
        $this->validate($request, [
            'follower_count' => 'required|integer|min:1'
        ]);

        // Get authenticated user
        $user = UserRepo::getUserByToken($request);
        if(!$user){
            return $this->errorResponse('Invalid API token!', 401);
        }

        $order = UserService::followerRequest($user, $request->follower_count);
        if($order){
            // Return response
            return $this->successResponse(new OrderResource($order), 'Followers ordered successfully.', 201);
        }else {
            return $this->errorResponse('Your balance is low for this request!', 422);
        }

    }
}
