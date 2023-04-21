<?php

namespace App\Application\Controller;

use App\Domain\Service\Account\AccountBalanceCheckService;
use App\Domain\Service\Account\AccountRetrieverService;
use App\Domain\Service\Share\ShareCreatorService;
use App\Domain\Service\Share\SharePurchaseService;
use App\Domain\Service\Share\ShareRetrieverService;
use App\Domain\Service\TimeCheckerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SharesController extends AbstractController
{
    public function __construct(
        private readonly TimeCheckerService $timeCheckerService,
        private readonly AccountRetrieverService $accountRetrieverService,
        private readonly ShareRetrieverService $shareRetrieverService,
        private readonly ShareCreatorService $shareCreatorService,
        private readonly SharePurchaseService $sharePurchaseService,
        private readonly AccountBalanceCheckService$accountBalanceCheckService
    ) {
    }

    #[Route('/shares', name: 'app_application_shares_index')]
    public function index(): JsonResponse
    {
        return $this->json([
            'routes' => [
                'list' => [
                    'path' => '/shares/list',
                    'method' => 'GET'
                ],
                'create' => [
                    'path' => '/shares/create',
                    'args' => ['slug', 'company', 'startingValue', 'startingPrice[optional]']
                ],
                'buy' => [
                    'path' => '/shares/buy/{slug}',
                    'args' => ['accountType[ISA|JISA]', 'accountHolder[name]']
                ]
            ]
        ]);
    }

    #[Route('/shares/list', name: 'app_application_shares_list')]
    public function list(): JsonResponse
    {
        if (!$this->timeCheckerService->canSharesBePurchased()) {
            return $this->json(['message' => 'Stock Exchange trading hours are from 8:00 to 16:30',]);
        }
        $shares = $this->shareRetrieverService->retrieveAll();

        foreach ($shares as $share) {
            $message[] = [
                'slug' => $share->getSlug(),
                'company' => $share->getCompany(),
                'price' => $share->getPrice(),
                'value' => $share->getValue(),
                'canBePurchased' => !$share->isOwned(),
                'purchaseUrl' => '/shares/buy/'.$share->getSlug(),
            ];
        }

        return $this->json(['shares' => $message ?? []]);
    }

    #[Route('/shares/create', name: 'app_application_shares_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        if (!$this->timeCheckerService->canSharesBePurchased()) {
            return $this->json(['message' => 'Stock Exchange trading hours are from 8:00 to 16:30',]);
        }
        $slug = $request->get('slug');
        $company = $request->get('company');
        $startingValue = $request->get('startingValue');
        $startingPrice = $request->get('startingPrice');

        if (!is_string($slug) || !is_string($company) || !$startingValue) {
            return $this->json(['message' => 'Please supply slug, company, and startingValue.']);
        }

        $newShare = $this->shareCreatorService->createShare($slug, $company, $startingValue, $startingPrice ?? 1.00);

        return $this->json(['message' => 'Share with slug '.$newShare->getSlug().' created.',]);
    }

    #[Route('/shares/buy/{slug}', name: 'app_application_shares_buy', methods: ['POST'])]
    public function buy(Request $request, string $slug): Response
    {
        if (!$this->timeCheckerService->canSharesBePurchased()) {
            return $this->json(['message' => 'Stock Exchange trading hours are from 8:00 to 16:30',]);
        }

        $accountHolder = $request->get('accountHolder');
        $accountType = $request->get('accountType');

        if (!is_string($accountHolder) || !is_string($accountType)) {
            return $this->json(['message' => 'Please supply accountHolder, accountType (either ISA or JISA).']);
        }

        $account = $this->accountRetrieverService->retrieve($accountType, $accountHolder);

        if (!$account) {
            return $this->json(['message' => 'Account not found.',]);
        }

        $result = $this->sharePurchaseService->purchase($account, $slug);
        if (!$result) {
            return $this->json(['message' => 'Share could not be purchased. Please contact your representative.']);
        }
        return $this->json([
            'totalBalance' => $this->accountBalanceCheckService->checkTotalBalance($account),
            'savingsBalance' => $this->accountBalanceCheckService->checkSavingsBalance($account),
            'sharesValueBalance' => $this->accountBalanceCheckService->checkSharesValueBalance($account),
        ]);
    }
}
