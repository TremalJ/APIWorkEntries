<?php

declare(strict_types=1);

namespace App\Application\Action\User;

use App\Domain\RepositoryInterface\UserRepositoryInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteUser
{
    /** @var UserRepositoryInterface */
    private $user_repository;

    /**
     * DeleteUser constructor.
     */
    public function __construct(
        UserRepositoryInterface $user_repository
    ) {
        $this->user_repository = $user_repository;
    }

    /**
     * Delete user.
     *
     * This call removes a user passing him his id.
     *
     * @OA\Response(response=200, description="Successful operation")
     * @OA\Response(response=400, description="Bad request")
     * @OA\Response(response=404, description="Resource not found")
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request): Response
    {
        return $this->deleteUser((string) $request->get('id'));
    }

    public function deleteUser(string $id): JsonResponse
    {
        $response = new JsonResponse();
        $user = [];

        if (null != $id && is_numeric($id)) {
            $user = $this->user_repository->getUserById($id);
        }

        // Delete User:
        if (!empty($user)) {
            $this->user_repository->deleteUserById($id, $user);
            $code = Response::HTTP_OK;
            $response->setData(['content' => Response::$statusTexts[200]]);
        } else {
            $code = Response::HTTP_NOT_FOUND;
            $data = ['app_code' => $code, 'message' => 'Resource not found'];
            $response->setData(['error' => $data]);
        }

        return new JsonResponse($response->getContent(), $code, [], true);
    }
}
