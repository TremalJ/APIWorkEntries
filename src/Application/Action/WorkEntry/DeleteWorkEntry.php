<?php

declare(strict_types=1);

namespace App\Application\Action\WorkEntry;

use App\Domain\RepositoryInterface\UserRepositoryInterface;
use App\Infraestructure\Repository\WorkEntryRepository;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteWorkEntry
{
    /** @var WorkEntryRepository */
    private $work_entry_repository;

    /** @var UserRepositoryInterface */
    private $user_repository;

    /**
     * DeleteUser constructor.
     */
    public function __construct(
        UserRepositoryInterface $user_repository,
        WorkEntryRepository $work_entry_repository
    ) {
        $this->user_repository = $user_repository;
        $this->work_entry_repository = $work_entry_repository;
    }

    /**
     * Delete user.
     *
     * This call removes a work entry passing the id
     *
     * @OA\Response(response=200, description="Successful operation")
     * @OA\Response(response=400, description="Bad request")
     * @OA\Response(response=404, description="Resource not found")
     * @OA\Tag(name="WorkEntry")
     * @Security(name="Bearer")
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request): Response
    {
        return $this->deleteWorkEntry((string) $request->get('id'));
    }

    public function deleteWorkEntry(string $id): JsonResponse
    {
        $response = new JsonResponse();
        $workEntry = [];

        if (null != $id && is_numeric($id)) {
            $workEntry = $this->work_entry_repository->getWorkEntryById($id);
        }

        // Delete Work Entry:
        if (!empty($workEntry)) {
            $this->work_entry_repository->deleteWorkEntryById($id, $workEntry);
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
