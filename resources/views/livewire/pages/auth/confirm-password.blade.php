<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $password = '';

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (
            !Auth::guard('web')->validate([
                'email' => Auth::user()->email,
                'password' => $this->password,
            ])
        ) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>
<!-- Social Media Links -->
@push('body_end')
    <div class="fixed bottom-6 left-6">
        <ul class="flex items-center gap-4">
            <li>
                <a href="https://www.facebook.com/AOFATA" target="_blank" title="ATAAOF Facebook"
                    class="block rounded-full bg-gradient-to-tr from-blue-600 via-blue-400 to-blue-300 p-1 shadow-lg hover:scale-110 transition-transform duration-200">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/51/Facebook_f_logo_%282019%29.svg"
                        class="w-6 h-6 rounded-full">
                </a>
            </li>
            <li>
                <a href="https://twitter.com/ATA_AOF" target="_blank" title="ATAAOF Twitter"
                    class="block rounded-full bg-gradient-to-tr from-gray-900 via-gray-700 to-gray-400 p-1 shadow-lg hover:scale-110 transition-transform duration-200">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/6/6f/Logo_of_Twitter.svg"
                        class="w-6 h-6 rounded-full">
                </a>
            </li>
            <li>
                <a href="https://www.instagram.com/aof.atauni/?hl=tr" target="_blank" title="ATAAOF Instagram"
                    class="block rounded-full bg-gradient-to-tr from-pink-500 via-red-500 to-yellow-500 p-1 shadow-lg hover:scale-110 transition-transform duration-200">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/a5/Instagram_icon.png"
                        class="w-6 h-6 rounded-full">
                </a>
            </li>
            <li>
                <a href="https://www.youtube.com/channel/UCL_d3t-f6FdTNEyPiP5_Asw" target="_blank" title="ATAAOF Youtube"
                    class="block rounded-full bg-gradient-to-tr from-red-600 via-red-400 to-white p-1 shadow-lg hover:scale-110 transition-transform duration-200">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/4/42/YouTube_icon_%282013-2017%29.png"
                        class="w-6 h-6 rounded-full">
                </a>
            </li>
            <li>
                <a href="https://t.me/AtaAofResmi" target="_blank" title="ATAAOF Telegram"
                    class="block rounded-full bg-gradient-to-tr from-blue-400 via-blue-300 to-white p-1 shadow-lg hover:scale-110 transition-transform duration-200">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/8/82/Telegram_logo.svg"
                        class="w-6 h-6 rounded-full">
                </a>
            </li>
            <li>
                <a href="https://bip.ai/join/ataaof" target="_blank" title="ATAAOF Bip"
                    class="block rounded-full bg-gradient-to-tr from-cyan-500 via-blue-400 to-white p-1 shadow-lg hover:scale-110 transition-transform duration-200">
                    <img src="https://bip.ai/share/assets2/images/bip-circle.png" class="w-6 h-6 rounded-full">
                </a>
            </li>
            <li>
                <a href="https://whatsapp.com/channel/0029VaqYii1DzgT5LSP72N2k" target="_blank" title="ATAAOF Whatsapp"
                    class="block rounded-full bg-gradient-to-tr from-green-500 via-green-400 to-white p-1 shadow-lg hover:scale-110 transition-transform duration-200">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg"
                        class="w-6 h-6 rounded-full">
                </a>
            </li>
        </ul>
        <div class="mt-2">
            <small class="text-xs text-gray-500">© Atatürk Üniversitesi Açık ve Uzaktan Öğretim Fakültesi</small>
        </div>
    </div>
@endpush
<div>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form wire:submit="confirmPassword">
        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" name="password"
                required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</div>
