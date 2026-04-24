<?php

namespace App\Http\Controllers;

use App\Models\JournalActivite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $mois  = (int) $request->input('mois', now()->month);
        $annee = (int) $request->input('annee', now()->year);

        $users = User::withCount(['ventes', 'depenses'])
            ->withSum([
                'ventes as ventes_mois_montant' => fn($q) => $q
                    ->where('statut_paiement', 'paye')
                    ->whereMonth('date_vente', $mois)
                    ->whereYear('date_vente', $annee),
            ], 'montant')
            ->withCount([
                'ventes as ventes_mois_count' => fn($q) => $q
                    ->whereMonth('date_vente', $mois)
                    ->whereYear('date_vente', $annee),
            ])
            ->withSum([
                'depenses as depenses_mois_montant' => fn($q) => $q
                    ->whereMonth('date_depense', $mois)
                    ->whereYear('date_depense', $annee),
            ], 'montant')
            ->withCount([
                'depenses as depenses_mois_count' => fn($q) => $q
                    ->whereMonth('date_depense', $mois)
                    ->whereYear('date_depense', $annee),
            ])
            ->latest()
            ->get();

        $moisLabel = \Carbon\Carbon::create($annee, $mois, 1)->translatedFormat('F Y');

        return view('users.index', compact('users', 'mois', 'annee', 'moisLabel'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'role'     => ['required', 'in:admin,employe'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'name.required'      => 'Le nom est obligatoire.',
            'email.required'     => 'L\'email est obligatoire.',
            'email.unique'       => 'Cet email est déjà utilisé.',
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'La confirmation ne correspond pas.',
            'password.min'       => 'Le mot de passe doit faire au moins 8 caractères.',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $user->setRole($data['role']);

        JournalActivite::enregistrer('autre', "Nouvel utilisateur créé : {$user->name} ({$user->role})", [
            'user_id' => $user->id,
        ]);

        return redirect()->route('users.index')
            ->with('success', "Utilisateur « {$user->name} » créé avec succès.");
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'role'  => ['required', 'in:admin,employe'],
        ], [
            'name.required'  => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.unique'   => 'Cet email est déjà utilisé.',
        ]);

        // Empêcher de se retirer son propre rôle admin
        if ($user->id === auth()->id() && $data['role'] !== 'admin') {
            return back()->withErrors(['role' => 'Vous ne pouvez pas retirer votre propre rôle admin.']);
        }

        $user->update(['name' => $data['name'], 'email' => $data['email']]);
        $user->setRole($data['role']);

        JournalActivite::enregistrer('autre', "Utilisateur modifié : {$user->name}", ['user_id' => $user->id]);

        return redirect()->route('users.index')
            ->with('success', "Utilisateur « {$user->name} » modifié.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'La confirmation ne correspond pas.',
        ]);

        $user->update(['password' => Hash::make($data['password'])]);

        JournalActivite::enregistrer('autre', "Mot de passe réinitialisé pour : {$user->name}", ['user_id' => $user->id]);

        return redirect()->route('users.index')
            ->with('success', "Mot de passe de « {$user->name} » réinitialisé.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $nom = $user->name;
        $user->delete();

        JournalActivite::enregistrer('autre', "Utilisateur supprimé : {$nom}");

        return redirect()->route('users.index')
            ->with('success', "Utilisateur « {$nom} » supprimé.");
    }
}
