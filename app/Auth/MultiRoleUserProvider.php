<?php

namespace App\Auth;

use App\Models\Admin;
use App\Models\Pembimbing;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Pimpinan;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Model;

class MultiRoleUserProvider implements UserProvider
{
    public function __construct(
        protected Hasher $hasher
    ) {}

    /**
     * Parse identifier "siswa_5" menjadi ['siswa', 5].
     */
    protected function parseIdentifier(string $identifier): ?array
    {
        foreach (['siswa_', 'guru_', 'pembimbing_', 'admin_', 'pimpinan_'] as $prefix) {
            if (str_starts_with($identifier, $prefix)) {
                $id = substr($identifier, strlen($prefix));
                $role = rtrim($prefix, '_');
                return [$role === 'siswa' ? 'siswa' : $role, $id];
            }
        }
        return null;
    }

    protected function modelForRole(string $role): Siswa|Guru|Pembimbing|Admin|Pimpinan
    {
        return match ($role) {
            'siswa' => new Siswa,
            'guru' => new Guru,
            'pembimbing' => new Pembimbing,
            'admin' => new Admin,
            'pimpinan' => new Pimpinan,
            default => throw new \InvalidArgumentException("Unknown role: {$role}"),
        };
    }

    public function retrieveById($identifier): ?UserContract
    {
        $parsed = $this->parseIdentifier((string) $identifier);
        if (!$parsed) {
            return null;
        }
        [$role, $id] = $parsed;
        $model = $this->modelForRole($role);
        return $model->newQuery()->find($id);
    }

    public function retrieveByToken($identifier, #[\SensitiveParameter] $token): ?UserContract
    {
        $user = $this->retrieveById($identifier);
        if (!$user) {
            return null;
        }
        $rememberToken = $user->getRememberToken();
        return $rememberToken && hash_equals($rememberToken, $token) ? $user : null;
    }

    public function updateRememberToken(UserContract $user, #[\SensitiveParameter] $token): void
    {
        $user->setRememberToken($token);
        if ($user instanceof Model) {
            $timestamps = $user->timestamps;
            $user->timestamps = false;
            $user->save();
            $user->timestamps = $timestamps;
        }
    }

    public function retrieveByCredentials(#[\SensitiveParameter] array $credentials): ?UserContract
    {
        $email = $credentials['email'] ?? null;
        if (!$email) {
            return null;
        }

        $models = [\App\Models\Siswa::class, \App\Models\Guru::class, \App\Models\Pembimbing::class, \App\Models\Admin::class, \App\Models\Pimpinan::class];
        foreach ($models as $modelClass) {
            $user = $modelClass::where('email', $email)->first();
            if ($user) {
                return $user;
            }
        }
        return null;
    }

    public function validateCredentials(UserContract $user, #[\SensitiveParameter] array $credentials): bool
    {
        $plain = $credentials['password'] ?? '';
        return $this->hasher->check($plain, $user->getAuthPassword());
    }

    public function rehashPasswordIfRequired(UserContract $user, #[\SensitiveParameter] array $credentials, bool $force = false): void
    {
        if (!$force && !$this->hasher->needsRehash($user->getAuthPassword())) {
            return;
        }
        if ($user instanceof Model) {
            $user->setAttribute('password', $credentials['password']);
            $user->save();
        }
    }
}
