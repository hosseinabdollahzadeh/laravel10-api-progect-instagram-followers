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
