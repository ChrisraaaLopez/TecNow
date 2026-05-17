{{-- Partial: tarjeta de comentario para inyección dinámica vía AJAX --}}
@php
    $isAuthor  = $comment->user_id === Auth::id();
    $isAdmin   = Auth::user()->global_role === 'admin';
@endphp

<div class="flex gap-3">
    <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-600 relative flex-shrink-0 mt-1">
        <img src="{{ asset('avatars/' . $comment->user->avatar) }}" class="w-full h-full object-cover" />
        @if($comment->user->marco)
            <img src="{{ asset('marcos/' . $comment->user->marco) }}" class="absolute inset-0 w-full h-full object-cover z-10" />
        @endif
    </div>

    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 mb-1">
            <span class="text-sm font-semibold">{{ $comment->user->name }}</span>
            <span class="text-xs text-muted-foreground">Justo ahora</span>
        </div>

        <p class="text-sm text-foreground whitespace-pre-wrap">{{ $comment->content }}</p>

        <div class="flex items-center gap-3 mt-2">
            {{-- Karma --}}
            <div class="flex items-center gap-1"
                 x-data="{
                   karma: {{ $karma }},
                   userVote: {{ $userVote ?? 'null' }},
                   loading: false,
                   async vote(value) {
                       if (this.loading) return;
                       this.loading = true;
                       try {
                           const res = await fetch('{{ route('comments.vote', $comment) }}', {
                               method: 'POST',
                               headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                               body: JSON.stringify({ vote: value }),
                           });
                           const data = await res.json();
                           this.karma = data.karma;
                           this.userVote = data.user_vote;
                       } finally { this.loading = false; }
                   }
                 }">
                <button type="button" @click="vote(1)" :disabled="loading"
                        :class="userVote === 1 ? 'text-orange-400' : 'text-gray-500 hover:text-orange-400'"
                        class="p-1 rounded transition-colors">
                    <svg class="w-3.5 h-3.5" :fill="userVote === 1 ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                </button>
                <span class="text-xs font-semibold min-w-[1.2rem] text-center"
                      :class="karma > 0 ? 'text-orange-400' : karma < 0 ? 'text-blue-400' : 'text-gray-500'"
                      x-text="karma"></span>
                <button type="button" @click="vote(-1)" :disabled="loading"
                        :class="userVote === -1 ? 'text-blue-400' : 'text-gray-500 hover:text-blue-400'"
                        class="p-1 rounded transition-colors">
                    <svg class="w-3.5 h-3.5" :fill="userVote === -1 ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>

            {{-- Eliminar --}}
            @if($isAuthor || $isAdmin)
            <form action="{{ route('comments.destroy', $comment) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit"
                        onclick="return confirm('¿Eliminar este comentario?')"
                        class="text-xs text-red-500 hover:text-red-400 transition-colors">
                    Eliminar
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
