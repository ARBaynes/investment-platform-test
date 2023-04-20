<?php

namespace App\Application\Controller;

use App\Domain\Exceptions\Account\AccountAlreadyExistsException;
use App\Domain\Service\Account\AccountBalanceCheckService;
use App\Domain\Service\Account\AccountBalanceUpdaterService;
use App\Domain\Service\Account\AccountCreatorService;
use App\Domain\Service\Account\AccountRetrieverService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    public function __construct(
        private readonly AccountRetrieverService $accountRetrieverService,
        private readonly AccountCreatorService $accountCreatorService,
        private readonly AccountBalanceCheckService $accountBalanceCheckService,
        private readonly AccountBalanceUpdaterService $accountBalanceUpdaterService
    ) {
    }

    #[Route('/account', name: 'app_application_account_index')]
    public function index(): JsonResponse
    {
        return $this->json([
            'routes' => [
                'create-isa' => '/account/create-isa/{accountType}/{accountHolder}',
                'create-jisa' => '/account/create-jisa/{accountType}/{accountHolder}/{accountHolderBirthday[YYYY-mm-dd]}',
                'balance' => '/account/{accountType}/{accountHolder}/balance',
                'deposit' => '/account/{accountType}/{accountHolder}/deposit',
                'withdraw' => '/account/{accountType}/{accountHolder}/withdraw'
            ]
        ]);
    }

    #[Route('/account/create-isa/{accountHolder}', name: 'app_application_account_createisa')]
    public function createIsa(string $accountHolder): JsonResponse
    {
        $account = $this->accountCreatorService->createIsaAccount($accountHolder);

        if (!$account) {
            $this->json([
                'message' => 'Could not create account. Please contact your representative.',
            ]);
        }

        return $this->json([
            'message' => 'Account with id '. $account->getId() . ' created.',
            'path' => 'src/Application/Controller/AccountController.php',
        ]);
    }

    #[Route('/account/create-jisa/{accountHolder}/{accountHolderBirthday}', name: 'app_application_account_createjisa')]
    public function createJisa(string $accountHolder, string $accountHolderBirthday): JsonResponse
    {
        $account = $this->accountCreatorService->createJisaAccount($accountHolder, $accountHolderBirthday);

        if (!$account) {
            $this->json([
                'message' => 'Could not create account. Please contact your representative.',
            ]);
        }

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Application/Controller/AccountController.php',
        ]);
    }

    #[Route('/account/{accountType}/{accountHolder}/balance', name: 'app_application_account_balance')]
    public function balance(string $accountType, string $accountHolder): JsonResponse
    {
        $account = $this->accountRetrieverService->retrieve($accountType, $accountHolder);
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Application/Controller/AccountController.php',
        ]);
    }

    #[Route('/account/{accountType}/{accountHolder}/deposit', name: 'app_application_account_deposit')]
    public function deposit(string $accountType, string $accountHolder): JsonResponse
    {
        $account = $this->accountRetrieverService->retrieve($accountType, $accountHolder);
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Application/Controller/AccountController.php',
        ]);
    }

    #[Route('/account/{accountType}/{accountHolder}/withdraw', name: 'app_application_account_withdraw')]
    public function withdraw(string $accountType, string $accountHolder): JsonResponse
    {
        $account = $this->accountRetrieverService->retrieve($accountType, $accountHolder);
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Application/Controller/AccountController.php',
        ]);
    }

    #[Route('/account/{accountType}/{accountHolder}/buy', name: 'app_application_account_buy')]
    public function buy(string $accountType, string $accountHolder): JsonResponse
    {
        $account = $this->accountRetrieverService->retrieve($accountType, $accountHolder);
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Application/Controller/AccountController.php',
        ]);
    }
}
