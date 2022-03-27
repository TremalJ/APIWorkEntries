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

class EditWorkEntry
{
    /** @var WorkEntryRepositoryInterface */
    private $work_entry_repository;

    /** @var UserRepositoryInterface */
    private $user_repository;

    /**
     * CreateUser constructor.
     */
    public function __construct(
        WorkEntryRepositoryInterface $work_entry_repository,
        UserRepositoryInterface $user_repository
    ) {
        $this->work_entry_repository = $work_entry_repository;
        $this->user_repository = $user_repository;
    }

    /**
     * Create user.
     *
     * This call edits a user by an id
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
        return $this->editWorkEntry((string) $request->get('id'), json_decode($request->getContent()));
    }

    /**
     * @return JsonResponse
     */
    public function editWorkEntry(string $id, object $user): Response
    {
        $response = new JsonResponse();
        if (is_numeric($id) && !empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL) && ($user->startDate < $user->endDate)) {
            $check_work_entry = $this->user_repository->getUserByEmail($user->email);
            $check_user = $this->work_entry_repository->getWorkEntryById($id);
            if (!empty($check_user) && !empty($check_work_entry)) {
                // Store user data:
                $user = $this->work_entry_repository->editWorkEntry($id, (array) $user);
                $code = Response::HTTP_CREATED;
                $response->setData(['content' => $user]);
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
