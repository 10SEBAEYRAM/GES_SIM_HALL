<?php 

namespace App\Services;

use App\Models\Transaction;
use App\Models\Produit;
use App\Models\Caisse;
use App\Models\JournalBalance;
use App\Models\JournalCaisse;
use App\Exceptions\InsufficientBalanceException;
use Illuminate\Support\Facades\DB;

class TransactionService 
{
    public function createTransaction(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Récupérer le produit
            $produit = Produit::findOrFail($data['produit_id']);
            
            // Vérifier le solde disponible
            if ($data['type_operation'] === 'RETRAIT' && $produit->balance < $data['montant_trans']) {
                throw new InsufficientBalanceException("Solde insuffisant pour ce produit");
            }
            
            // Calculer la commission
            $commission = $this->calculateCommission($data['montant_trans'], $produit);
            
            // Créer la transaction
            $transaction = Transaction::create([
                'type_transaction_id' => $data['type_transaction_id'],
                'produit_id' => $data['produit_id'],
                'user_id' => auth()->id(),
                'etat_trans' => 'ACTIF',
                'montant_trans' => $data['montant_trans'],
                'commission_appliquee' => $commission,
                'num_beneficiaire' => $data['num_beneficiaire'],
                'date_trans' => now(),
                'type_operation' => $data['type_operation'],
                'statut' => 'COMPLETE'
            ]);
            
            // Mettre à jour la balance du produit
            $this->updateProductBalance($produit, $data['montant_trans'], $data['type_operation']);
            
            // Mettre à jour la caisse
            $this->updateCaisse($data['montant_trans'], $commission, $data['type_operation']);
            
            // Enregistrer dans le journal de balance
            $this->logBalanceJournal($produit, $transaction);
            
            // Enregistrer dans le journal de caisse
            $this->logCaisseJournal($transaction);
            
            return $transaction;
        });
    }
    
    private function calculateCommission(float $montant, Produit $produit): float
    {
        // Logique de calcul de commission selon les règles de l'entreprise
        $tauxCommission = $produit->taux_commission ?? 0.02; // 2% par défaut
        return $montant * $tauxCommission;
    }
    
    private function updateProductBalance(Produit $produit, float $montant, string $typeOperation)
    {
        if ($typeOperation === 'DEPOT') {
            $produit->balance += $montant;
        } else {
            $produit->balance -= $montant;
        }
        $produit->save();
    }
    
    private function updateCaisse(float $montant, float $commission, string $typeOperation)
    {
        $caisse = Caisse::first();
        
        if ($typeOperation === 'DEPOT') {
            $caisse->solde += $montant + $commission;
        } else {
            $caisse->solde -= $montant;
            $caisse->solde += $commission;
        }
        
        $caisse->save();
    }
    
    private function logBalanceJournal(Produit $produit, Transaction $transaction)
    {
        JournalBalance::create([
            'produit_id' => $produit->id,
            'transaction_id' => $transaction->id,
            'ancien_solde' => $produit->balance - $transaction->montant_trans,
            'nouveau_solde' => $produit->balance,
            'date_operation' => now(),
            'user_id' => auth()->id()
        ]);
    }
    
    private function logCaisseJournal(Transaction $transaction)
    {
        JournalCaisse::create([
            'transaction_id' => $transaction->id,
            'montant' => $transaction->montant_trans,
            'commission' => $transaction->commission_appliquee,
            'type_operation' => $transaction->type_operation,
            'date_operation' => now(),
            'user_id' => auth()->id()
        ]);
    }
}