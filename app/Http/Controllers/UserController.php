<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TypeUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs
     */
    public function index()
    {
        $users = User::paginate(10);
        $typeUsers = TypeUser::all();
        $totalUsers = User::count();
        return view('users.index', compact('users', 'typeUsers', 'totalUsers'));
    }

    /**
     * Affiche le formulaire de création d'utilisateur
     */
    public function create()
    {
        if (!auth()->user()->can('create-users')) {
            return redirect()->route('users.index')
                ->with('error', 'Vous n\'êtes pas autorisé à créer un utilisateur.');
        }
        $typeUsers = TypeUser::all();
        return view('users.create', compact('typeUsers'));
    }

    /**
     * Gère l'insertion des utilisateurs dans la base de données
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'nom_util' => 'required|string|max:50',
            'prenom_util' => 'required|string|max:50',
            'email_util' => 'required|string|email|max:50|unique:users,email_util',
            'num_util' => 'required|string|max:50',
            'adress_util' => 'nullable|string|max:255',
            'password' => 'required|string|min:4',
            'type_users_id' => 'required|exists:type_users,id_type_users',
        ]);

        try {

            $user = User::create([
                'nom_util' => $validated['nom_util'],
                'prenom_util' => $validated['prenom_util'],
                'email_util' => $validated['email_util'],
                'num_util' => $validated['num_util'],
                'adress_util' => $validated['adress_util'],
                'password' => Hash::make($validated['password']),
                'type_users_id' => $validated['type_users_id'],
            ]);

            $user->assignRole('Administrateur', get_class($user));


            return redirect()
                ->route('users.index')
                ->with('success', 'L\'utilisateur a été ajouté avec succès.');
        } catch (\Exception $e) {
            report($e);
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de l\'utilisateur : ' . $e->getMessage());
        }
    }

    /**
     * Affiche le formulaire de modification d'utilisateur
     */
    public function edit($id)
    {
        if (!auth()->user()->can('create-users')) {
            return redirect()->route('users.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier un utilisateur.');
        }
        $user = User::where('id_util', $id)->firstOrFail();
        $typeUsers = TypeUser::all();
        return view('users.edit', compact('user', 'typeUsers'));
    }

    /**
     * Met à jour les informations d'un utilisateur
     */
    public function update(Request $request, $id)
    {
        $user = User::where('id_util', $id)->firstOrFail();

        $validated = $request->validate([
            'nom_util' => 'required|string|max:50',
            'prenom_util' => 'required|string|max:50',
            'email_util' => 'required|string|email|max:50|unique:users,email_util,' . $user->id_util . ',id_util',
            'num_util' => 'required|string|max:50',
            'adress_util' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:4',
            'type_users_id' => 'required|exists:type_users,id_type_users',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        try {
            $user->update($validated);

            return redirect()
                ->route('users.index')
                ->with('success', 'L\'utilisateur a été modifié avec succès.');
        } catch (\Exception $e) {
            report($e);
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
        if (!auth()->user()->can('delete-users')) {
            return redirect()->route('users.index')
                ->with('error', 'Vous n\'êtes pas autorisé à supprimer un utilisateur.');
        }
        try {
            $user = User::where('id_util', $id)->firstOrFail();
            $user->delete();

            return redirect()
                ->route('users.index')
                ->with('success', 'Utilisateur supprimé avec succès.');
        } catch (\Exception $e) {
            report($e);
            return redirect()
                ->route('users.index')
                ->with('error', 'Une erreur est survenue lors de la suppression de l\'utilisateur.');
        }
    }
}
