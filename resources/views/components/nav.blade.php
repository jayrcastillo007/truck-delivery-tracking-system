<nav 
        x-data="{ open: false }"
        class="bg-gray-800 text-white fixed w-full z-20 top-0 left-0 border-gray-200 px-2 sm:px-3"
    >
        <div class="container flex flex-wrap justify-between items-center mx-auto">


            <!-- Mobile menu button -->
            <button class="md:hidden" @click="open = !open">
                <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" fill="#e3e3e3" viewBox="0 -960 960 960">
                    <path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/>
                </svg>
            </button>

            <!-- Menu -->
            <div
                :class="open ? 'block' : 'hidden'"
                class="w-full md:block md:w-auto"
                id="navbar-main">
            
                <x-items />
                
            </div>

        </div>
    </nav>