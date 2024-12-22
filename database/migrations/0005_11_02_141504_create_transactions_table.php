<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('id_transaction');

            // Clé étrangère vers la table 'type_transactions'
            $table->foreignId('type_transaction_id')
                ->constrained('type_transactions', 'id_type_transa')
                ->onDelete('cascade');  // Suppression en cascade si le type de transaction est supprimé

            // Clé étrangère vers la table 'produits'
            $table->foreignId('produit_id')
                ->constrained('produits', 'id_prod')
                ->onDelete('cascade');  // Suppression en cascade si le produit est supprimé

            // Clé étrangère vers la table 'users'
            $table->foreignId('user_id')
                ->constrained('users', 'id_util')
                ->onDelete('cascade');  // Suppression en cascade si l'utilisateur est supprimé

            // Montants et soldes
            $table->decimal('montant_trans', 15, 2);  // Montant de la transaction

            // Informations supplémentaires
            $table->string('num_beneficiaire');  // Numéro du bénéficiaire
            $table->enum('statut', ['EN_COURS', 'COMPLETE', 'ANNULE'])->default('COMPLETE');  // Statut de la transaction

            // Soldes avant et après la transaction
            $table->decimal('solde_avant', 15, 2);  // Solde avant la transaction
            $table->decimal('solde_apres', 15, 2);  // Solde après la transaction

            // Soldes pour la caisse avant et après la transaction
            $table->decimal('solde_caisse_avant', 15, 2);  // Solde caisse avant la transaction
            $table->decimal('solde_caisse_apres', 15, 2);  // Solde caisse après la transaction

            // Timestamps pour les dates de création et mise à jour
            $table->timestamps();

            // Soft deletes pour la suppression logique
            $table->softDeletes();

            $table->decimal('commission_grille_tarifaire', 15, 2)->nullable();
            $table->decimal('frais_service', 15, 2)->nullable();
            $table->string('motif')->nullable();
            $table->foreignId('id_caisse')
                ->constrained('caisses', 'id_caisse')
                ->onDelete('cascade');

            // Ajout des nouveaux champs
            $table->string('numero_compteur')->nullable();
            $table->string('numero_validation')->nullable();
            $table->string('numero_carte_paiement')->nullable();
        });
    }

    public function down()
    {
        // Suppression de la table 'transactions'
        Schema::dropIfExists('transactions');
    }
};
