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

class CreateWorkEntry
{
    /** @var UserRepositoryInterface */
    private $user_repository;

    /** @var WorkEntryRepositoryInterface */
    private $work_entry_repository;

    /**
     * CreateUser constructor.
     */
    public function __construct(
        UserRepositoryInterface $user_repository,
        WorkEntryRepositoryInterface $work_entry_repository
    ) {
        $this->user_repository = $user_repository;
        $this->work_entry_repository = $work_entry_repository;
    }

    /**
     * Create work entry.
     *
     * This call creates a work entry
     *
     * @OA\Response(response=201, description="Successful operation - Created")
     * @OA\Response(response=400, description="Bad request")
     * @OA\Response(response=409, description="This resource already exists")
     * @OA\Tag(name="WorkEntry")
     * @Security(name="Bearer")
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request): Response
    {
        return $this->createWorkEntry(json_decode($request->getContent()));
    }

    /**
     * @return JsonResponse
     */
    public function createWorkEntry(object $workEntry): Response
    {
        $response = new JsonResponse();
        if (!empty($workEntry->email) && filter_var($workEntry->email, FILTER_VALIDATE_EMAIL) && ($workEntry->startDate < $workEntry->endDate)) {
            $check_user = $this->user_repository->getUserByEmail($workEntry->email);
            if (!empty($check_user)) {
                // Store work entry data:
                $work_entry = $this->work_entry_repository->createWorkEntry((array) $workEntry,(string) $check_user[0]['id']);
                $code = Response::HTTP_CREATED;
                $response->setData(['content' => $work_entry]);
            } else {
                $code = Response::HTTP_NOT_FOUND;
                $data = ['app_code' => $code, 'message' => Response::$statusTexts[404]];
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
