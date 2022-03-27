<?php

declare(strict_types=1);

namespace App\Application\Action\User;

use App\Domain\RepositoryInterface\UserRepositoryInterface;
use App\Infraestructure\Hooks\validations;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListUser
{
    /** @var UserRepositoryInterface */
    private $user_repository;

    /** @var validations */
    private $validations;

    /**
     * ListUser constructor.
     */
    public function __construct(
        UserRepositoryInterface $user_repository,
        validations $validations
    ) {
        $this->user_repository = $user_repository;
        $this->validations = $validations;
    }

    /**
     * List users.
     *
     * This call returns the list of users.
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
        return $this->getListUsers((string) $request->get('id'));
    }

    public function getListUsers(string $id): JsonResponse
    {
        $response = new JsonResponse();
        $code = Response::HTTP_OK;

        if(null != $id) {
            !is_numeric($id) ? $code = Response::HTTP_BAD_REQUEST : $users = $this->user_repository->getUserById($id);
        } else{
            $users = $this->user_repository->getAllUsers();
        }

        if (Response::HTTP_BAD_REQUEST !== $code) {
            if (!empty($users)) {
                $response->setData(['content' => $users]);
            } else {
                $data = ['app_code' => Response::HTTP_NOT_FOUND, 'message' => Response::$statusTexts[404]];
                $response->setData(['error' => $data]);
            }
        } else {
            $data = ['app_code' => Response::HTTP_BAD_REQUEST, 'message' => Response::$statusTexts[400]];
            $response->setData(['error' => $data]);
        }

        return new JsonResponse($response->getContent(), $code, [], true);
    }
}
