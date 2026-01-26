<div x-data="helpdeskWidget()" x-init="initWidget()" class="fixed bottom-6 right-6 z-50 flex flex-col items-end">

    <!-- Chat Window -->
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="mb-4 w-80 sm:w-96 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col h-[500px]"
        style="display: none;">

        <!-- Header -->
        <div class="bg-blue-600 p-4 text-white flex justify-between items-center">
            <div>
                <h3 class="font-semibold"
                    x-text="isVerified ? (userName ? 'Hi, ' + userName : 'Bantuan ASN') : 'Verifikasi Identitas'"></h3>
                <p class="text-xs text-blue-100">Online Bot & Admin</p>
            </div>
            <button @click="open = false" class="hover:bg-blue-700 rounded p-1 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <!-- VERIFICATION MODE -->
        <div x-show="!isVerified"
            class="flex-1 p-6 flex flex-col justify-center items-center bg-gray-50 dark:bg-gray-900">
            <div class="text-center mb-6">
                <div class="bg-blue-100 p-3 rounded-full inline-block mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                    </svg>
                </div>
                <h4 class="text-gray-800 dark:text-gray-200 font-semibold mb-2">Selamat datang!</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">Mohon masukkan NIP Anda terlebih dahulu untuk
                    memulai layanan bantuan.</p>
            </div>

            <form @submit.prevent="verifyNip" class="w-full">
                <div class="mb-4">
                    <input type="text" x-model="nipInput" placeholder="Masukkan NIP (18 digit)"
                        class="w-full px-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        :class="verificationError ? 'border-red-500 ring-1 ring-red-500' : ''">
                    <p x-show="verificationError" x-text="verificationError" class="text-red-500 text-xs mt-1 ml-1"></p>
                </div>
                <button type="submit" :disabled="isLoading || !nipInput"
                    class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors font-medium">
                    <span x-show="!isLoading">Verifikasi Identitas</span>
                    <span x-show="isLoading" class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Memeriksa...
                    </span>
                </button>
            </form>
        </div>

        <!-- CHAT MODE -->
        <div x-show="isVerified" class="flex flex-col h-full">
            <!-- Messages Area -->
            <div class="flex-1 p-4 overflow-y-auto bg-gray-50 dark:bg-gray-900 scrollbar-thin" x-ref="chatContainer">
                <template x-if="messages.length === 0">
                    <div class="text-center text-gray-500 mt-10 text-sm">
                        <p>Identitas terverifikasi.</p>
                        <p>Silakan tanyakan sesuatu.</p>
                    </div>
                </template>

                <template x-for="msg in messages" :key="msg.id || msg.tempId">
                    <div class="mb-3 flex" :class="msg.direction === 'in' ? 'justify-end' : 'justify-start'">
                        <div class="max-w-[80%] rounded-lg px-4 py-2 text-sm shadow-sm whitespace-pre-wrap"
                            :class="msg.direction === 'in' 
                                ? 'bg-blue-600 text-white rounded-br-none' 
                                : 'bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-bl-none border border-gray-200 dark:border-gray-600'">
                            <span x-text="msg.message"></span>
                        </div>
                    </div>
                </template>

                <!-- Bot Typing Indicator if loading -->
                <div x-show="isLoading" class="flex justify-start mb-3">
                    <div class="bg-gray-200 dark:bg-gray-700 rounded-lg px-4 py-2 text-sm">
                        <span class="animate-pulse">...</span>
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <form @submit.prevent="sendMessage"
                class="p-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex gap-2">
                <input type="text" x-model="newMessage" placeholder="Ketik pesan..."
                    class="flex-1 px-3 py-2 border rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <button type="submit" :disabled="!newMessage.trim() || isLoading"
                    class="bg-blue-600 text-white rounded-full p-2 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Toggle Button -->
    <button @click="open = !open" :class="open ? 'scale-0 opacity-0' : 'scale-100 opacity-100'"
        class="transition-all duration-200 ease-in-out bg-blue-600 hover:bg-blue-700 text-white rounded-full p-4 shadow-lg flex items-center justify-center focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
        </svg>
    </button>
</div>

<script>
    function helpdeskWidget() {
        return {
            open: false,
            // Verification State
            isVerified: false,
            nipInput: '',
            userName: '',
            verificationError: '',

            // Chat State
            messages: [],
            newMessage: '',
            isLoading: false,

            initWidget() {
                this.$watch('open', value => {
                    if (value) {
                        this.fetchHistory();
                        // Dont scroll yet if not verified
                    }
                });
            },

            fetchHistory() {
                axios.get('{{ route("chat.history") }}')
                    .then(response => {
                        this.isVerified = response.data.is_verified;
                        this.userName = response.data.user_name || '';

                        if (this.isVerified) {
                            this.messages = response.data.messages;
                            this.scrollToBottom();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching history:', error);
                    });
            },

            verifyNip() {
                if (!this.nipInput) return;

                this.isLoading = true;
                this.verificationError = '';

                axios.post('{{ route("chat.verify") }}', {
                    nip: this.nipInput
                })
                    .then(response => {
                        this.isVerified = true;
                        // Add success message as a bot message instantly
                        this.messages.push({
                            tempId: Date.now(),
                            message: response.data.message,
                            direction: 'out', // Bot said it
                            created_at: new Date().toISOString()
                        });
                        this.scrollToBottom();
                    })
                    .catch(error => {
                        // console.error(error);
                        if (error.response && error.response.data) {
                            this.verificationError = error.response.data.message;
                        } else {
                            this.verificationError = 'Terjadi kesalahan sistem.';
                        }
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
            },

            sendMessage() {
                if (!this.newMessage.trim()) return;

                const msgToSend = this.newMessage;
                this.newMessage = '';

                // Optimistic Update
                this.messages.push({
                    tempId: Date.now(),
                    message: msgToSend,
                    direction: 'in',
                    created_at: new Date().toISOString()
                });
                this.scrollToBottom();
                this.isLoading = true;

                axios.post('{{ route("chat.send") }}', {
                    message: msgToSend
                })
                    .then(response => {
                        if (response.data.reply) {
                            this.messages.push({
                                tempId: Date.now() + 1,
                                message: response.data.reply,
                                direction: 'out',
                                created_at: new Date().toISOString()
                            });
                            this.scrollToBottom();
                        }
                    })
                    .catch(error => {
                        console.error('Error sending message:', error);
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const container = this.$refs.chatContainer;
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                });
            }
        }
    }
</script>