<?php

namespace App\Application\Controller;

use App\Domain\Entity\Interfaces\AccountInterface;
use App\Domain\Service\Account\AccountBalanceCheckService;
use App\Domain\Service\Account\AccountBalanceUpdaterService;
use App\Domain\Service\Account\AccountPersistService;
use App\Domain\Service\Account\AccountRetrieverService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


// With more time I would like to split out this controller so that it isn't quite as large
class AccountController extends AbstractController
{
    public function __construct(
        private readonly AccountRetrieverService      $accountRetrieverService,
        private readonly AccountPersistService        $accountPersistService,
        private readonly AccountBalanceCheckService   $accountBalanceCheckService,
        private readonly AccountBalanceUpdaterService $accountBalanceUpdaterService
    ) {
    }

    #[Route('/account', name: 'app_application_account_index')]
    public function index(): JsonResponse
    {
        return $this->json([
            'routes' => [
                'create-isa' => [
                    'path' => '/account/create-isa',
                    'args' => ['accountHolder[name]']
                ],
                'create-jisa' => [
                    'path' => '/account/create-jisa',
                    'args' => ['accountHolder[name]', 'accountHolderBirthday[YYYY-mm-dd]']
                ],
                'balance' => [
                    'path' => '/account/balance',
                    'args' => ['accountType[ISA|JISA]', 'accountHolder[name]']
                ],
                'deposit' => [
                    'path' => '/account/deposit',
                    'args' => ['accountType[ISA|JISA]', 'accountHolder[name]', 'amount']
                ],
                'withdraw' => [
                    'path' => '/account/withdraw',
                    'args' => ['accountType[ISA|JISA]', 'accountHolder[name]', 'amount']
                ],
                'withdraw' => [
                    'path' => '/account/shares',
                    'args' => ['accountType[ISA|JISA]', 'accountHolder[name]', 'amount']
                ]
            ]
        ]);
    }

    #[Route('/account/create-isa', name: 'app_application_account_createisa', methods: ['POST'])]
    public function createIsa(Request $request): JsonResponse
    {
        $accountHolder = $request->get('accountHolder');

        if ($accountHolder === null) {
            return $this->json(['message' => 'Please supply an accountHolder.']);
        }

        $account = $this->accountPersistService->persistIsaAccount($accountHolder);

        if ($account !== null) {
            return $this->json(['message' => 'ISA account with id '. $account->getId() . ' created.']);
        }

        return $this->json(['message' => 'Could not create account. Please contact your representative.',]);
    }

    #[Route('/account/create-jisa', name: 'app_application_account_createjisa', methods: ['POST'])]
    public function createJisa(Request $request): JsonResponse
    {
        $accountHolder = $request->get('accountHolder');
        $accountHolderBirthday = $request->get('accountHolderBirthday');

        if ($accountHolder === null || $accountHolderBirthday === null) {
            return $this->json(['message' => 'Please supply both an accountHolder and an accountHolderBirthday (format: YYYY-mm-dd).']);
        }

        $account = $this->accountPersistService->persistJisaAccount($accountHolder, $accountHolderBirthday);

        if ($account !== null) {
            return $this->json(['message' => 'Jisa account with id '. $account->getId() . ' created.',]);
        }

        return $this->json(['message' => 'Could not create account. Please contact your representative.',]);
    }

    #[Route('/account/balance', name: 'app_application_account_balance', methods: ['POST'])]
    public function balance(Request $request): JsonResponse
    {
        $account = $this->getAccount($request);
        if (is_array($account)) {
            return $this->json($account);
        }

        return $this->json([
            'totalBalance' => $this->accountBalanceCheckService->checkTotalBalance($account),
            'savingsBalance' => $this->accountBalanceCheckService->checkSavingsBalance($account),
            'sharesValueBalance' => $this->accountBalanceCheckService->checkSharesValueBalance($account),
        ]);
    }

    #[Route('/account/deposit', name: 'app_application_account_deposit', methods: ['POST'])]
    public function deposit(Request $request): JsonResponse
    {
        $account = $this->getAccount($request);
        if (is_array($account)) {
            return $this->json($account);
        }

        $amount = $request->get('amount') ?? 0;

        $result = $this->accountBalanceUpdaterService->deposit($account, $amount);

        $successfulMessage = [
            'message' => $account->getAccountHolder().', your balance is now '.$account->getBalance()
        ];

        if (is_float($result)) {
            $successfulMessage['unprocessedAmount'] = $result;
        }

        return $this->json($successfulMessage);
    }

    #[Route('/account/withdraw', name: 'app_application_account_withdraw', methods: ['POST'])]
    public function withdraw(Request $request): JsonResponse
    {
        $account = $this->getAccount($request);
        if (is_array($account)) {
            return $this->json($account);
        }

        $amount = $request->get('amount') ?? 0;
        $result = $this->accountBalanceUpdaterService->withdraw($account, $amount);
        if (!$result) {
            return $this->json([
                'message' => 'You must be 18 or over to withdraw funds from a JISA.'
            ]);
        }

        return $this->json([
            'message' => $account->getAccountHolder().', your balance is now '.$account->getBalance()
        ]);
    }

    #[Route('/account/shares', name: 'app_application_account_shares', methods: ['POST'])]
    public function shares(Request $request): JsonResponse
    {
        $account = $this->getAccount($request);
        if (is_array($account)) {
            return $this->json($account);
        }
        $sharesHeld = $this->accountBalanceCheckService->checkSharesHeld($account);
        foreach ($sharesHeld as $share) {
            $message[] = [
                'slug' => $share->getSlug(),
                'company' => $share->getCompany(),
                'value' => $share->getValue(),
            ];
        }
        return $this->json(['accountHolder' => $account->getAccountHolder(),'shares' => $message ?? []]);
    }

    // This is mostly being used as a shortcut for returning the same json messages for the balance related functions
    private function getAccount(Request $request): AccountInterface|array
    {
        $accountHolder = $request->get('accountHolder');
        $accountType = $request->get('accountType');

        if (!is_string($accountHolder) || !is_string($accountType)) {
            return ['message' => 'Please supply both an accountHolder and an accountType (either ISA or JISA).'];
        }

        $account = $this->accountRetrieverService->retrieve(strtoupper($accountType), $accountHolder);

        return $account ?? ['message' => 'Account not found.',];
    }
}
