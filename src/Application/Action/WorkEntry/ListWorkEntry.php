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

class ListWorkEntry
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

    public function __invoke(Request $request): Response
    {
        return $this->getListWorkEntry((string) $request->get('id'));
    }

    /**
     * List users.
     *
     * This call returns the list of work entries
     *
     * @OA\Response(response=200, description="Successful operation")
     * @OA\Response(response=400, description="Bad request")
     * @OA\Response(response=404, description="Resource not found")
     * @OA\Tag(name="WorkEntry")
     * @Security(name="Bearer")
     *
     * @return JsonResponse
     */
    public function getListWorkEntry(string $id): JsonResponse
    {
        $response = new JsonResponse();
        $code = Response::HTTP_OK;

        if(null != $id) {
            !is_numeric($id) ? $code = Response::HTTP_BAD_REQUEST : $workEntry = $this->work_entry_repository->getWorkEntryById($id);
        }

        if (Response::HTTP_BAD_REQUEST !== $code) {
            if (!empty($workEntry)) {
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
