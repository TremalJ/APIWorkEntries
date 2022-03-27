<?php

declare(strict_types=1);

namespace App\Application\Action\WorkEntry;

use App\Domain\RepositoryInterface\UserRepositoryInterface;
use App\Domain\RepositoryInterface\WorkEntryRepositoryInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListWorkEntryByUser
{
    /** @var UserRepositoryInterface */
    private $user_repository;

    /** @var WorkEntryRepositoryInterface */
    private $work_entry_repository;

    /**
     * ListUser constructor.
     */
    public function __construct(
        UserRepositoryInterface $user_repository,
        WorkEntryRepositoryInterface $work_entry_repository
    ) {
        $this->user_repository = $user_repository;
        $this->work_entry_repository = $work_entry_repository;
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
        return $this->getListWorkEntryByUser((string) $request->get('user_id'));
    }

    /**
     * List users.
     *
     * This call returns the list of work entries by user
     *
     * @OA\Response(response=200, description="Successful operation")
     * @OA\Response(response=400, description="Bad request")
     * @OA\Response(response=404, description="Resource not found")
     * @OA\Tag(name="WorkEntry")
     * @Security(name="Bearer")
     *
     * @return JsonResponse
     */
    public function getListWorkEntryByUser(string $user_id): JsonResponse
    {
        $response = new JsonResponse();
        $code = Response::HTTP_OK;

        if(null != $user_id) {
            !is_numeric($user_id) ? $code = Response::HTTP_BAD_REQUEST : $users = $this->user_repository->getUserById($user_id);
        } else{
            $users = $this->user_repository->getAllUsers();
        }

        if (Response::HTTP_BAD_REQUEST !== $code) {
            if (!empty($users)) {
                $workEntry = $this->work_entry_repository->getAllWorksEntryByUserId($user_id);
                $response->setData(['content' => $workEntry]);
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
