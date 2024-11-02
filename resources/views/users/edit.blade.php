</div>

<div>
    <label class="block text-sm font-medium text-gray-700">Adresse</label>
    <input type="text" name="adress_util" value="{{ old('adress_util', $user->adress_util) }}" 
           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700">Mot de passe</label>
    <input type="password" name="password" 
           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    <p class="text-sm text-gray-500">Laissez vide si vous ne souhaitez pas changer le mot de passe.</p>
</div>

<div class="flex justify-end gap-4">
    <a href="{{ route('users.index') }}" 
       class="bg-gray-500 text-white px-4 py-2 rounded">Annuler</a>
    <button type="submit" 
            class="bg-blue-500 text-white px-4 py-2 rounded">Mettre à jour</button>
</div>
</form>
</div>
</div>
@endsection
