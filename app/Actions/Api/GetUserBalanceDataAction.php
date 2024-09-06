<?php

namespace App\Actions\Api;

use App\Http\ExternalApiHelpers\GetUserBalance;
use App\Models\User;

class GetUserBalanceDataAction
{
    public function handle(User $user): void
    {
        $user->load('balance', 'balance.balanceItems', 'balance.balanceItems.itemDocuments');
        if ($user->userCard->trading_name) {
            $apiHelper = new GetUserBalance();
            $response = $apiHelper->sendData(['ClientDesc' => $user->userCard->trading_name]);
            if ($response && is_array($response)) {
                $this->deleteOldUserBalance($user);
                foreach ($response as $mainEntrance) {
                    $company = $mainEntrance['Entity'];
                    $closingBalance = $mainEntrance['AmountTurnover'];
                    $documents = $mainEntrance['Docs'];
                    if (is_array($documents) && count($documents)) {

                        $userBalance = $user->balance()->create([
                            'entity_name' => $company,
                            'closing_balance' => $closingBalance,
                            'balance_items_count' => count($documents),
                        ]);

                        foreach($documents as $document) {
                           $documentName = $document['Doc'];
                           $amount = $document['AmountTurnover'];
                           $balanceItems = $document['TypesOfSum'];

                            $userBalanceItem = $userBalance->balanceItems()->create([
                                'document_name' => $documentName,
                                'closing_balance' => $amount,
                            ]);

                           if (is_array($balanceItems) && count($balanceItems)) {
                               foreach ($balanceItems as $balanceItem) {
                                   $paymentType = $balanceItem['TypeOfSum'];
                                   $payment = $balanceItem['AmountTurnover'];

                                   $userBalanceItem->itemDocuments()->create([
                                       'document_description' => $paymentType,
                                       'amount' => $payment,
                                   ]);
                               }
                           }
                        }
                    }
                }
            }
        }
    }

    private function deleteOldUserBalance(User $user): void
    {
        $user->balance()->delete();
    }
}
