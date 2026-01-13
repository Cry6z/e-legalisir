<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public string $name = '';

    public string $email = '';

    public $photo;

    public ?string $currentPhotoUrl = null;

    public bool $managesPhoto = false;

    protected array $photoManagerRoles = ['staf', 'dekan', 'superadmin'];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->currentPhotoUrl = Auth::user()->profilePhotoUrl();
        $this->managesPhoto = $this->canManagePhoto();
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
        ];

        if ($this->managesPhoto) {
            $rules['photo'] = ['nullable', 'image', 'max:2048'];
        }

        $validated = $this->validate($rules);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($this->managesPhoto && $this->photo) {
            $path = $this->photo->store('profile-photos', 'public');

            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $user->profile_photo_path = $path;
        }

        $user->save();

        $this->reset('photo');
        $this->currentPhotoUrl = $user->profilePhotoUrl();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    public function removeProfilePhoto(): void
    {
        if (! $this->managesPhoto) {
            abort(403);
        }

        $user = Auth::user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->forceFill(['profile_photo_path' => null])->save();

        $this->reset('photo');
        $this->currentPhotoUrl = null;

        $this->dispatch('profile-updated', name: $user->name);
    }

    protected function canManagePhoto(): bool
    {
        $user = Auth::user();

        return $user?->hasAnyRole($this->photoManagerRoles) ?? false;
    }
}
