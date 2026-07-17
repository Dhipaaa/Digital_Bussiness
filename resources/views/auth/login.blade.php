<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-100 flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
        <h1 class="text-2xl font-semibold text-slate-900 mb-6 text-center">Masuk ke Akun Anda</h1>
        <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-5">
            @csrf
            @if ($errors->any())
            <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 text-rose-700 text-sm">
                <p class="font-semibold mb-2">Data login tidak valid:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                    class="w-full rounded-xl border px-4 py-3 text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('email') border-rose-500 focus:border-rose-500 focus:ring-rose-200 @enderror" />
                @error('email')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required
                    class="w-full rounded-xl border px-4 py-3 text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('password') border-rose-500 focus:border-rose-500 focus:ring-rose-200 @enderror" />
                @error('password')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="w-full rounded-xl bg-indigo-600 text-white py-3 text-sm font-semibold hover:bg-indigo-700 transition-colors">Masuk</button>
        </form>
    </div>
</body>

</html>