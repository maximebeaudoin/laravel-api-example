<?php

namespace App\Http\Controllers;

use App\Domain\User\Repository\UserRepository;
use App\Domain\User\Service\CreateUser;
use App\Domain\User\Service\DeleteUser;
use App\Domain\User\Service\UpdateUser;
use App\Domain\User\Transformer\UserTransformer;
use App\Domain\User\Validator\CreateUserValidator;
use App\Domain\User\Validator\UpdateUserValidator;
use EllipseSynergie\ApiResponse\Contracts\Response;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * UserController constructor.
     * @param Response $response
     * @param UserRepository $userRepository
     */
    public function __construct(Response $response, UserRepository $userRepository)
    {
        $this->response = $response;
        $this->userRepository = $userRepository;
    }

    /**
     * @return Response
     */
    public function index()
    {
        return $this->response->withCollection($this->userRepository->all(), new UserTransformer());
    }

    /**
     * @param Request $request
     * @param string $userId
     * @return Response
     */
    public function show(Request $request, $userId)
    {
        return $this->response->withItem($this->userRepository->find($userId), new UserTransformer());
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        (new CreateUserValidator())->validate($request->all());

        $user = app(CreateUser::class)->handle(
            $request->input('name'),
            $request->input('email'),
            $request->input('password')
        );

        return $this->response
            ->setStatusCode(SymfonyResponse::HTTP_CREATED)
            ->withItem($user, new UserTransformer());
    }

    /**
     * @param Request $request
     * @param string $userId
     * @return Response
     */
    public function update(Request $request, $userId)
    {
        $user = $this->userRepository->find($userId);

        // @todo fix validation for email update, maybe just unset the email if its the same with
        // the user before validating
        (new UpdateUserValidator())->validate($request->all());

        $user = app(UpdateUser::class)->handle(
            $user,
            $request->input('name', $user->name),
            $request->input('email', $user->email)
        );

        return $this->response
            ->setStatusCode(SymfonyResponse::HTTP_CREATED)
            ->withItem($user, new UserTransformer());
    }

    /**
     * @param Request $request
     * @param string $userId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function delete(Request $request, $userId)
    {
        $user = $this->userRepository->find($userId);

        app(DeleteUser::class)->handle($user);

        return response(null, SymfonyResponse::HTTP_NO_CONTENT);
    }
}
