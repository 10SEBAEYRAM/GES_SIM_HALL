<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    // Affiche la liste des utilisateurs
    public function index()
    {
        $users = User::paginate(10);
      
        return view('users.index', compact('users'));
    }

    // Affiche le formulaire de création d'utilisateur
    public function create()
    {
        return view('users.create');
    }

    // Gère l'insertion des utilisateurs dans la base de données
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_utili' => 'required|string|max:50',
            'prenom_utili' => 'required|string|max:50',
            'email_utili' => 'required|string|email|max:50|unique:users,email_utili', 
            'num_utili' => 'required|string|max:50',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'nom_utili' => $validated['nom_utili'],
            'prenom_utili' => $validated['prenom_utili'],
            'email_utili' => $validated['email_utili'],
            'num_utili' => $validated['num_utili'],
            'password' => Hash::make($validated['password']),
        ]);

        Session::flash('success', 'L\'utilisateur a été ajouté avec succès.');
        return redirect()->route('users.index')->with('success', 'Utilisateur ajouté avec succès.');
    }

    // Affiche le formulaire de modification d'un utilisateur
    public function edit($id)
    {
        $user = User::findOrFail($id); 
        return view('users.edit', compact('user')); 
    }

    // Met à jour les informations d'un utilisateur
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nom_utili' => 'required|string|max:50',
            'prenom_utili' => 'required|string|max:50',
            'email_utili' => 'required|string|email|max:50|unique:users,email_utili,' . $id, 
            'num_utili' => 'required|string|max:50',
            'password' => 'nullable|string|min:8', 
        ]);

        $user = User::findOrFail($id); 

        
        $user->nom_utili = $validated['nom_utili'];
        $user->prenom_utili = $validated['prenom_utili'];
        $user->email_utili = $validated['email_utili'];
        $user->num_utili = $validated['num_utili'];

        
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save(); 

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    
    public function destroy($id)
    {
        $user = User::findOrFail($id); 
        $user->delete(); 

        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }
}
