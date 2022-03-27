<?php

declare(strict_types=1);

namespace App\Application\Action\User;

use App\Domain\RepositoryInterface\UserRepositoryInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateUser
{
    /** @var UserRepositoryInterface */
    private $user_repository;

    /**
     * CreateUser constructor.
     */
    public function __construct(
        UserRepositoryInterface $user_repository
    ) {
        $this->user_repository = $user_repository;
    }

    /**
     * Create user.
     *
     * This call creates a user
     *
     * @OA\Response(response=201, description="Successful operation - Created")
     * @OA\Response(response=400, description="Bad request")
     * @OA\Response(response=409, description="This resource already exists")
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request): Response
    {
        return $this->createUser(json_decode($request->getContent()));
    }

    /**
     * @return JsonResponse
     */
    public function createUser(object $user): Response
    {
        $response = new JsonResponse();
        if (!empty($user->email) && !empty($user->user_name) && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            $check_user = $this->user_repository->getUserByEmail($user->email);
            if (empty($check_user)) {
                // Store user data:
                $user = $this->user_repository->createUser((array) $user);
                $code = Response::HTTP_CREATED;
                $response->setData(['content' => $user]);
            } else {
                $code = Response::HTTP_CONFLICT;
                $data = ['app_code' => $code, 'message' => Response::$statusTexts[409]];
                $response->setData(['error' => $data]);
            }
        } else {
            $code = Response::HTTP_BAD_REQUEST;
            $data = ['app_code' => $code, 'message' => Response::$statusTexts[400]];
            $response->setData(['error' => $data]);
        }

        return new JsonResponse($response->getContent(), $code, [], true);
    }
}
