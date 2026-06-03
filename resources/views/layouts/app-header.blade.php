<header
    class="sticky top-0 flex w-full bg-[#F4F8FC] border-gray-200 z-99999 dark:border-gray-800 dark:bg-gray-900 xl:border-b"
    x-data="{
        isApplicationMenuOpen: false,
        toggleApplicationMenu() {
            this.isApplicationMenuOpen = !this.isApplicationMenuOpen;
        }
    }">
    <div class="flex flex-col items-center justify-between grow xl:flex-row xl:px-6">
        
        <div class="flex items-center justify-between w-full gap-2 px-3 py-3 border-b border-gray-200 dark:border-gray-800 sm:gap-4 xl:justify-normal xl:border-b-0 xl:px-0 lg:py-4">

            <button
                class="hidden xl:flex items-center justify-center w-10 h-10 text-[#1E55AA] border border-[#1E55AA]/20 rounded-lg dark:border-gray-800 dark:text-gray-400 lg:h-11 lg:w-11 transition-colors hover:bg-white"
                :class="{ 'bg-white shadow-sm': !$store.sidebar.isExpanded }"
                @click="$store.sidebar.toggleExpanded()" aria-label="Toggle Sidebar">
                <svg x-show="!$store.sidebar.isMobileOpen" width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.583252 1C0.583252 0.585788 0.919038 0.25 1.33325 0.25H14.6666C15.0808 0.25 15.4166 0.585786 15.4166 1C15.4166 1.41421 15.0808 1.75 14.6666 1.75L1.33325 1.75C0.919038 1.75 0.583252 1.41422 0.583252 1ZM0.583252 11C0.583252 10.5858 0.919038 10.25 1.33325 10.25L14.6666 10.25C15.0808 10.25 15.4166 10.5858 15.4166 11C15.4166 11.4142 15.0808 11.75 14.6666 11.75L1.33325 11.75C0.919038 11.75 0.583252 11.4142 0.583252 11ZM1.33325 5.25C0.919038 5.25 0.583252 5.58579 0.583252 6C0.583252 6.41421 0.919038 6.75 1.33325 6.75L7.99992 6.75C8.41413 6.75 8.74992 6.41421 8.74992 6C8.74992 5.58579 8.41413 5.25 7.99992 5.25L1.33325 5.25Z" fill="currentColor"></path>
                </svg>
                <svg x-show="$store.sidebar.isMobileOpen" class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6.21967 7.28131C5.92678 6.98841 5.92678 6.51354 6.21967 6.22065C6.51256 5.92775 6.98744 5.92775 7.28033 6.22065L11.999 10.9393L16.7176 6.22078C17.0105 5.92789 17.4854 5.92788 17.7782 6.22078C18.0711 6.51367 18.0711 6.98855 17.7782 7.28144L13.0597 12L17.7782 16.7186C18.0711 17.0115 18.0711 17.4863 17.7782 17.7792C17.4854 18.0721 17.0105 18.0721 16.7176 17.7792L11.999 13.0607L7.28033 17.7794C6.98744 18.0722 6.51256 18.0722 6.21967 17.7794C5.92678 17.4865 5.92678 17.0116 6.21967 16.7187L10.9384 12L6.21967 7.28131Z" fill="" />
                </svg>
            </button>

            <button class="flex xl:hidden items-center justify-center w-10 h-10 text-[#1E55AA] rounded-lg dark:text-gray-400 lg:h-11 lg:w-11"
                :class="{ 'bg-white shadow-sm': $store.sidebar.isMobileOpen }"
                @click="$store.sidebar.toggleMobileOpen()" aria-label="Toggle Mobile Menu">
                <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.583252 1H15.4166V1.75H0.583252V1ZM0.583252 11H15.4166V11.75H0.583252V11ZM1.33325 5.25H7.99992V6.75H1.33325V5.25Z" fill="currentColor"></path>
                </svg>
            </button>

            <a href="/" class="xl:hidden">
                <img class="h-8 w-auto dark:hidden" src="{{ asset('images/logo/logo1.png') }}" alt="BunnyKlin" />
            </a>

            <div class="hidden xl:block">
                <form>
                    <div class="relative group">
                        <span class="absolute -translate-y-1/2 pointer-events-none left-4 top-1/2 text-[#1E55AA]/40 group-focus-within:text-[#1E55AA]">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="9" r="6"></circle>
                                <line x1="19" y1="19" x2="13.5" y2="13.5"></line>
                            </svg>
                        </span>
                        <input type="text" placeholder="Buscar..."
                            class="h-11 w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-12 pr-14 text-sm text-[#1E55AA] shadow-sm placeholder:text-gray-400 focus:border-[#1E55AA] focus:outline-none focus:ring-4 focus:ring-[#1E55AA]/5 xl:w-[430px] transition-all" />
                        <button class="absolute right-2.5 top-1/2 inline-flex -translate-y-1/2 items-center gap-0.5 rounded-lg border border-gray-200 bg-gray-50 px-[7px] py-[4.5px] text-xs font-bold text-gray-400">
                            <span> ⌘ K </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div :class="isApplicationMenuOpen ? 'flex' : 'hidden'" class="items-center justify-between w-full gap-4 px-5 py-4 xl:flex xl:justify-end xl:px-0 xl:shadow-none">
            
            <div class="flex items-center gap-4 ml-auto">
                
                <button
                    class="relative flex items-center justify-center text-[#1E55AA] bg-white border border-gray-200 rounded-full hover:border-[#1E55AA] h-11 w-11 transition-all shadow-sm"
                    @click="$store.theme.toggle()">
                    <svg class="hidden dark:block" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                    </svg>
                    <svg class="dark:hidden" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                </button>

                <div class="relative" x-data="{ dropdownOpen: false }" @click.outside="dropdownOpen = false">
                    
                    <a class="flex items-center gap-4 cursor-pointer" @click.prevent="dropdownOpen = !dropdownOpen">
                        <span class="hidden text-right lg:block">
                            <span class="block text-sm font-bold text-[#1E55AA] dark:text-white">
                                {{ Auth::user()->name ?? 'Administrador' }}
                            </span>
                            <span class="block text-xs font-medium text-gray-500">
                                {{ ucfirst(Auth::user()->role ?? 'Admin') }}
                            </span>
                        </span>

                        <span class="h-11 w-11 rounded-full bg-[#1E55AA] flex items-center justify-center text-white font-black text-lg shadow-sm">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </span>

                        <svg :class="dropdownOpen ? 'rotate-180' : ''" class="hidden fill-current sm:block transition-transform duration-200 text-[#1E55AA] dark:text-white" width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.410765 0.910734C0.736202 0.585297 1.26384 0.585297 1.58928 0.910734L6.00002 5.32148L10.4108 0.910734C10.7362 0.585297 11.2638 0.585297 11.5893 0.910734C11.9147 1.23617 11.9147 1.76381 11.5893 2.08924L6.58928 7.08924C6.26384 7.41468 5.7362 7.41468 5.41077 7.08924L0.410765 2.08924C0.0853277 1.76381 0.0853277 1.23617 0.410765 0.910734Z" fill="currentColor" />
                        </svg>
                    </a>

                    <div x-show="dropdownOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute right-0 mt-4 flex w-56 flex-col rounded-2xl border border-gray-100 bg-white shadow-xl dark:border-strokedark dark:bg-boxdark z-99999" 
                         style="display: none;">
                        
                        <ul class="flex flex-col gap-2 border-b border-gray-100 px-4 py-4 dark:border-strokedark">
                            <li>
                                <a href="{{ route('profile') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-bold text-gray-600 duration-300 ease-in-out hover:bg-gray-50 hover:text-[#1E55AA] dark:text-gray-300 dark:hover:bg-meta-4 dark:hover:text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Mi Perfil
                                </a>
                            </li>
                        </ul>
                        
                        <div class="px-4 py-4">
                            <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-bold text-red-500 duration-300 ease-in-out hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-500/10 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</header>