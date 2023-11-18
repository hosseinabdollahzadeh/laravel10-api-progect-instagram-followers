<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\ApiController;
use App\Http\Resources\V1\UserResource;
use Database\Repositories\UserRepo;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Services\UserService;
/**
 * @OA\OpenApi(
 *     security={{"bearer_token": {}}},
 *     @OA\Components(
 *         @OA\SecurityScheme(
 *             type="http",
 *             scheme="bearer",
 *             securityScheme="bearer_token"
 *         )
 *     )
 * )
 */
class UserController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/users/followable-list",
     *     summary="Get followable users list",
     *     tags={"Users"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(response="200", description="Success"),
     *     @OA\Response(response="401", description="Unauthenticated")
     * )
     */
    public function followableList(Request $request)
    {
        // Validate input
        $this->validate($request, [
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1'
        ]);


        // Get authenticated user
        $user = UserRepo::getUserByToken($request);

        if(!$user){
            return $this->errorResponse('Invalid API token!', 401);
        }

        // Set pagination parameters
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 10);

        // Paginate followable users
        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $followableList = UserRepo::paginateFollowableUsers($user->id, $perPage);

        // Return response
        return $this->successResponse([
            'followableUsers' => UserResource::collection($followableList),
            'links' => UserResource::collection($followableList)->response()->getData()->links,
            'meta' => UserResource::collection($followableList)->response()->getData()->meta,
        ], null, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/users/follow-user",
     *     summary="Follow a user",
     *     tags={"Users"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *          name="followable_id",
     *          in="query",
     *          description="Followable Id",
     *          required=true,
     *          @OA\Schema(type="integer", default=1)
     *      ),
     *     @OA\Response(response="200", description="TYou are following successfully."),
     *     @OA\Response(response="400", description="Invalid followable user"),
     *     @OA\Response(response="500", description="Internal server error"),
     *     @OA\Response(response="401", description="Unauthenticated")
     * )
     */
    public function followUser(Request $request)
    {
        // Get authenticated user
        $follower = UserRepo::getUserByToken($request);

        $followableId = $request->followable_id;

        if (!UserRepo::validateFollowable($follower, $request->followable_id)) {
            return $this->errorResponse('Invalid followable user', 400);
        }
        try {
            $followableUser = UserService::followUser($follower, $followableId);
            return $this->successResponse(new UserResource($followableUser), 'You are following successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

}
