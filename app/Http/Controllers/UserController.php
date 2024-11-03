<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller 
{
    /**
     * Affiche la liste des utilisateurs
     */
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Affiche le formulaire de création d'utilisateur
     */
    public function create()
    {
        // Plus besoin de passer $typeUsers à la vue
        return view('users.create');
    }

    /**
     * Gère l'insertion des utilisateurs dans la base de données
     */
    public function store(Request $request)
    {
       
        try {
            $validated = $request->validate([
                'nom_utili' => 'required|string|max:50',
                'prenom_utili' => 'required|string|max:50',
                'email_utili' => 'required|string|email|max:50|unique:users,email_utili',
                'num_utili' => 'required|string|max:50',
                'adress_utili' => 'nullable|string|max:255',
            ]);
           

            User::create([
                'nom_utili' => $validated['nom_utili'],
                'prenom_utili' => $validated['prenom_utili'],
                'email_utili' => $validated['email_utili'],
                'num_utili' => $validated['num_utili'],
                'adress_utili' => $validated['adress_utili'],
                'password' => Hash::make($validated['password']),
            ]);

            return redirect()
                ->route('users.index')
                ->with('success', 'L\'utilisateur a été ajouté avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de l\'utilisateur.');
        }
    }

    /**
     * Met à jour les informations d'un utilisateur
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'nom_utili' => 'required|string|max:50',
                'prenom_utili' => 'required|string|max:50',
                'email_utili' => 'required|string|email|max:50|unique:users,email_utili,'.$id,
                'num_utili' => 'required|string|max:50',
                'adress_utili' => 'nullable|string|max:255',
            ]);

            $user = User::findOrFail($id);
            
            $user->update([
                'nom_utili' => $validated['nom_utili'],
                'prenom_utili' => $validated['prenom_utili'],
                'email_utili' => $validated['email_utili'],
                'num_utili' => $validated['num_utili'],
                'adress_utili' => $validated['adress_utili'],
            ]);

            return redirect()
                ->route('users.index')
                ->with('success', 'Utilisateur mis à jour avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de l\'utilisateur.');
        }
    }

    /**
     * Supprime un utilisateur
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return redirect()
                ->route('users.index')
                ->with('success', 'Utilisateur supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Une erreur est survenue lors de la suppression de l\'utilisateur.');
        }
    }
}